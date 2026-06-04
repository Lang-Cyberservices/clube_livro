<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRememberTokenToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE users ADD COLUMN remember_token VARCHAR(100) NULL DEFAULT NULL AFTER password');
        $this->db->query('ALTER TABLE users ADD COLUMN remember_token_expires_at DATETIME NULL DEFAULT NULL AFTER remember_token');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE users DROP COLUMN remember_token_expires_at');
        $this->db->query('ALTER TABLE users DROP COLUMN remember_token');
    }
}
