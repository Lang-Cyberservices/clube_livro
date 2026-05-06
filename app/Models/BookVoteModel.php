<?php

namespace App\Models;

use CodeIgniter\Model;

class BookVoteModel extends Model
{
    protected $table            = 'book_votes';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_id',
        'suggestion_id',
        'user_id',
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findUserVote(int $sessionId, int $userId): ?array
    {
        return $this->where('session_id', $sessionId)
            ->where('user_id', $userId)
            ->first();
    }
}
