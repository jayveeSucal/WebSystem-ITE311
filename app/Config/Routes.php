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
$routes->post('/course/enrolled', 'Course::getEnrolledCourses');
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');
$routes->get('/course/getAvailableCourses', 'Course::getAvailableCourses');
$routes->post('/course/getAvailableCourses', 'Course::getAvailableCourses');

// Enrollment approval (for teachers)
$routes->get('/courses/pending-enrollments', 'Course::pendingEnrollments');
$routes->post('/courses/approve-enrollment/(:num)', 'Course::approveEnrollment/$1');
$routes->post('/courses/reject-enrollment/(:num)', 'Course::rejectEnrollment/$1');

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
$routes->post('/admin/courses/schedule/update', 'Admin::updateCourseSchedule');

// Admin - academic structure
$routes->get('/admin/academic', 'Admin::academicStructure');
$routes->get('/admin/academic-structure', 'Admin::academicStructure');
$routes->post('/admin/academic-structure/save', 'Admin::saveAcademicStructure');

// Admin - teacher assignments
$routes->get('/admin/teacher-assignments', 'Admin::teacherAssignments');
$routes->post('/admin/teacher-assignments/update', 'Admin::updateTeacherAssignment');
$routes->post('/admin/teacher-assignments/quick-assign', 'Admin::quickAssignTeacher');
$routes->get('/admin/teacher-assignments/available-teachers', 'Admin::getAvailableTeachers');
$routes->post('/admin/teacher-assignments/available-teachers', 'Admin::getAvailableTeachers');

// Admin - completed courses
$routes->get('/admin/completed-courses', 'Admin::completedCourses');

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

