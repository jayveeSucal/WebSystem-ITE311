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

        $path = $request->getUri()->getPath();
        $path = strtolower($path ?: '/');

        // Admin routes
        if (str_starts_with($path, '/admin')) {
            if ($role !== 'admin') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return;
        }

        // Teacher routes
        if (str_starts_with($path, '/teacher')) {
            if ($role !== 'teacher' && $role !== 'admin') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return;
        }

        // Student routes
        if (str_starts_with($path, '/student')) {
            if ($role !== 'student') {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/announcements'));
            }
            return;
        }

        // Allow announcements to logged-in users
        if ($path === '/announcements' || $path === '/announcements/') {
            if (! $session->get('isLoggedIn')) {
                $session->setFlashdata('error', 'Access Denied: Insufficient Permissions');
                return redirect()->to(site_url('/login'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
