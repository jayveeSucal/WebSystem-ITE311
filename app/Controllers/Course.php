<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
    }

    /**
     * Handle course enrollment via AJAX
     */
    public function enroll()
    {
        // Check if user is logged in
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ])->setStatusCode(401);
        }

        // CSRF Protection - CodeIgniter automatically validates CSRF tokens
        // This is handled by the framework's security filters

        // Get user ID from session
        $user_id = $session->get('userId');

        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');

        // Validate course_id - prevent SQL injection and ensure it's a valid integer
        if (!$course_id || !is_numeric($course_id) || $course_id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        // Additional validation: ensure course_id is within reasonable bounds
        if ($course_id > 999999) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        // Check if course exists
        $course = $this->courseModel->getCourseById($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Check if user is already enrolled
        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ])->setStatusCode(400);
        }

        // Prepare enrollment data
        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        // Insert enrollment record
        if ($this->enrollmentModel->enrollUser($enrollmentData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in ' . $course['title'] . '!',
                'course' => $course
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in course. Please try again.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get user's enrolled courses (for AJAX)
     */
    public function getEnrolledCourses()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $user_id = $session->get('userId');
        $enrollments = $this->enrollmentModel->getUserEnrollments($user_id);

        return $this->response->setJSON([
            'success' => true,
            'enrollments' => $enrollments
        ]);
    }

    /**
     * Display all courses (for teachers/admins)
     */
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $courses = $this->courseModel->getAllCourses();
        
        $data = [
            'title' => 'Course Management',
            'courses' => $courses,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('courses/index', $data);
    }

    /**
     * Show create course form
     */
    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $data = [
            'title' => 'Create New Course',
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('courses/create', $data);
    }

    /**
     * Store new course
     */
    public function store()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $user_id = $session->get('userId');

        if (empty($title) || empty($description)) {
            return redirect()->back()->withInput()->with('error', 'Title and description are required.');
        }

        $courseData = [
            'title' => $title,
            'description' => $description,
            'user_id' => $user_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->courseModel->insert($courseData)) {
            return redirect()->to(base_url('courses'))->with('success', 'Course created successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create course. Please try again.');
        }
    }

    /**
     * Show edit course form
     */
    public function edit($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $course = $this->courseModel->getCourseById($id);
        if (!$course) {
            return redirect()->to(base_url('courses'))->with('error', 'Course not found.');
        }

        $data = [
            'title' => 'Edit Course',
            'course' => $course,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('courses/edit', $data);
    }

    /**
     * Update course
     */
    public function update($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');

        if (empty($title) || empty($description)) {
            return redirect()->back()->withInput()->with('error', 'Title and description are required.');
        }

        $courseData = [
            'title' => $title,
            'description' => $description,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($this->courseModel->update($id, $courseData)) {
            return redirect()->to(base_url('courses'))->with('success', 'Course updated successfully!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update course. Please try again.');
        }
    }

    /**
     * Delete course
     */
    public function delete($id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->courseModel->delete($id)) {
            return redirect()->to(base_url('courses'))->with('success', 'Course deleted successfully!');
        } else {
            return redirect()->to(base_url('courses'))->with('error', 'Failed to delete course. Please try again.');
        }
    }
}
