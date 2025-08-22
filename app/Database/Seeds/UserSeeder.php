<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Sample data for users
        $data = [
            // Admin User
            [
                'name'       => 'Admin User',
                'email'      => 'admin@example.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'role'       => 'admin',  // You can set the role for admin
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Instructor User 1
            [
                'name'       => 'Instructor One',
                'email'      => 'instructor1@example.com',
                'password'   => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'       => 'instructor',  // Set the role for instructors
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Instructor User 2
            [
                'name'       => 'Instructor Two',
                'email'      => 'instructor2@example.com',
                'password'   => password_hash('instructor456', PASSWORD_DEFAULT),
                'role'       => 'instructor',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Student User 1
            [
                'name'       => 'Student One',
                'email'      => 'student1@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'role'       => 'student',  // Set the role for students
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Student User 2
            [
                'name'       => 'Student Two',
                'email'      => 'student2@example.com',
                'password'   => password_hash('student456', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // Student User 3
            [
                'name'       => 'Student Three',
                'email'      => 'student3@example.com',
                'password'   => password_hash('student789', PASSWORD_DEFAULT),
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        // Inserting data into the 'users' table
        $this->db->table('users')->insertBatch($data);
    }
}
