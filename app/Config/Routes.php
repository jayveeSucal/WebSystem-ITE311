<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Custom routes
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth & Dashboard
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');
// Registration
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');

// Course management
$routes->get('/courses', 'Course::index');
$routes->get('/courses/create', 'Course::create');
$routes->post('/courses/store', 'Course::store');
$routes->get('/courses/edit/(:num)', 'Course::edit/$1');
$routes->post('/courses/update/(:num)', 'Course::update/$1');
$routes->get('/courses/delete/(:num)', 'Course::delete/$1');

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/enrolled', 'Course::getEnrolledCourses');

// Debug route
$routes->get('/debug', 'Home::debug');