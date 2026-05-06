<?php

namespace App\Models;

use CodeIgniter\Model;

class BookSuggestionModel extends Model
{
    protected $table            = 'book_suggestions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_id',
        'user_id',
        'title',
        'author',
        'cover_image',
        'description',
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getSessionSuggestionsWithStats(int $sessionId): array
    {
        return $this->select('book_suggestions.*, users.name AS suggested_by, COUNT(book_votes.id) AS vote_count')
            ->join('users', 'users.id = book_suggestions.user_id')
            ->join('book_votes', 'book_votes.suggestion_id = book_suggestions.id', 'left')
            ->where('book_suggestions.session_id', $sessionId)
            ->groupBy('book_suggestions.id')
            ->orderBy('vote_count', 'DESC')
            ->orderBy('book_suggestions.created_at', 'ASC')
            ->findAll();
    }

    public function countForUserInSession(int $sessionId, int $userId): int
    {
        return $this->where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->countAllResults();
    }
}
