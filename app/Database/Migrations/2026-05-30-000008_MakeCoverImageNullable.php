<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeCoverImageNullable extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('books', [
            'cover_image' => ['type' => 'TEXT', 'null' => true],
        ]);

        $this->forge->modifyColumn('book_suggestions', [
            'cover_image' => ['type' => 'TEXT', 'null' => true],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('books', [
            'cover_image' => ['type' => 'TEXT', 'null' => false],
        ]);

        $this->forge->modifyColumn('book_suggestions', [
            'cover_image' => ['type' => 'TEXT', 'null' => false],
        ]);
    }
}
