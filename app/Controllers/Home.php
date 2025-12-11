<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('index'); // Homepage
    }

    public function about()
    {
        return view('about'); // About page
    }

    public function contact() // Contact page
    {
        return view('contact');
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        return view('dashboard');
    }

    public function debug()
    {
        // Debug page - show system information
        $data = [
            'title' => 'Debug Information',
            'php_version' => phpversion(),
            'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
            'environment' => ENVIRONMENT,
        ];
        return view('debug', $data);
    }
}