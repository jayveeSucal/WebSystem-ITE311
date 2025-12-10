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
// User settings
$routes->get('/settings', 'Auth::settings');
$routes->post('/settings', 'Auth::settings');
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

// Admin - user management
$routes->get('/admin/users', 'Admin::users');
$routes->get('/admin/users/create', 'Admin::createUser');
$routes->post('/admin/users/store', 'Admin::storeUser');
$routes->post('/admin/users/toggle/(:num)', 'Admin::toggleUserStatus/$1');
$routes->get('/admin/users/edit/(:num)', 'Admin::editUser/$1');
$routes->post('/admin/users/update/(:num)', 'Admin::updateUser/$1');

// Admin - course schedule
$routes->get('/admin/courses/schedule', 'Admin::courseSchedule');

// Admin - course offerings
$routes->get('/admin/courses/offering/create', 'Admin::createCourseOffering');
$routes->post('/admin/courses/offering/store', 'Admin::storeCourseOffering');

// Admin - departments & programs
$routes->get('/admin/departments', 'Admin::departments');
$routes->get('/admin/departments/create', 'Admin::createDepartment');
$routes->post('/admin/departments/store', 'Admin::storeDepartment');

$routes->get('/admin/programs', 'Admin::programs');
$routes->get('/admin/programs/create', 'Admin::createProgram');
$routes->post('/admin/programs/store', 'Admin::storeProgram');

// Admin - student records
$routes->get('/admin/student-records', 'Admin::studentRecords');
$routes->get('/admin/student-records/create', 'Admin::createStudentRecord');
$routes->post('/admin/student-records/store', 'Admin::storeStudentRecord');

// API - academic structure (used by admin course offering form)
$routes->get('/api/semesters/by-year/(:num)', 'AcademicApi::semestersByYear/$1');
$routes->get('/api/terms/by-semester/(:num)', 'AcademicApi::termsBySemester/$1');

