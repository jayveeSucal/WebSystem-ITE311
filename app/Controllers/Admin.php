<?php

namespace App\Controllers;

use App\Models\UserModel;

class Admin extends BaseController
{
    public function users()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        $data = [
            'title' => 'Manage Users',
            'users' => $users,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('admin/users', $data);
    }

    public function createUser()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Add User',
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('admin/create_user', $data);
    }

    public function storeUser()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $name = trim((string) $this->request->getPost('name'));
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        $passwordConfirm = (string) $this->request->getPost('password_confirm');
        $newRole = (string) $this->request->getPost('role');
        $active = $this->request->getPost('active');

        if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '' || $newRole === '' || $active === null || $active === '') {
            return redirect()->back()->withInput()->with('user_error', 'All fields are required.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('user_error', 'Invalid email address.');
        }

        if ($password !== $passwordConfirm) {
            return redirect()->back()->withInput()->with('user_error', 'Passwords do not match.');
        }

        if (! in_array($newRole, ['admin', 'teacher', 'student'], true)) {
            return redirect()->back()->withInput()->with('user_error', 'Invalid role selected.');
        }

        $activeVal = $active === '0' ? 0 : 1;

        $userModel = new UserModel();
        if ($userModel->where('email', $email)->first()) {
            return redirect()->back()->withInput()->with('user_error', 'Email is already registered.');
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $userModel->insert([
            'name' => $name,
            'email' => $email,
            'role' => $newRole,
            'active' => $activeVal,
            'password' => $passwordHash,
        ], true);

        if (! $userId) {
            return redirect()->back()->withInput()->with('user_error', 'Failed to create user.');
        }

        return redirect()->to(base_url('admin/users'))->with('user_success', 'User created successfully.');
    }
    
    public function toggleUserStatus($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('user_error', 'User not found.');
        }

        $current = (int) ($user['active'] ?? 1);
        $new = $current ? 0 : 1;

        $userModel->update($id, ['active' => $new]);

        $message = $new ? 'User has been activated.' : 'User has been deactivated.';

        return redirect()->to(base_url('admin/users'))->with('user_success', $message);
    }

    public function editUser($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('user_error', 'User not found.');
        }

        $data = [
            'title' => 'Edit User',
            'editUser' => $user,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('admin/edit_user', $data);
    }

    public function updateUser($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (! $user) {
            return redirect()->to(base_url('admin/users'))->with('user_error', 'User not found.');
        }

        $name = trim((string) $this->request->getPost('name'));
        $newRole = (string) $this->request->getPost('role');
        $active = $this->request->getPost('active');

        if ($name === '' || $active === null || $active === '') {
            return redirect()->back()->withInput()->with('user_error', 'Name and status are required.');
        }

        $currentRole = (string) ($user['role'] ?? 'student');

        if ($currentRole === 'admin') {
            // Do not allow changing admin role
            $finalRole = 'admin';
        } else {
            if ($newRole === '') {
                return redirect()->back()->withInput()->with('user_error', 'Role is required.');
            }

            if (! in_array($newRole, ['admin', 'teacher', 'student'], true)) {
                return redirect()->back()->withInput()->with('user_error', 'Invalid role selected.');
            }

            $finalRole = $newRole;
        }

        $activeVal = $active === '0' ? 0 : 1;

        $updateData = [
            'name' => $name,
            'role' => $finalRole,
            'active' => $activeVal,
        ];

        $userModel->update($id, $updateData);

        return redirect()->to(base_url('admin/users'))->with('user_success', 'User updated successfully.');
    }
}
