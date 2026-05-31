<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoneMaskToCountries extends Migration
{
    public function up()
    {
        $this->forge->addColumn('countries', [
            'phone_mask' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
                'default'    => null,
                'after'      => 'code',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('countries', 'phone_mask');
    }
}
