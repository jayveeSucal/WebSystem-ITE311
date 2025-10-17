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
        'enrollment_date',
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
     * Check if a user is already enrolled in a specific course.
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        $result = $this->where('user_id', $user_id)
                      ->where('course_id', $course_id)
                      ->first();
        
        return $result !== null;
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
