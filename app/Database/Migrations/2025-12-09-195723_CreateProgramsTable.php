<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProgramsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'department_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->addUniqueKey('code');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('programs');
    }

    public function down()
    {
        $this->forge->dropTable('programs');
    }
}
