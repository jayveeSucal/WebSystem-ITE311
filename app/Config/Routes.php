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
$routes->get('/courses/deleteMaterials/(:num)', 'Course::deleteMaterials/$1');

// Course enrollment
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/enrolled', 'Course::getEnrolledCourses');
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');
$routes->get('/course/getAvailableCourses', 'Course::getAvailableCourses');
$routes->post('/course/getAvailableCourses', 'Course::getAvailableCourses');

// Materials management
$routes->get('/courses/upload/(:num)', 'Course::upload/$1');
$routes->post('/courses/upload/(:num)', 'Course::upload/$1');
$routes->get('/courses/materials/(:num)', 'Course::materials/$1');
$routes->get('/courses/deleteMaterial/(:num)', 'Course::deleteMaterial/$1');
$routes->get('/courses/download/(:num)', 'Course::download/$1');

// Debug route
$routes->get('/debug', 'Home::debug');

// Notifications
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');


