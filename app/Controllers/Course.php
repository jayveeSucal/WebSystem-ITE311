<?php
namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\MaterialModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;
    protected $materialModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->materialModel = new MaterialModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Handle course enrollment via AJAX.
     */
    public function enroll()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in to enroll in courses.'
            ])->setStatusCode(401);
        }

        $user_id = $session->get('userId');
        $course_id = $this->request->getPost('course_id');

        if (!$course_id || !is_numeric($course_id) || $course_id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        if ($course_id > 999999) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        $course = $this->courseModel->getCourseById($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        if ($this->enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ])->setStatusCode(400);
        }

        // Check for schedule conflict with other enrolled courses
        if (!empty($course['schedule_date']) && !empty($course['schedule_time'])) {
            $db = \Config\Database::connect();
            $conflictingCourse = $db->table('enrollments e')
                ->join('courses c', 'c.id = e.course_id')
                ->where('e.user_id', $user_id)
                ->where('e.status', 'approved')
                ->where('c.schedule_date', $course['schedule_date'])
                ->where('c.schedule_time', $course['schedule_time'])
                ->where('e.course_id !=', $course_id)
                ->select('c.title, c.course_number')
                ->get()
                ->getRowArray();

            if ($conflictingCourse) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Schedule conflict: Mayroon ka nang enrolled course sa parehong schedule (' . $course['schedule_time'] . '). Course: ' . $conflictingCourse['title'] . ' (' . $conflictingCourse['course_number'] . ')'
                ])->setStatusCode(400);
            }
        }

        $enrollmentData = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrolled_at' => date('Y-m-d H:i:s')
        ];

        if ($this->enrollmentModel->enrollUser($enrollmentData)) {
            // Create a notification for the student
            try {
                $message = 'You have been enrolled in ' . $course['title'];
                $this->notificationModel->insert([
                    'user_id' => $user_id,
                    'message' => $message,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            } catch (\Exception $e) {
                // Log the error so missing table / DB issues are visible in writable/logs
                try {
                    $logger = service('logger');
                    $logger->error('Failed to create notification for user ' . $user_id . ': ' . $e->getMessage());
                } catch (\Throwable $_) {
                    // ignore logging failure
                }
            }
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
     * Get user's enrolled courses (AJAX)
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
     * Search courses (AJAX endpoint for server-side search)
     */
    public function search()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $query = $this->request->getGet('q') ?? $this->request->getPost('q') ?? '';
        $query = trim($query);

        // Get all courses or search results
        if (empty($query)) {
            $courses = $this->courseModel->getAllCourses();
        } else {
            $courses = $this->courseModel->searchCourses($query);
        }

        return $this->response->setJSON([
            'success' => true,
            'courses' => $courses,
            'query' => $query,
            'count' => count($courses)
        ]);
    }

    /**
     * Get available courses for students (AJAX)
     */
    public function getAvailableCourses()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You must be logged in.'
            ])->setStatusCode(401);
        }

        $user_id = $session->get('userId');
        $query = $this->request->getGet('q') ?? $this->request->getPost('q') ?? '';
        $query = trim($query);

        // Get available courses (not enrolled)
        $availableCourses = $this->courseModel->getAvailableCourses($user_id);

        // Apply search filter if query provided
        if (!empty($query)) {
            $availableCourses = array_filter($availableCourses, function($course) use ($query) {
                $searchTerm = strtolower($query);
                $title = strtolower($course['title'] ?? '');
                $description = strtolower($course['description'] ?? '');
                return strpos($title, $searchTerm) !== false || strpos($description, $searchTerm) !== false;
            });
            $availableCourses = array_values($availableCourses); // Re-index array
        }

        return $this->response->setJSON([
            'success' => true,
            'courses' => $availableCourses,
            'query' => $query,
            'count' => count($availableCourses)
        ]);
    }

    /**
     * List all courses (for teachers/admins)
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

        foreach ($courses as &$course) {
            $course['materials'] = $this->materialModel->getMaterialsByCourse($course['id']);
        }

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

        // Fetch academic years from database
        $db = \Config\Database::connect();
        $academicYears = [];
        
        if ($db->tableExists('academic_years')) {
            $years = $db->table('academic_years')
                ->orderBy('year_start', 'DESC')
                ->get()
                ->getResultArray();
            
            // Format academic years with display field
            foreach ($years as $year) {
                $academicYears[] = [
                    'id' => $year['id'],
                    'year_start' => $year['year_start'],
                    'year_end' => $year['year_end'],
                    'display' => $year['year_start'] . '-' . $year['year_end']
                ];
            }
        }

        $data = [
            'title' => 'Create New Course',
            'academicYears' => $academicYears,
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
        $academic_year = $this->request->getPost('academic_year');
        $semester = $this->request->getPost('semester');
        $term = $this->request->getPost('term');
        $schedule_time = $this->request->getPost('schedule_time');
        $schedule_date = $this->request->getPost('schedule_date');
        $cn_number = $this->request->getPost('cn_number');
        $course_number = trim($this->request->getPost('course_number'));
        $user_id = $session->get('userId');

        if (empty($title) || empty($description)) {
            return redirect()->back()->withInput()->with('error', 'Title and description are required.');
        }

        // Format Control Number: CN-XXXX (exactly 4 digits, pad with zeros)
        if (!empty($cn_number)) {
            // Ensure exactly 4 digits
            $paddedNumber = str_pad($cn_number, 4, '0', STR_PAD_LEFT);
            if (strlen($paddedNumber) > 4) {
                $paddedNumber = substr($paddedNumber, -4); // Take last 4 digits if longer
            }
            $course_number = 'CN-' . $paddedNumber;
        } elseif (empty($course_number)) {
            return redirect()->back()->withInput()->with('error', 'Control Number (CN) is required.');
        }

        // Validate CN format - must be exactly CN-XXXX (4 digits)
        if (!preg_match('/^CN-\d{4}$/', $course_number)) {
            return redirect()->back()->withInput()->with('error', 'Invalid Control Number format. Dapat ay eksaktong 4 digits (e.g., CN-0001, CN-0123).');
        }

        // Check if control number already exists (must be unique)
        $db = \Config\Database::connect();
        $existingCourse = $db->table('courses')
            ->where('course_number', $course_number)
            ->get()
            ->getRowArray();

        if ($existingCourse) {
            return redirect()->back()->withInput()->with('error', 'Control Number "' . $course_number . '" ay ginagamit na. Pumili ng ibang Control Number.');
        }

        $courseData = [
            'title' => $title,
            'description' => $description,
            'academic_year' => $academic_year,
            'semester' => $semester,
            'term' => $term,
            'schedule_time' => $schedule_time,
            'schedule_date' => $schedule_date,
            'course_number' => $course_number,
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
     * Delete all materials for a course (but keep the course)
     */
    public function deleteMaterials($id)
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

        $materials = $this->materialModel->getMaterialsByCourse($id);
        $deletedCount = 0;

        foreach ($materials as $material) {
            $filePath = WRITEPATH . $material['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            if ($this->materialModel->delete($material['id'])) {
                $deletedCount++;
            }
        }

        return redirect()->to(base_url('courses'))->with('success', 'All materials deleted successfully! (' . $deletedCount . ' files removed)');
    }



    /**
     * Upload material for a course
     */
    public function upload($course_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $course = $this->courseModel->getCourseById($course_id);
        if (!$course) {
            return redirect()->to(base_url('courses'))->with('error', 'Course not found.');
        }

        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('material');
            if ($file->isValid() && !$file->hasMoved()) {
                // Validate file type - only PDF, PPT, PPTX allowed
                $allowedExtensions = ['pdf', 'ppt', 'pptx'];
                $fileExtension = $file->getClientExtension();
                
                if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
                    return redirect()->back()->with('error', 'Invalid file type. Only PDF, PPT, and PPTX files are allowed.');
                }
                
                // Validate file size (10MB max)
                $maxSize = 10 * 1024 * 1024; // 10MB in bytes
                if ($file->getSize() > $maxSize) {
                    return redirect()->back()->with('error', 'File size exceeds 10MB limit.');
                }
                
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads', $newName);

                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/' . $newName,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                if ($this->materialModel->insertMaterial($data)) {
                    return redirect()->to(base_url('courses'))->with('success', 'Material uploaded successfully!');
                } else {
                    return redirect()->back()->with('error', 'Failed to upload material.');
                }
            } else {
                return redirect()->back()->with('error', 'Invalid file or file upload failed.');
            }
        }

        $data = [
            'title' => 'Upload Material',
            'course' => $course,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
            ],
        ];

        return view('courses/upload', $data);
    }

    /**
     * Delete material
     */
    public function deleteMaterial($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = $session->get('userRole');
        if (!in_array($role, ['teacher', 'admin'])) {
            return redirect()->to(base_url('dashboard'));
        }

        $material = $this->materialModel->find($material_id);
        if (!$material) {
            return redirect()->to(base_url('courses'))->with('error', 'Material not found.');
        }

        $filePath = WRITEPATH . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        if ($this->materialModel->delete($material_id)) {
            return redirect()->back()->with('success', 'Material deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material.');
        }
    }

    /**
     * Download material
     */
    public function download($material_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $material = $this->materialModel->find($material_id);
        if (!$material) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Material not found.');
        }

        $user_id = $session->get('userId');
        if (!$this->enrollmentModel->isAlreadyEnrolled($user_id, $material['course_id'])) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not enrolled in this course.');
        }

        $filePath = WRITEPATH . $material['file_path'];
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null, true)->setFileName($material['file_name']);
        } else {
            return redirect()->back()->with('error', 'File not found.');
        }
    }
}
