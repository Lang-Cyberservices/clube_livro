<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMeetingDataToBooks extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE books ADD COLUMN participant_count INT(11) UNSIGNED NULL AFTER actual_meeting_date');
        $this->db->query('ALTER TABLE books ADD COLUMN book_rating DECIMAL(3,1) NULL AFTER participant_count');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE books DROP COLUMN book_rating');
        $this->db->query('ALTER TABLE books DROP COLUMN participant_count');
    }
}
