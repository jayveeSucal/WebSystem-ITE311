<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrolled_at',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $useTimestamps = false;

    /**
     * Enroll a user in a course.
     * Returns inserted ID on success or false on failure.
     */
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all courses a user is enrolled in.
     * Joins `courses` for display fields.
     */
    public function getUserEnrollments($user_id)
    {
        return $this->db->table('enrollments')
            ->select('enrollments.*, courses.title, courses.description, courses.created_at as course_created')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->get()
            ->getResultArray();
    }

    /**
     * Check if a user is already enrolled in a specific course (approved or pending).
     * Rejected enrollments don't count as enrolled.
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $result = $this->where('user_id', $user_id)
                      ->where('course_id', $course_id)
                      ->whereIn('status', ['pending', 'approved'])
                      ->first();
        
        return $result !== null;
    }

    /**
     * Get pending enrollments for a teacher's courses
     */
    public function getPendingEnrollments($teacher_id)
    {
        return $this->db->table('enrollments e')
            ->select('e.*, c.title as course_title, c.course_number, u.name as student_name, u.email as student_email')
            ->join('courses c', 'c.id = e.course_id')
            ->join('users u', 'u.id = e.user_id')
            ->where('c.user_id', $teacher_id)
            ->where('e.status', 'pending')
            ->orderBy('e.enrolled_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get approved enrollments for a user
     */
    public function getApprovedEnrollments($user_id)
    {
        return $this->db->table('enrollments')
            ->select('enrollments.*, courses.title, courses.description, courses.created_at as course_created')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.status', 'approved')
            ->get()
            ->getResultArray();
    }

    /**
     * Get rejected enrollments for a user
     */
    public function getRejectedEnrollments($user_id)
    {
        return $this->db->table('enrollments')
            ->select('enrollments.*, courses.title, courses.description, courses.course_number, courses.created_at as course_created')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.status', 'rejected')
            ->orderBy('enrollments.approved_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get pending enrollments for a user
     */
    public function getPendingEnrollmentsForUser($user_id)
    {
        return $this->db->table('enrollments')
            ->select('enrollments.*, courses.title, courses.description, courses.course_number, courses.created_at as course_created')
            ->join('courses', 'courses.id = enrollments.course_id')
            ->where('enrollments.user_id', $user_id)
            ->where('enrollments.status', 'pending')
            ->orderBy('enrollments.enrolled_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get available courses for a user (courses they're not enrolled in).
     */
    public function getAvailableCourses($user_id)
    {
        $enrolledCourseIds = $this->where('user_id', $user_id)
                                 ->select('course_id')
                                 ->findColumn('course_id');

        $courseModel = new CourseModel();
        $query = $courseModel->builder();

        if (!empty($enrolledCourseIds)) {
            $query->whereNotIn('id', $enrolledCourseIds);
        }

        return $query->get()->getResultArray();
    }
}
