<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSemestersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'academic_year_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '30', // e.g. "1st Semester"
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'id', 'CASCADE', 'RESTRICT');
        
        try {
            $this->forge->createTable('semesters');
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            if (strpos($e->getMessage(), 'already exists') !== false) {
                return; // Table already exists, skip
            }
            throw $e;
        }
    }

    public function down()
    {
        $this->forge->dropTable('semesters');
    }
}
