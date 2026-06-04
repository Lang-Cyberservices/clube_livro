<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'country_id', 'phone', 'password', 'must_change_password', 'role', 'remember_token', 'remember_token_expires_at'];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [
        'must_change_password' => 'boolean',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByPhone(string $phone): ?array
    {
        return $this->where('phone', $phone)->first();
    }

    public function findByCountryAndPhone(int $countryId, string $phone): ?array
    {
        return $this->where('country_id', $countryId)->where('phone', $phone)->first();
    }

    public function setRememberToken(int $userId, string $hashedToken, string $expiresAt): void
    {
        $this->update($userId, [
            'remember_token'            => $hashedToken,
            'remember_token_expires_at' => $expiresAt,
        ]);
    }

    public function findByRememberToken(string $hashedToken): ?array
    {
        return $this->where('remember_token', $hashedToken)
                    ->where('remember_token_expires_at >', date('Y-m-d H:i:s'))
                    ->first();
    }

    public static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
