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
                'id' => $session->get('userId'),
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

        if (! preg_match('/^[A-Za-z\s]+$/', $name)) {
            return redirect()->back()->withInput()->with('user_error', 'Name may only contain letters and spaces.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('user_error', 'Invalid email address.');
        }

        if (! preg_match('/^[A-Za-z0-9._@-]+$/', $email)) {
            return redirect()->back()->withInput()->with('user_error', 'Email contains invalid characters.');
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
            log_message('error', 'User not found with ID: ' . $id);
            return redirect()->to(base_url('admin/users'))->with('user_error', 'User not found.');
        }

        // Prevent admin from deactivating themselves
        if ($user['role'] === 'admin' && $id == $session->get('userId')) {
            return redirect()->to(base_url('admin/users'))->with('user_error', 'You cannot deactivate your own admin account.');
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
                'id' => $session->get('userId'),
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

        if (! preg_match('/^[A-Za-z\s]+$/', $name)) {
            return redirect()->back()->withInput()->with('user_error', 'Name may only contain letters and spaces.');
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

    public function courseSchedule()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        // Use existing courses table with academic fields and links to teacher and enrollments
        $builder = $db->table('courses c');
        $builder->select([
            'c.id',
            'c.title',
            'c.course_number',
            'c.academic_year',
            'c.semester',
            'c.term',
            'c.schedule_date',
            'c.schedule_time',
            't.name AS teacher_name',
            'COUNT(DISTINCT su.id) AS enrolled_count',
        ]);

        // teacher is the course owner (user_id)
        $builder->join('users t', 't.id = c.user_id', 'left');

        // enrollments: count only student users
        $builder->join('enrollments e', 'e.course_id = c.id', 'left');
        $builder->join('users su', 'su.id = e.user_id AND su.role = "student"', 'left');

        $builder->groupBy([
            'c.id',
            'c.title',
            'c.course_number',
            'c.academic_year',
            'c.semester',
            'c.term',
            'c.schedule_date',
            'c.schedule_time',
            't.name',
        ]);

        $builder->orderBy('c.academic_year', 'DESC');
        $builder->orderBy('c.semester', 'ASC');
        $builder->orderBy('c.term', 'ASC');
        $builder->orderBy('c.course_number', 'ASC');

        $courses = $builder->get()->getResultArray();

        // Compute a simple status per course based on schedule_date and enrollments
        $today = new \DateTime('today');
        foreach ($courses as &$course) {
            $status = 'Upcoming';

            if (! empty($course['schedule_date'])) {
                $courseDate = \DateTime::createFromFormat('Y-m-d', $course['schedule_date']);

                if ($courseDate instanceof \DateTime) {
                    if ($courseDate < $today && (int) ($course['enrolled_count'] ?? 0) > 0) {
                        $status = 'Completed';
                    } elseif ($courseDate <= $today && $courseDate->format('Y-m-d') === $today->format('Y-m-d')) {
                        $status = 'Ongoing';
                    }
                }
            }

            $course['status'] = $status;
        }
        unset($course);

        $data = [
            'title' => 'Course Schedule',
            'courses' => $courses,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/courses_schedule', $data);
    }

    /**
     * Update course schedule (date and time)
     */
    public function updateCourseSchedule()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You must be logged in.'
                ])->setStatusCode(401);
            }
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ])->setStatusCode(403);
            }
            return redirect()->to(base_url('dashboard'));
        }

        $courseId = $this->request->getPost('course_id');
        $scheduleDate = $this->request->getPost('schedule_date');
        $scheduleTime = $this->request->getPost('schedule_time');

        if (empty($courseId) || empty($scheduleDate) || empty($scheduleTime)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course ID, schedule date, and schedule time are required.'
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('schedule_error', 'All fields are required.');
        }

        $db = \Config\Database::connect();
        $courseModel = new \App\Models\CourseModel();

        // Verify course exists
        $course = $courseModel->find($courseId);
        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course not found.'
                ])->setStatusCode(404);
            }
            return redirect()->back()->with('schedule_error', 'Course not found.');
        }

        // Check for teacher schedule conflict - ALWAYS check schedule_time
        $teacherId = $course['user_id'] ?? null;
        if ($teacherId) {
            $teacherConflictQuery = $db->table('courses')
                ->where('user_id', $teacherId)
                ->where('schedule_time', $scheduleTime)
                ->where('id !=', $courseId);
            
            // If schedule_date is set, also check for date conflict (exact match)
            if ($scheduleDate) {
                $teacherConflictQuery->where('schedule_date', $scheduleDate);
            }
            
            $teacherConflict = $teacherConflictQuery->get()->getRowArray();

            if ($teacherConflict) {
                $conflictDate = !empty($teacherConflict['schedule_date']) ? date('M d, Y', strtotime($teacherConflict['schedule_date'])) : 'Walang date';
                $errorMsg = 'Conflict: Ang teacher ay may ibang course sa parehong schedule time (' . $scheduleTime . ')';
                if ($scheduleDate) {
                    $errorMsg .= ' at date (' . date('M d, Y', strtotime($scheduleDate)) . ')';
                } elseif (!empty($teacherConflict['schedule_date'])) {
                    $errorMsg .= '. Existing course date: ' . $conflictDate;
                }
                $errorMsg .= '. Existing course: ' . ($teacherConflict['title'] ?? 'Course');
                
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $errorMsg
                    ])->setStatusCode(400);
                }
                return redirect()->back()->with('schedule_error', $errorMsg);
            }
        }

        // Check for student schedule conflicts
        $enrolledStudents = $db->table('enrollments')
            ->where('course_id', $courseId)
            ->where('status', 'approved')
            ->get()
            ->getResultArray();

        $conflictingStudents = [];
        foreach ($enrolledStudents as $enrollment) {
            $studentId = $enrollment['user_id'];
            
            // Check if student has another approved enrollment with same schedule
            $studentConflict = $db->table('enrollments e')
                ->join('courses c', 'c.id = e.course_id')
                ->where('e.user_id', $studentId)
                ->where('e.status', 'approved')
                ->where('c.schedule_date', $scheduleDate)
                ->where('c.schedule_time', $scheduleTime)
                ->where('e.course_id !=', $courseId)
                ->select('c.title, c.course_number')
                ->get()
                ->getRowArray();

            if ($studentConflict) {
                $student = $db->table('users')->where('id', $studentId)->get()->getRowArray();
                $conflictingStudents[] = ($student['name'] ?? 'Student') . ' - ' . $studentConflict['title'] . ' (' . $studentConflict['course_number'] . ')';
            }
        }

        if (!empty($conflictingStudents)) {
            $conflictList = implode(', ', array_slice($conflictingStudents, 0, 3));
            if (count($conflictingStudents) > 3) {
                $conflictList .= ' at ' . (count($conflictingStudents) - 3) . ' pa';
            }
            $errorMsg = 'Conflict: May mga students na may conflict sa schedule: ' . $conflictList;
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $errorMsg
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('schedule_error', $errorMsg);
        }

        // Update schedule
        $updateData = [
            'schedule_date' => $scheduleDate,
            'schedule_time' => $scheduleTime,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($courseModel->update($courseId, $updateData)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Schedule updated successfully.'
                ]);
            }
            return redirect()->back()->with('schedule_success', 'Schedule updated successfully.');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update schedule.'
                ])->setStatusCode(500);
            }
            return redirect()->back()->with('schedule_error', 'Failed to update schedule.');
        }
    }

    public function createCourseOffering()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $academicYears = $db->table('academic_years')->orderBy('name', 'DESC')->get()->getResultArray();
        // Order courses by course_number since there is no 'code' column on courses
        $courses = $db->table('courses')->orderBy('course_number', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Create Course Offering',
            'academicYears' => $academicYears,
            'courses' => $courses,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/create_course_offering', $data);
    }

    public function storeCourseOffering()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $academicYearId = (int) $this->request->getPost('academic_year_id');
        $semesterId      = (int) $this->request->getPost('semester_id');
        $termId          = (int) $this->request->getPost('term_id');
        $courseId        = (int) $this->request->getPost('course_id');
        $courseNumber    = trim((string) $this->request->getPost('course_number'));
        $scheduleDate    = (string) $this->request->getPost('schedule_date');
        $scheduleTime    = (string) $this->request->getPost('schedule_time');

        if ($academicYearId === 0 || $semesterId === 0 || $termId === 0 || $courseId === 0 || $courseNumber === '' || $scheduleDate === '' || $scheduleTime === '') {
            return redirect()->back()->withInput()->with('offering_error', 'All fields are required.');
        }

        // Basic consistency checks
        $semester = $db->table('semesters')->where('id', $semesterId)->where('academic_year_id', $academicYearId)->get()->getRowArray();
        if (! $semester) {
            return redirect()->back()->withInput()->with('offering_error', 'Selected semester does not belong to the chosen academic year.');
        }

        $term = $db->table('terms')->where('id', $termId)->where('semester_id', $semesterId)->get()->getRowArray();
        if (! $term) {
            return redirect()->back()->withInput()->with('offering_error', 'Selected term does not belong to the chosen semester.');
        }

        $course = $db->table('courses')->where('id', $courseId)->get()->getRowArray();
        if (! $course) {
            return redirect()->back()->withInput()->with('offering_error', 'Selected course not found.');
        }

        $builder = $db->table('course_offerings');
        $builder->insert([
            'course_id'      => $courseId,
            'term_id'        => $termId,
            'course_number'  => $courseNumber,
            'schedule_date'  => $scheduleDate,
            'schedule_time'  => $scheduleTime,
        ]);

        return redirect()->to(base_url('admin/courses/schedule'))->with('offering_success', 'Course offering created successfully.');
    }

    public function departments()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();
        $departments = $db->table('departments')->orderBy('code', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Departments',
            'departments' => $departments,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/departments', $data);
    }

    public function createDepartment()
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
            'title' => 'Create Department',
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/create_department', $data);
    }

    public function storeDepartment()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $code = strtoupper(trim((string) $this->request->getPost('code')));
        $name = trim((string) $this->request->getPost('name'));

        if ($code === '' || $name === '') {
            return redirect()->back()->withInput()->with('dept_error', 'Code and name are required.');
        }

        $db = \Config\Database::connect();
        $exists = $db->table('departments')->where('code', $code)->get()->getRowArray();
        if ($exists) {
            return redirect()->back()->withInput()->with('dept_error', 'Department code already exists.');
        }

        $db->table('departments')->insert([
            'code' => $code,
            'name' => $name,
        ]);

        return redirect()->to(base_url('admin/departments'))->with('dept_success', 'Department created successfully.');
    }

    public function programs()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $builder = $db->table('programs p');
        $builder->select('p.*, d.code AS dept_code, d.name AS dept_name');
        $builder->join('departments d', 'd.id = p.department_id', 'left');
        $programs = $builder->orderBy('p.code', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Programs',
            'programs' => $programs,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/programs', $data);
    }

    public function createProgram()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();
        $departments = $db->table('departments')->orderBy('code', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Create Program',
            'departments' => $departments,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/create_program', $data);
    }

    public function storeProgram()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $departmentId = (int) $this->request->getPost('department_id');
        $code = strtoupper(trim((string) $this->request->getPost('code')));
        $name = trim((string) $this->request->getPost('name'));

        if ($departmentId === 0 || $code === '' || $name === '') {
            return redirect()->back()->withInput()->with('prog_error', 'All fields are required.');
        }

        $db = \Config\Database::connect();
        $dept = $db->table('departments')->where('id', $departmentId)->get()->getRowArray();
        if (! $dept) {
            return redirect()->back()->withInput()->with('prog_error', 'Selected department not found.');
        }

        $exists = $db->table('programs')->where('code', $code)->get()->getRowArray();
        if ($exists) {
            return redirect()->back()->withInput()->with('prog_error', 'Program code already exists.');
        }

        $db->table('programs')->insert([
            'department_id' => $departmentId,
            'code'          => $code,
            'name'          => $name,
        ]);

        return redirect()->to(base_url('admin/programs'))->with('prog_success', 'Program created successfully.');
    }

    public function studentRecords()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $builder = $db->table('students s');
        $builder->select('s.*, u.name AS user_name, u.email, d.code AS dept_code, d.name AS dept_name, p.code AS prog_code, p.name AS prog_name');
        $builder->join('users u', 'u.id = s.user_id', 'left');
        $builder->join('departments d', 'd.id = s.department_id', 'left');
        $builder->join('programs p', 'p.id = s.program_id', 'left');
        $students = $builder->orderBy('u.name', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Student Records',
            'students' => $students,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/student_records', $data);
    }

    public function createStudentRecord()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $studentsUsers = $db->table('users')->where('role', 'student')->orderBy('name', 'ASC')->get()->getResultArray();
        $departments   = $db->table('departments')->orderBy('code', 'ASC')->get()->getResultArray();
        $programs      = $db->table('programs')->orderBy('code', 'ASC')->get()->getResultArray();

        $data = [
            'title' => 'Create Student Record',
            'studentUsers' => $studentsUsers,
            'departments'  => $departments,
            'programs'     => $programs,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/create_student_record', $data);
    }

    public function storeStudentRecord()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();

        $userId        = (int) $this->request->getPost('user_id');
        $studentNumber = trim((string) $this->request->getPost('student_number'));
        $departmentId  = (int) $this->request->getPost('department_id');
        $programId     = (int) $this->request->getPost('program_id');

        if ($userId === 0 || $studentNumber === '' || $departmentId === 0 || $programId === 0) {
            return redirect()->back()->withInput()->with('stud_error', 'All fields are required.');
        }

        $user = $db->table('users')->where('id', $userId)->where('role', 'student')->get()->getRowArray();
        if (! $user) {
            return redirect()->back()->withInput()->with('stud_error', 'Selected user is not a student.');
        }

        $program = $db->table('programs')->where('id', $programId)->get()->getRowArray();
        if (! $program) {
            return redirect()->back()->withInput()->with('stud_error', 'Selected program not found.');
        }

        if ((int) $program['department_id'] !== $departmentId) {
            return redirect()->back()->withInput()->with('stud_error', 'Selected program does not belong to the chosen department.');
        }

        $existingByUser = $db->table('students')->where('user_id', $userId)->get()->getRowArray();
        if ($existingByUser) {
            return redirect()->back()->withInput()->with('stud_error', 'This student already has a record.');
        }

        $existingByNumber = $db->table('students')->where('student_number', $studentNumber)->get()->getRowArray();
        if ($existingByNumber) {
            return redirect()->back()->withInput()->with('stud_error', 'Student number is already in use.');
        }

        $db->table('students')->insert([
            'user_id'        => $userId,
            'student_number' => $studentNumber,
            'department_id'  => $departmentId,
            'program_id'     => $programId,
        ]);

        return redirect()->to(base_url('admin/student-records'))->with('stud_success', 'Student record created successfully.');
    }

    /**
     * Academic Structure Management
     */
    public function academicStructure()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();
        $academicYears = [];
        $currentYear = null;
        $canCreateNewYear = true;
        $currentYearMessage = '';
        
        if ($db->tableExists('academic_years')) {
            // Get all academic years and format them
            $years = $db->table('academic_years')
                ->orderBy('year_start', 'DESC')
                ->get()
                ->getResultArray();
            
            foreach ($years as $year) {
                $academicYears[] = [
                    'id' => $year['id'],
                    'year_start' => $year['year_start'],
                    'year_end' => $year['year_end'],
                    'display' => $year['year_start'] . '-' . $year['year_end'],
                    'is_current' => isset($year['is_current']) ? (int)$year['is_current'] : 0
                ];
            }
            
            // Check for current school year
            $currentYear = $db->table('academic_years')
                ->where('is_current', 1)
                ->orderBy('year_start', 'DESC')
                ->get()
                ->getRowArray();
            
            if ($currentYear) {
                // Check if current year has ended (current date is past year_end)
                $currentDate = date('Y-m-d');
                $yearEndDate = $currentYear['year_end'] . '-12-31'; // Assume year ends on Dec 31
                
                if ($currentDate <= $yearEndDate) {
                    $canCreateNewYear = false;
                    $currentYearMessage = 'May aktibong school year: ' . $currentYear['year_start'] . '-' . $currentYear['year_end'] . '. Kailangan matapos muna ang current school year bago makagawa ng bago.';
                } else {
                    // Year has ended, mark as not current
                    $db->table('academic_years')
                        ->where('id', $currentYear['id'])
                        ->update(['is_current' => 0]);
                    $canCreateNewYear = true;
                }
            }
        }
        
        // If no academic years exist, allow creating one
        if (empty($academicYears)) {
            $canCreateNewYear = true;
        }

        // Fetch complete academic structures with semesters and terms
        $academicStructures = [];
        if ($db->tableExists('academic_years') && $db->tableExists('semesters') && $db->tableExists('terms')) {
            foreach ($academicYears as $year) {
                $semesters = $db->table('semesters')
                    ->where('academic_year_id', $year['id'])
                    ->orderBy('sequence', 'ASC')
                    ->get()
                    ->getResultArray();
                
                $yearData = [
                    'id' => $year['id'],
                    'year_start' => $year['year_start'],
                    'year_end' => $year['year_end'],
                    'display' => $year['display'],
                    'is_current' => $year['is_current'],
                    'semesters' => []
                ];
                
                foreach ($semesters as $semester) {
                    $terms = $db->table('terms')
                        ->where('semester_id', $semester['id'])
                        ->orderBy('sequence', 'ASC')
                        ->get()
                        ->getResultArray();
                    
                    $yearData['semesters'][] = [
                        'id' => $semester['id'],
                        'name' => $semester['name'],
                        'sequence' => $semester['sequence'],
                        'start_date' => $semester['start_date'] ?? null,
                        'end_date' => $semester['end_date'] ?? null,
                        'terms' => $terms
                    ];
                }
                
                $academicStructures[] = $yearData;
            }
        }

        $data = [
            'title' => 'Academic Structure Management',
            'academicYears' => $academicYears,
            'academicStructures' => $academicStructures,
            'currentYear' => $currentYear,
            'canCreateNewYear' => $canCreateNewYear,
            'currentYearMessage' => $currentYearMessage,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/academic_structure', $data);
    }

    /**
     * Save Academic Structure
     */
    public function saveAcademicStructure()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Forbidden'
            ])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        
        // Get JSON input
        $input = json_decode($this->request->getBody(), true);
        if (!$input) {
            $input = $this->request->getPost();
        }

        $isNewYear = $input['is_new_year'] ?? false;
        $academicYearId = $input['academic_year_id'] ?? null;
        $semesters = $input['semesters'] ?? [];

        // Validate input
        if (empty($semesters) || count($semesters) !== 2) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please provide dates for both semesters with all terms.'
            ])->setStatusCode(400);
        }

        // If creating new year, validate and create it
        if ($isNewYear) {
            $yearStart = $input['year_start'] ?? null;
            $yearEnd = $input['year_end'] ?? null;

            if (!$yearStart || !$yearEnd) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Year Start and Year End are required for new school year.'
                ])->setStatusCode(400);
            }

            // Check if year already exists
            $existing = $db->table('academic_years')
                ->where('year_start', $yearStart)
                ->where('year_end', $yearEnd)
                ->get()
                ->getRowArray();

            if ($existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'School year ' . $yearStart . '-' . $yearEnd . ' already exists.'
                ])->setStatusCode(400);
            }

            // Check if there's a current year that hasn't ended
            $currentYear = $db->table('academic_years')
                ->where('is_current', 1)
                ->get()
                ->getRowArray();

            if ($currentYear) {
                $currentDate = date('Y-m-d');
                $yearEndDate = $currentYear['year_end'] . '-12-31';
                
                if ($currentDate <= $yearEndDate) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Hindi maaaring gumawa ng bagong school year habang may aktibong school year.'
                    ])->setStatusCode(400);
                }
            }

            // Create new academic year
            $db->table('academic_years')->insert([
                'year_start' => $yearStart,
                'year_end' => $yearEnd,
                'is_current' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $academicYearId = $db->insertID();

            // Mark previous current year as not current
            if ($currentYear) {
                $db->table('academic_years')
                    ->where('id', $currentYear['id'])
                    ->update(['is_current' => 0]);
            }
        } else {
            if (!$academicYearId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Academic Year ID is required.'
                ])->setStatusCode(400);
            }

            // Verify year exists
            $year = $db->table('academic_years')
                ->where('id', $academicYearId)
                ->get()
                ->getRowArray();

            if (!$year) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Academic year not found.'
                ])->setStatusCode(404);
            }
        }

        // Delete existing semesters and terms for this year (if updating)
        $existingSemesters = $db->table('semesters')
            ->where('academic_year_id', $academicYearId)
            ->get()
            ->getResultArray();

        foreach ($existingSemesters as $sem) {
            $db->table('terms')->where('semester_id', $sem['id'])->delete();
        }
        $db->table('semesters')->where('academic_year_id', $academicYearId)->delete();

        // Insert semesters and terms
        try {
            $db->transStart();
            
            foreach ($semesters as $semester) {
                $sequence = $semester['sequence'] ?? 0;
                $terms = $semester['terms'] ?? [];

                if (empty($terms)) {
                    continue;
                }

                // Insert semester
                $db->table('semesters')->insert([
                    'academic_year_id' => $academicYearId,
                    'name' => $sequence == 1 ? 'First' : 'Second',
                    'sequence' => $sequence,
                    'is_current' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $semesterId = $db->insertID();

                if (!$semesterId) {
                    throw new \Exception('Failed to insert semester');
                }

                // Insert terms
                foreach ($terms as $term) {
                    $termSequence = $term['sequence'] ?? 0;
                    $startDate = $term['start_date'] ?? null;
                    $endDate = $term['end_date'] ?? null;

                    if (!$startDate || !$endDate) {
                        continue;
                    }

                    $db->table('terms')->insert([
                        'semester_id' => $semesterId,
                        'name' => 'Term ' . $termSequence,
                        'sequence' => $termSequence,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'is_current' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Database transaction failed');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Academic structure saved successfully!'
            ]);
        } catch (\Exception $e) {
            // Log the error
            log_message('error', 'Failed to save academic structure: ' . $e->getMessage());
            log_message('error', 'Input data: ' . json_encode($input));
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save academic structure: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Teacher Assignments Management
     */
    public function teacherAssignments()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();
        
        // Get all courses with their assigned teachers (include role to check if admin, teacher, or instructor)
        $courses = [];
        if ($db->tableExists('courses')) {
            // Check if units column exists in courses table
            $columns = $db->getFieldNames('courses');
            $hasUnits = in_array('units', $columns);
            
            $selectFields = 'c.*, u.name as teacher_name, u.email as teacher_email, u.role as teacher_role';
            if ($hasUnits) {
                $selectFields .= ', c.units';
            }
            
            $courses = $db->table('courses c')
                ->select($selectFields)
                ->join('users u', 'u.id = c.user_id', 'left')
                ->orderBy('c.academic_year', 'DESC')
                ->orderBy('c.semester', 'ASC')
                ->orderBy('c.term', 'ASC')
                ->get()
                ->getResultArray();
        }

        // Get all teachers (include both 'teacher' and 'instructor' roles, exclude admins)
        $teachers = [];
        if ($db->tableExists('users')) {
            $teachers = $db->table('users')
                ->whereIn('role', ['teacher', 'instructor'])
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
        }

        // For each course, determine which teachers have schedule conflicts
        // This will be used in the view to filter dropdown options
        $courseTeacherConflicts = [];
        foreach ($courses as $course) {
            $courseId = $course['id'];
            $scheduleTime = $course['schedule_time'] ?? null;
            $scheduleDate = $course['schedule_date'] ?? null;
            $conflictingTeacherIds = [];
            
            if ($scheduleTime) {
                // Get all teachers who have courses with the same schedule_time
                $conflictQuery = $db->table('courses')
                    ->where('schedule_time', $scheduleTime)
                    ->where('id !=', $courseId)
                    ->where('schedule_time !=', '')
                    ->where('schedule_time IS NOT NULL')
                    ->select('user_id');
                
                if ($scheduleDate) {
                    $conflictQuery->where('schedule_date', $scheduleDate);
                }
                
                $conflicts = $conflictQuery->get()->getResultArray();
                foreach ($conflicts as $conflict) {
                    if (!empty($conflict['user_id'])) {
                        $conflictingTeacherIds[] = $conflict['user_id'];
                    }
                }
            }
            
            $courseTeacherConflicts[$courseId] = $conflictingTeacherIds;
        }

        $data = [
            'title' => 'Teacher Assignments',
            'courses' => $courses,
            'teachers' => $teachers,
            'courseTeacherConflicts' => $courseTeacherConflicts,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/teacher_assignments', $data);
    }

    /**
     * Update teacher assignment for a course
     */
    public function updateTeacherAssignment()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You must be logged in.'
                ])->setStatusCode(401);
            }
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access.'
                ])->setStatusCode(403);
            }
            return redirect()->to(base_url('dashboard'));
        }

        $courseId = $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');

        if (empty($courseId)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course ID is required.'
                ])->setStatusCode(400);
            }
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        $db = \Config\Database::connect();
        $courseModel = new \App\Models\CourseModel();

        // Verify course exists
        $course = $courseModel->find($courseId);
        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Course not found.'
                ])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Verify teacher is actually a teacher or instructor (not admin) if teacherId is provided
        if (!empty($teacherId)) {
            $teacher = $db->table('users')
                ->where('id', $teacherId)
                ->whereIn('role', ['teacher', 'instructor'])
                ->get()
                ->getRowArray();
            
            if (!$teacher) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Selected user is not a teacher. Only teachers can be assigned to courses.'
                    ])->setStatusCode(400);
                }
                return redirect()->back()->with('error', 'Selected user is not a teacher. Only teachers can be assigned to courses.');
            }

            // Check for teacher schedule conflict if course has schedule_time
            $scheduleTime = $course['schedule_time'] ?? null;
            $scheduleDate = $course['schedule_date'] ?? null;
            
            if ($scheduleTime) {
                // STRICT CHECK: Always check if teacher has ANY course with same schedule_time
                $teacherConflictQuery = $db->table('courses')
                    ->where('user_id', $teacherId)
                    ->where('schedule_time', $scheduleTime)
                    ->where('id !=', $courseId)
                    ->where('schedule_time !=', '')
                    ->where('schedule_time IS NOT NULL');
                
                // If schedule_date is set, also check for date conflict (exact match)
                if ($scheduleDate) {
                    $teacherConflictQuery->where('schedule_date', $scheduleDate);
                }
                // If no date, still check - prevents same time slot conflicts
                
                $teacherConflict = $teacherConflictQuery->get()->getRowArray();

                if ($teacherConflict) {
                    $conflictDate = !empty($teacherConflict['schedule_date']) ? date('M d, Y', strtotime($teacherConflict['schedule_date'])) : 'Walang date';
                    $errorMsg = 'HINDI MAKAKA-ASSIGN: Ang teacher na ito ay may ibang course sa parehong schedule time (' . $scheduleTime . ')';
                    if ($scheduleDate) {
                        $errorMsg .= ' at date (' . date('M d, Y', strtotime($scheduleDate)) . ')';
                    }
                    if (!empty($teacherConflict['schedule_date']) && !$scheduleDate) {
                        $errorMsg .= '. Existing course date: ' . $conflictDate;
                    }
                    $errorMsg .= '. Existing course: ' . ($teacherConflict['title'] ?? 'Course') . ' (' . ($teacherConflict['course_number'] ?? 'N/A') . ')';
                    
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => $errorMsg
                        ])->setStatusCode(400);
                    }
                    return redirect()->back()->with('error', $errorMsg);
                }
            }
        }

        // Update teacher assignment
        $updateData = [
            'user_id' => $teacherId ?: null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($courseModel->update($courseId, $updateData)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Teacher assignment updated successfully.'
                ]);
            }
            return redirect()->back()->with('success', 'Teacher assignment updated successfully.');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update teacher assignment.'
                ])->setStatusCode(500);
            }
            return redirect()->back()->with('error', 'Failed to update teacher assignment.');
        }
    }

    /**
     * Completed Courses View
     */
    public function completedCourses()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $db = \Config\Database::connect();
        
        // Get completed courses (courses with schedule_date in the past and have enrollments)
        $today = date('Y-m-d');
        $courses = [];
        
        if ($db->tableExists('courses')) {
            $builder = $db->table('courses c');
            $builder->select([
                'c.*',
                't.name AS teacher_name',
                'COUNT(DISTINCT e.user_id) AS enrolled_count',
            ]);
            $builder->join('users t', 't.id = c.user_id', 'left');
            $builder->join('enrollments e', 'e.course_id = c.id', 'left');
            $builder->where('c.schedule_date <', $today);
            $builder->where('c.schedule_date !=', '');
            $builder->groupBy('c.id');
            $builder->having('enrolled_count >', 0);
            $builder->orderBy('c.schedule_date', 'DESC');
            
            $courses = $builder->get()->getResultArray();
        }

        $data = [
            'title' => 'Completed Courses',
            'courses' => $courses,
            'user' => [
                'name' => $session->get('userName'),
                'email' => $session->get('userEmail'),
                'id' => $session->get('userId'),
            ],
        ];

        return view('admin/completed_courses', $data);
    }

    /**
     * Quick assign teacher to course with CN and time
     */
    public function quickAssignTeacher()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return redirect()->to(base_url('dashboard'));
        }

        $courseId = $this->request->getPost('course_id');
        $teacherId = $this->request->getPost('teacher_id');
        $courseNumber = trim($this->request->getPost('course_number'));
        $scheduleTime = $this->request->getPost('schedule_time');

        if (empty($courseId) || empty($teacherId) || empty($courseNumber) || empty($scheduleTime)) {
            return redirect()->back()->with('error', 'All fields are required: Course, Teacher, CN, and Schedule Time.');
        }

        $db = \Config\Database::connect();
        $courseModel = new \App\Models\CourseModel();

        // Verify course exists
        $course = $courseModel->find($courseId);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Verify teacher is actually a teacher or instructor (not admin)
        $teacher = $db->table('users')
            ->where('id', $teacherId)
            ->whereIn('role', ['teacher', 'instructor'])
            ->get()
            ->getRowArray();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Selected user is not a teacher. Only teachers can be assigned to courses.');
        }

        // Get current course schedule_date if exists
        $scheduleDate = $course['schedule_date'] ?? null;

        // Check for teacher schedule conflict - STRICT CHECKING
        // Always check if teacher has ANY course with the same schedule_time
        // This prevents assigning the same time slot to a teacher
        $teacherConflictQuery = $db->table('courses')
            ->where('user_id', $teacherId)
            ->where('schedule_time', $scheduleTime)
            ->where('id !=', $courseId)
            ->where('schedule_time !=', '')
            ->where('schedule_time IS NOT NULL');
        
        // If current course has schedule_date, check for exact date match
        // If current course has NO schedule_date, still check for time conflict
        // (teacher can't have two courses at same time, even on different dates)
        if ($scheduleDate) {
            // If current course has date, check for courses with same date AND time
            $teacherConflictQuery->where('schedule_date', $scheduleDate);
        }
        // If no date, still check - prevents same time slot conflicts
        
        $teacherConflict = $teacherConflictQuery->get()->getRowArray();

        if ($teacherConflict) {
            $conflictDate = !empty($teacherConflict['schedule_date']) ? date('M d, Y', strtotime($teacherConflict['schedule_date'])) : 'Walang date';
            $errorMsg = 'HINDI MAKAKA-ASSIGN: Ang teacher na ito ay may ibang course sa parehong schedule time (' . $scheduleTime . ')';
            if ($scheduleDate) {
                $errorMsg .= ' at date (' . date('M d, Y', strtotime($scheduleDate)) . ')';
            }
            if (!empty($teacherConflict['schedule_date']) && !$scheduleDate) {
                $errorMsg .= '. Existing course date: ' . $conflictDate;
            }
            $errorMsg .= '. Existing course: ' . ($teacherConflict['title'] ?? 'Course') . ' (' . ($teacherConflict['course_number'] ?? 'N/A') . ')';
            return redirect()->back()->with('error', $errorMsg);
        }

        // Check for student schedule conflicts
        if ($scheduleDate && $scheduleTime) {
            // Get all students enrolled in this course
            $enrolledStudents = $db->table('enrollments')
                ->where('course_id', $courseId)
                ->where('status', 'approved')
                ->get()
                ->getResultArray();

            $conflictingStudents = [];
            foreach ($enrolledStudents as $enrollment) {
                $studentId = $enrollment['user_id'];
                
                // Check if student has another approved enrollment with same schedule
                $studentConflict = $db->table('enrollments e')
                    ->join('courses c', 'c.id = e.course_id')
                    ->where('e.user_id', $studentId)
                    ->where('e.status', 'approved')
                    ->where('c.schedule_date', $scheduleDate)
                    ->where('c.schedule_time', $scheduleTime)
                    ->where('e.course_id !=', $courseId)
                    ->select('c.title, c.course_number')
                    ->get()
                    ->getRowArray();

                if ($studentConflict) {
                    $student = $db->table('users')->where('id', $studentId)->get()->getRowArray();
                    $conflictingStudents[] = ($student['name'] ?? 'Student') . ' - ' . $studentConflict['title'] . ' (' . $studentConflict['course_number'] . ')';
                }
            }

            if (!empty($conflictingStudents)) {
                $conflictList = implode(', ', array_slice($conflictingStudents, 0, 3));
                if (count($conflictingStudents) > 3) {
                    $conflictList .= ' at ' . (count($conflictingStudents) - 3) . ' pa';
                }
                return redirect()->back()->with('error', 'Conflict: May mga students na may conflict sa schedule: ' . $conflictList);
            }
        }

        // Update course with teacher, CN, and schedule time
        $updateData = [
            'user_id' => $teacherId,
            'course_number' => $courseNumber,
            'schedule_time' => $scheduleTime,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($courseModel->update($courseId, $updateData)) {
            return redirect()->back()->with('success', 'Teacher assigned successfully with CN and schedule time!');
        } else {
            return redirect()->back()->with('error', 'Failed to assign teacher. Please try again.');
        }
    }

    /**
     * Get available teachers for a schedule (AJAX endpoint)
     * Returns teachers without schedule conflicts
     */
    public function getAvailableTeachers()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        $role = (string) $session->get('userRole');
        if ($role !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Forbidden'
            ])->setStatusCode(403);
        }

        $scheduleTime = $this->request->getGet('schedule_time') ?? $this->request->getPost('schedule_time');
        $scheduleDate = $this->request->getGet('schedule_date') ?? $this->request->getPost('schedule_date');
        $courseId = $this->request->getGet('course_id') ?? $this->request->getPost('course_id');

        if (empty($scheduleTime)) {
            // If no schedule time, return all teachers
            $db = \Config\Database::connect();
            $teachers = $db->table('users')
                ->whereIn('role', ['teacher', 'instructor'])
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'teachers' => $teachers
            ]);
        }

        $db = \Config\Database::connect();
        
        // Get all teachers
        $allTeachers = $db->table('users')
            ->whereIn('role', ['teacher', 'instructor'])
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Get teachers with conflicts
        $conflictQuery = $db->table('courses')
            ->where('schedule_time', $scheduleTime)
            ->where('schedule_time !=', '')
            ->where('schedule_time IS NOT NULL')
            ->select('user_id');
        
        if ($scheduleDate) {
            $conflictQuery->where('schedule_date', $scheduleDate);
        }
        
        if ($courseId) {
            $conflictQuery->where('id !=', $courseId);
        }
        
        $conflicts = $conflictQuery->get()->getResultArray();
        $conflictingTeacherIds = array_column($conflicts, 'user_id');
        $conflictingTeacherIds = array_filter($conflictingTeacherIds); // Remove nulls

        // Filter out teachers with conflicts
        $availableTeachers = array_filter($allTeachers, function($teacher) use ($conflictingTeacherIds) {
            return !in_array($teacher['id'], $conflictingTeacherIds);
        });

        return $this->response->setJSON([
            'success' => true,
            'teachers' => array_values($availableTeachers),
            'conflicting_count' => count($conflictingTeacherIds)
        ]);
    }
}
