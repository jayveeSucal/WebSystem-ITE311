<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Semester',
                'content' => 'Classes begin next Monday. Please check your schedules and enrolment.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'title' => 'System Maintenance',
                'content' => 'The portal will be unavailable this Sunday from 02:00 to 05:00 for maintenance.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
