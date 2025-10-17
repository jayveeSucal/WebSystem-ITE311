<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Attempt to find some users and courses to link
        $users = $db->table('users')->select('id')->limit(5)->get()->getResultArray();
        $courses = $db->table('courses')->select('id')->limit(5)->get()->getResultArray();

        if (empty($users) || empty($courses)) {
            return; // nothing to seed
        }

        $now = date('Y-m-d H:i:s');
        $enrollments = [];

        // Enroll first 3 users to first 3 courses (simple sample data)
        $userIds = array_column($users, 'id');
        $courseIds = array_column($courses, 'id');

        foreach (array_slice($userIds, 0, 3) as $userId) {
            foreach (array_slice($courseIds, 0, 3) as $courseId) {
                $enrollments[] = [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'enrollment_date' => $now,
                ];
            }
        }

        if (! empty($enrollments)) {
            $db->table('enrollments')->insertBatch($enrollments);
        }
    }
}


