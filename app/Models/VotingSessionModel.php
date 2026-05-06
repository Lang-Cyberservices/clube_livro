<?php

namespace App\Models;

use CodeIgniter\Model;

class VotingSessionModel extends Model
{
    public const STATUS_COLLECTING = 'collecting';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_FINISHED = 'finished';

    protected $table            = 'voting_sessions';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'status',
        'created_by',
        'activated_by',
        'finished_by',
        'winning_suggestion_id',
        'activated_at',
        'finished_at',
    ];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getOpenSession(): ?array
    {
        return $this->whereIn('status', [self::STATUS_COLLECTING, self::STATUS_ACTIVE])
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getActiveSession(): ?array
    {
        return $this->where('status', self::STATUS_ACTIVE)
            ->orderBy('id', 'DESC')
            ->first();
    }
}
