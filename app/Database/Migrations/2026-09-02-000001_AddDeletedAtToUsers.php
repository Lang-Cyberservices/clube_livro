<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE users ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL AFTER updated_at');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE users DROP COLUMN deleted_at');
    }
}
