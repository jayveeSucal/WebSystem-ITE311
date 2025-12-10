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
}
