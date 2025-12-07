<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActiveToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'active');
    }
}
