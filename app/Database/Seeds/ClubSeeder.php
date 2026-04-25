<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClubSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insertBatch([
            [
                'name'       => 'Tiago Lang',
                'phone'      => '11999990001',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'must_change_password' => 1,
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
