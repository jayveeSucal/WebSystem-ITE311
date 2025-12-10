<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseOfferingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'term_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'teacher_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'course_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'schedule_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'schedule_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'Planned',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('term_id', 'terms', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('course_offerings');
    }

    public function down()
    {
        $this->forge->dropTable('course_offerings');
    }
}
