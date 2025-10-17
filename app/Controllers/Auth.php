<?php

namespace App\Controllers;

class Auth extends BaseController
{
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
            try {
                $userModel = new \App\Models\UserModel();
                $user = $userModel->where('email', $email)->first();
            } catch (\Throwable $e) {
                // Database not reachable or other DB error
                log_message('error', 'Auth login DB error: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('login_error', 'Database unavailable. Please try again later.');
            }

            if ($user && password_verify($password, $user['password'])) {
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

                // Role-based redirects
                if ($normalizedRole === 'admin') {
                    return redirect()->to(site_url('/admin/dashboard'));
                }

                if ($normalizedRole === 'teacher') {
                    return redirect()->to(site_url('/teacher/dashboard'));
                }

                return redirect()->to(site_url('/announcements'));
            }

            return redirect()->back()->with('login_error', 'Invalid credentials');
        }

        if ($session->get('isLoggedIn')) {
            $role = $session->get('userRole') ?? 'student';
            if ($role === 'admin') {
                return redirect()->to(base_url('admin/dashboard'));
            }
            if ($role === 'teacher') {
                return redirect()->to(base_url('teacher/dashboard'));
            }
            return redirect()->to(base_url('announcements'));
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

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
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

        // Check database connectivity first to avoid Fatal errors when DB is down.
        $dbAvailable = true;
        try {
            $db = \Config\Database::connect();
            // attempt a lightweight connection check
            $db->connect();
        } catch (\Throwable $e) {
            $dbAvailable = false;
            // Optional: log the connectivity issue but don't block dashboard rendering
            log_message('error', 'Database not available for dashboard: ' . $e->getMessage());
        }

        // Role-based data loading only when DB is available
        if ($dbAvailable) {
            try {
                if ($role === 'admin') {
                    $userModel = new \App\Models\UserModel();
                    $courseModel = new \App\Models\CourseModel();
                    $data['stats']['admin']['usersTotal'] = $userModel->countAllResults();
                    $data['stats']['admin']['coursesTotal'] = $courseModel->countAllResults();
                }

                if ($role === 'student') {
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $userId = (int) $session->get('userId');
                    $data['available_courses'] = $enrollmentModel->getAvailableCourses($userId);
                }
            } catch (\Throwable $e) {
                // Keep dashboard functional; log for debugging
                log_message('error', 'Error loading dashboard data: ' . $e->getMessage());
            }
        } else {
            // DB not available: populate safe defaults and notify user via flashdata
            session()->setFlashdata('error', 'Some dashboard features are unavailable because the database is unreachable.');
        }

        // Render unified dashboard with role-conditional content
        return view('auth/dashboard', $data);
    }
}