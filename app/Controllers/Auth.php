<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new \App\Models\CourseModel();
    }
    /**
     * Handle login for GET (form) and POST (authenticate) requests.
     * Uses session to persist `isLoggedIn`, `userId`, and normalized `userRole`.
     */

    public function login()
    {
        $session = session();
        if ($this->request->getMethod(true) === 'POST') {
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');

            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();
            if ($user && password_verify($password, $user['password'])) {
                if ((int) ($user['active'] ?? 1) !== 1) {
                    return redirect()->back()->with('login_error', 'Your account has been deactivated. Please contact an administrator.');
                }
                // Normalize role values to a supported set to avoid drift like "instructor"
                $rawRole = (string) ($user['role'] ?? 'student');
                $normalizedRole = $rawRole;
                if ($rawRole === 'instructor') {
                    $normalizedRole = 'teacher';
                }
                if (! in_array($normalizedRole, ['admin', 'teacher', 'student'], true)) {
                    $normalizedRole = 'student';
                }
                $session->regenerate();
                $session->set([
                    'isLoggedIn' => true,
                    'userId' => $user['id'] ?? null,
                    'userEmail' => $user['email'],
                    'userName' => $user['name'] ?? null,
                    'userRole' => $normalizedRole,
                ]);

                return redirect()->to(site_url('/dashboard'));
            }

            return redirect()->back()->with('login_error', 'Invalid credentials');
        }

        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login');
    }

    /**
     * Destroy session and redirect to login.
     */
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }

    /**
     * Handle registration for GET (form) and POST (create account) requests.
     * Validates required fields, email format, and password confirmation.
     */
    public function register()
    {
        $session = session();
        if ($this->request->getMethod(true) === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');
            $passwordConfirm = (string) $this->request->getPost('password_confirm');

            if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
            }

            if (! preg_match('/^[A-Za-z\s]+$/', $name)) {
                return redirect()->back()->withInput()->with('register_error', 'Name may only contain letters and spaces.');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
            }

            if (! preg_match('/^[A-Za-z0-9._@-]+$/', $email)) {
                return redirect()->back()->withInput()->with('register_error', 'Email contains invalid characters.');
            }

            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
            }

            $userModel = new \App\Models\UserModel();

            if ($userModel->where('email', $email)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $userId = $userModel->insert([
                'name' => $name,
                'email' => $email,
                'role' => 'student',
                'password' => $passwordHash,
            ], true);

            if (! $userId) {
                return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
            }

            return redirect()
                ->to(base_url('login'))
                ->with('register_success', 'Account created successfully. Please log in.');
        }

        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/register');
    }

    /**
     * Render role-aware dashboard.
     * - Admin: loads counts.
     * - Student: preloads available courses for AJAX section.
     * Guards access for unauthenticated users.
     */
    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role === 'instructor') {
            $role = 'teacher';
        }
        if (! in_array($role, ['admin', 'teacher', 'student'], true)) {
            $role = 'student';
        }
        $data = [
            'title' => 'Dashboard',
            'role' => $role,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
            // Placeholder role-specific data
            'stats' => [
                'admin' => [ 'usersTotal' => null, 'coursesTotal' => null ],
                'teacher' => [ 'myCourses' => [], 'pendingSubmissions' => 0 ],
                'student' => [ 'enrolledCourses' => [], 'notifications' => 0 ],
            ],
        ];

        // Role-based data loading (best-effort; dashboard remains functional if queries fail)
        try {
            if ($role === 'admin') {
                $userModel = new \App\Models\UserModel();
                $data['stats']['admin']['usersTotal'] = $userModel->countAllResults();
                $data['stats']['admin']['coursesTotal'] = $this->courseModel->countAllResults();
                // Load all courses for admin dashboard search
                $data['courses'] = $this->courseModel->getAllCourses();
            }

            if ($role === 'student') {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $materialModel = new \App\Models\MaterialModel();
                $userId = (int) $session->get('userId');
                $data['available_courses'] = $enrollmentModel->getAvailableCourses($userId);
                $enrollments = $enrollmentModel->getUserEnrollments($userId);

                $materials = [];
                foreach ($enrollments as $enrollment) {
                    $courseMaterials = $materialModel->getMaterialsByCourse($enrollment['course_id']);
                    $materials = array_merge($materials, $courseMaterials);
                }
                $data['enrollments'] = $enrollments;
                $data['materials'] = $materials;
            }
        } catch (\Throwable $e) {
            // Silently continue to keep dashboard functional without DB extras
        }

        // Render unified dashboard with role-conditional content
        return view('auth/dashboard', $data);
    }

    /**
     * Allow logged-in users to update their own email and password.
     * Requires current password to confirm identity.
     */
    public function settings()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userId = (int) $session->get('userId');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        if (! $user) {
            return redirect()->to(base_url('login'));
        }

        if ($this->request->getMethod(true) === 'POST') {
            $email = trim((string) $this->request->getPost('email'));
            $currentPassword = (string) $this->request->getPost('current_password');
            $newPassword = (string) $this->request->getPost('new_password');
            $newPasswordConfirm = (string) $this->request->getPost('new_password_confirm');

            if ($email === '' || $currentPassword === '') {
                return redirect()->back()->withInput()->with('settings_error', 'Email and current password are required.');
            }

            if (! password_verify($currentPassword, $user['password'])) {
                return redirect()->back()->withInput()->with('settings_error', 'Current password is incorrect.');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('settings_error', 'Invalid email address.');
            }

            if (! preg_match('/^[A-Za-z0-9._@-]+$/', $email)) {
                return redirect()->back()->withInput()->with('settings_error', 'Email contains invalid characters.');
            }

            $existing = $userModel->where('email', $email)->where('id !=', $userId)->first();
            if ($existing) {
                return redirect()->back()->withInput()->with('settings_error', 'Email is already in use by another account.');
            }

            $updateData = [ 'email' => $email ];
            $passwordChanged = false;

            if ($newPassword !== '' || $newPasswordConfirm !== '') {
                if ($newPassword === '' || $newPasswordConfirm === '') {
                    return redirect()->back()->withInput()->with('settings_error', 'Both new password fields are required.');
                }

                if ($newPassword !== $newPasswordConfirm) {
                    return redirect()->back()->withInput()->with('settings_error', 'New passwords do not match.');
                }

                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
                $passwordChanged = true;
            }

            if (! $userModel->update($userId, $updateData)) {
                return redirect()->back()->withInput()->with('settings_error', 'Failed to update settings.');
            }

            // If password changed, force logout and require re-login
            if ($passwordChanged) {
                $session->destroy();
                return redirect()->to(base_url('login'))->with('settings_success', 'Password updated successfully. Please log in again.');
            }

            // Email-only change: refresh session email and stay logged in
            $session->set('userEmail', $email);

            return redirect()->back()->with('settings_success', 'Settings updated successfully.');
        }

        $data = [
            'title' => 'Account Settings',
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('auth/settings', $data);
    }
}