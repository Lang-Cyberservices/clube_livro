<?php

namespace App\Models;

use CodeIgniter\Model;

class BookModel extends Model
{
    protected $table            = 'books';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'author',
        'cover_image',
        'description',
        'start_reading_date',
        'scheduled_meeting_date',
        'actual_meeting_date',
        'meeting_happened',
        'is_current',
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [
        'meeting_happened' => 'boolean',
        'is_current'       => 'boolean',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getCurrentBook(): ?array
    {
        return $this->where('is_current', 1)
            ->where('meeting_happened', 0)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getPreviousBooks(): array
    {
        return $this->where('meeting_happened', 1)
            ->orderBy('COALESCE(actual_meeting_date, scheduled_meeting_date)', 'DESC', false)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function setCurrentBook(int $bookId): void
    {
        $this->builder()->set('is_current', 0)->update();
        $this->update($bookId, ['is_current' => 1]);
    }

    public function hasOngoingBook(): bool
    {
        return $this->where('meeting_happened', 0)->countAllResults() > 0;
    }
}
