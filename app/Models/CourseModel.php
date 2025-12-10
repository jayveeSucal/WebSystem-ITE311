<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'title',
        'description',
        'user_id',
        'academic_year',
        'semester',
        'term',
        'schedule_time',
        'schedule_date',
        'course_number',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all available courses
     */
    public function getAllCourses()
    {
        return $this->findAll();
    }

    /**
     * Get course by ID
     */
    public function getCourseById($id)
    {
        return $this->find($id);
    }
}
