<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeBookVotesUniqueKey extends Migration
{
    public function up()
    {
        $result = $this->db->query("
            SELECT DISTINCT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'book_votes'
              AND NON_UNIQUE = 0
              AND INDEX_NAME != 'PRIMARY'
        ");

        foreach ($result->getResultArray() as $row) {
            $this->db->query("ALTER TABLE `book_votes` DROP INDEX `{$row['INDEX_NAME']}`");
        }

        $this->db->query('ALTER TABLE `book_votes` ADD UNIQUE KEY `book_votes_session_user_suggestion` (`session_id`, `user_id`, `suggestion_id`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `book_votes` DROP INDEX IF EXISTS `book_votes_session_user_suggestion`');
        $this->db->query('ALTER TABLE `book_votes` ADD UNIQUE KEY `book_votes_session_id_user_id` (`session_id`, `user_id`)');
    }
}
