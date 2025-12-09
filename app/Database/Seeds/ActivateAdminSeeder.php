<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ActivateAdminSeeder extends Seeder
{
    public function run()
    {
        // Update the admin user to active
        $this->db->table('users')
                 ->where('email', 'admin@example.com')
                 ->update(['active' => 1]);
    }
}
