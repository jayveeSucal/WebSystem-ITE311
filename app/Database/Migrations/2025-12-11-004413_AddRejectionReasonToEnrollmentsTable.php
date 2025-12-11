<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectionReasonToEnrollmentsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['rejection_reason']);
    }
}
