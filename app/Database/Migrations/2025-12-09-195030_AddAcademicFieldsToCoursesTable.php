<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcademicFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'academic_year' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'semester' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'term' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'schedule_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'schedule_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'course_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'academic_year',
            'semester',
            'term',
            'schedule_time',
            'schedule_date',
            'course_number',
        ]);
    }
}
