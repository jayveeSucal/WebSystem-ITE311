<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAcademicYearsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '20', // e.g. "2025-2026"
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('academic_years');
    }

    public function down()
    {
        $this->forge->dropTable('academic_years');
    }
}
