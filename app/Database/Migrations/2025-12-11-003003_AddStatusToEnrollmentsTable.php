<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'pending',
                'after'      => 'enrolled_at',
            ],
            'approved_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['status', 'approved_by', 'approved_at']);
    }
}
