<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicStructure extends Migration
{
    public function up()
    {
        // Departments
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departments', true);

        // Programs
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'department_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'code' => [ 'type' => 'VARCHAR', 'constraint' => 30 ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 150 ],
            'description' => [ 'type' => 'TEXT', 'null' => true ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('department_id');
        $this->forge->addUniqueKey(['department_id','code']);
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('programs', true);

        // Courses
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'program_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'code' => [ 'type' => 'VARCHAR', 'constraint' => 30 ],
            'title' => [ 'type' => 'VARCHAR', 'constraint' => 200 ],
            'description' => [ 'type' => 'TEXT', 'null' => true ],
            'units' => [ 'type' => 'DECIMAL', 'constraint' => '4,1', 'default' => '0.0' ],
            'level' => [ 'type' => 'VARCHAR', 'constraint' => 50, 'null' => true ], // e.g., Year level or course level
            'is_active' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 1 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('program_id');
        $this->forge->addUniqueKey(['program_id','code']);
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('courses', true);

        // Academic Years
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'year_start' => [ 'type' => 'INT', 'constraint' => 4 ],
            'year_end' => [ 'type' => 'INT', 'constraint' => 4 ],
            'is_current' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['year_start','year_end']);
        $this->forge->createTable('academic_years', true);

        // Semesters
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'academic_year_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 50 ], // e.g., First, Second, Summer
            'sequence' => [ 'type' => 'TINYINT', 'constraint' => 2 ],
            'start_date' => [ 'type' => 'DATE', 'null' => true ],
            'end_date' => [ 'type' => 'DATE', 'null' => true ],
            'is_current' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('academic_year_id');
        $this->forge->addForeignKey('academic_year_id', 'academic_years', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['academic_year_id','sequence']);
        $this->forge->createTable('semesters', true);

        // Terms (within semesters)
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'semester_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 50 ], // e.g., Term 1, Term 2
            'sequence' => [ 'type' => 'TINYINT', 'constraint' => 2 ],
            'start_date' => [ 'type' => 'DATE', 'null' => true ],
            'end_date' => [ 'type' => 'DATE', 'null' => true ],
            'is_current' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 0 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('semester_id');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['semester_id','sequence']);
        $this->forge->createTable('terms', true);

        // Grading Periods (within terms)
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'term_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'name' => [ 'type' => 'VARCHAR', 'constraint' => 50 ], // e.g., Midterm, Final
            'sequence' => [ 'type' => 'TINYINT', 'constraint' => 2 ],
            'weight_percent' => [ 'type' => 'DECIMAL', 'constraint' => '5,2', 'default' => '0.00' ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('term_id');
        $this->forge->addForeignKey('term_id', 'terms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addUniqueKey(['term_id','sequence']);
        $this->forge->createTable('grading_periods', true);

        // Enrollment Rules
        $this->forge->addField([
            'id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true ],
            'program_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true ],
            'semester_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'term_id' => [ 'type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true ],
            'rules_json' => [ 'type' => 'TEXT', 'null' => true ], // JSON blob for flexible rules
            'prerequisite_enforced' => [ 'type' => 'TINYINT', 'constraint' => 1, 'default' => 1 ],
            'created_at' => [ 'type' => 'DATETIME', 'null' => true ],
            'updated_at' => [ 'type' => 'DATETIME', 'null' => true ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('program_id');
        $this->forge->addKey('semester_id');
        $this->forge->addKey('term_id');
        $this->forge->addForeignKey('program_id', 'programs', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semesters', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('term_id', 'terms', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addUniqueKey(['program_id','semester_id','term_id']);
        $this->forge->createTable('enrollment_rules', true);
    }

    public function down()
    {
        $this->forge->dropTable('enrollment_rules', true);
        $this->forge->dropTable('grading_periods', true);
        $this->forge->dropTable('terms', true);
        $this->forge->dropTable('semesters', true);
        $this->forge->dropTable('academic_years', true);
        $this->forge->dropTable('courses', true);
        $this->forge->dropTable('programs', true);
        $this->forge->dropTable('departments', true);
    }
}
