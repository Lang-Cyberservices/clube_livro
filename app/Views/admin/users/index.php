<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Usuários</h1>
        <p class="text-muted mb-0">Gerencie perfis administrativos e leitores do clube.</p>
    </div>
    <a href="/admin/users/new" class="btn btn-primary">Novo usuário</a>
</div>

<div class="card border-0 p-3 p-lg-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Perfil</th>
                    <th>Primeiro acesso</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= esc($user['name']); ?></td>
                        <td><?= esc(format_phone($user['phone'], $countriesById[$user['country_id']]['phone_mask'] ?? null)); ?></td>
                        <td><span class="badge <?= $user['role'] === 'admin' ? 'text-bg-dark' : 'text-bg-secondary'; ?>"><?= esc($user['role']); ?></span></td>
                        <td><?= ! empty($user['must_change_password']) ? 'Pendente' : 'Concluido'; ?></td>
                        <td class="text-end">
                            <a href="/admin/users/<?= $user['id']; ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>
