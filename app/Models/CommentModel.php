<?php

namespace App\Models;

use CodeIgniter\Model;

class CommentModel extends Model
{
    protected $table            = 'comments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['book_id', 'user_id', 'content'];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getVisibleComments(int $bookId, ?int $viewerId, bool $meetingHappened): array
    {
        $builder = $this->select('comments.*, users.name as author_name')
            ->join('users', 'users.id = comments.user_id')
            ->where('comments.book_id', $bookId)
            ->orderBy('comments.created_at', 'ASC');

        if (! $meetingHappened) {
            $builder->where('comments.user_id', $viewerId ?? 0);
        }

        return $builder->findAll();
    }
}
