<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCountryIdToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE users ADD COLUMN country_id INT UNSIGNED NOT NULL DEFAULT 1 AFTER name');
        $this->db->query('ALTER TABLE users ADD CONSTRAINT users_country_id_foreign FOREIGN KEY (country_id) REFERENCES countries(id)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE users DROP FOREIGN KEY users_country_id_foreign');
        $this->db->query('ALTER TABLE users DROP COLUMN country_id');
    }
}
