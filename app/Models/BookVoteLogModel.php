<?php

namespace App\Models;

use CodeIgniter\Model;

class BookVoteLogModel extends Model
{
    public const ACTION_ADDED   = 'added';
    public const ACTION_REMOVED = 'removed';
    public const ACTOR_USER     = 'user';
    public const ACTOR_ADMIN    = 'admin';

    protected $table            = 'book_vote_logs';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'session_id',
        'suggestion_id',
        'user_id',
        'action',
        'actor_id',
        'actor_role',
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function log(int $sessionId, int $suggestionId, int $userId, string $action, int $actorId, string $actorRole): void
    {
        $this->insert([
            'session_id'    => $sessionId,
            'suggestion_id' => $suggestionId,
            'user_id'       => $userId,
            'action'        => $action,
            'actor_id'      => $actorId,
            'actor_role'    => $actorRole,
        ]);
    }
}
