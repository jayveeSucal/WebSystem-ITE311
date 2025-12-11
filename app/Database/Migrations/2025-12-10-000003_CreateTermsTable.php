<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTermsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'semester_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'term_number' => [
                'type'     => 'INT',
                'unsigned' => true, // 1,2,3...
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'CASCADE', 'RESTRICT');
        
        try {
            $this->forge->createTable('terms');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                return; // Table already exists, skip
            }
            throw $e;
        }
    }

    public function down()
    {
        $this->forge->dropTable('terms');
    }
}
