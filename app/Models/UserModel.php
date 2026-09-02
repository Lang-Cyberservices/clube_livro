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

    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    /**
     * Busca pelo telefone incluindo usuarios removidos.
     *
     * O indice UNIQUE de phone e global, entao um usuario removido continua
     * ocupando o numero: as checagens de duplicidade precisam enxerga-lo.
     */
    public function findByPhoneWithDeleted(string $phone): ?array
    {
        return $this->withDeleted()->where('phone', $phone)->first();
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

    /**
     * Reativa um usuario removido, sobrescrevendo os dados informados.
     *
     * Usa o query builder direto porque update() filtra por allowedFields e
     * descartaria o deleted_at.
     */
    public function restore(int $id, array $data = []): bool
    {
        $data['deleted_at'] = null;
        $data['updated_at'] = date('Y-m-d H:i:s');

        return $this->db->table($this->table)->where('id', $id)->update($data);
    }

    public function countActiveAdmins(): int
    {
        return (int) $this->where('role', self::ROLE_ADMIN)->countAllResults();
    }

    public static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
