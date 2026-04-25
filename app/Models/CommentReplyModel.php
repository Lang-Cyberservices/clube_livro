<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentReplyModel extends Model
{
    protected $table            = 'comment_replies';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['comment_id', 'user_id', 'content'];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getRepliesForComments(array $commentIds, ?int $viewerId, bool $meetingHappened): array
    {
        if ($commentIds === []) {
            return [];
        }

        $builder = $this->select('comment_replies.*, users.name as author_name')
            ->join('users', 'users.id = comment_replies.user_id')
            ->whereIn('comment_replies.comment_id', $commentIds)
            ->orderBy('comment_replies.created_at', 'ASC');

        if (! $meetingHappened) {
            $builder->where('comment_replies.user_id', $viewerId ?? 0);
        }

        $replies = $builder->findAll();
        $grouped = [];

        foreach ($replies as $reply) {
            $grouped[$reply['comment_id']][] = $reply;
        }

        return $grouped;
    }
}
