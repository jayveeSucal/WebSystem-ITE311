<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $role = (string) $session->get('userRole');
        if ($role === 'instructor') {
            $role = 'teacher';
        }
        if ($role === '') {
            $role = 'guest';
        }

        $uri = current_url();
        $path = parse_url($uri, PHP_URL_PATH) ?: '';

        // Normalize to lower-case for checks
        $path = strtolower($path);

        // Admin routes: start with /admin
        if (str_starts_with($path, '/admin')) {
            if ($role !== 'admin') {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return; // allowed
        }

        // Teacher routes: start with /teacher
        if (str_starts_with($path, '/teacher')) {
            if ($role !== 'teacher' && $role !== 'admin') {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return;
        }

        // Student-only routes: start with /student
        if (str_starts_with($path, '/student')) {
            if ($role !== 'student') {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return;
        }

        // Allow /announcements for everyone who is logged in
        if ($path === '/announcements' || $path === '/announcements/') {
            if (! $session->get('isLoggedIn')) {
                session()->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/login'));
            }
            return;
        }

        // No restrictions for other routes here
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
