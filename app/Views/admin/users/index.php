<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Usuários</h1>
        <p class="text-muted mb-0">Gerencie perfis administrativos e leitores do clube.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= $showDeleted ? '/admin/users' : '/admin/users?show_deleted=1'; ?>" class="btn <?= $showDeleted ? 'btn-secondary' : 'btn-outline-secondary'; ?>">
            <?= $showDeleted ? 'Ocultar removidos' : 'Mostrar removidos'; ?>
        </a>
        <a href="/admin/users/new" class="btn btn-primary">Novo usuário</a>
    </div>
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
                    <?php $isDeleted = ! empty($user['deleted_at']); ?>
                    <tr<?= $isDeleted ? ' class="opacity-50"' : ''; ?>>
                        <td><?= esc($user['name']); ?></td>
                        <td><?= esc(format_phone($user['phone'], $countriesById[$user['country_id']]['phone_mask'] ?? null)); ?></td>
                        <td>
                            <span class="badge <?= $user['role'] === 'admin' ? 'text-bg-dark' : 'text-bg-secondary'; ?>"><?= esc($user['role']); ?></span>
                            <?php if ($isDeleted): ?>
                                <span class="badge text-bg-danger">Removido</span>
                            <?php endif; ?>
                        </td>
                        <td><?= ! empty($user['must_change_password']) ? 'Pendente' : 'Concluido'; ?></td>
                        <td class="text-end">
                            <?php if ($isDeleted): ?>
                                <form method="post" action="/admin/users/<?= (int) $user['id']; ?>/restore" class="d-inline">
                                    <?= csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-success">Reativar</button>
                                </form>
                            <?php else: ?>
                                <a href="/admin/users/<?= $user['id']; ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php if ((int) $user['id'] !== 1 && (int) $user['id'] !== (int) current_user_id()): ?>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger js-delete-user"
                                            data-user-id="<?= (int) $user['id']; ?>"
                                            data-user-name="<?= esc($user['name'], 'attr'); ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteUserModal">Apagar</button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="" class="modal-content" id="deleteUserForm">
            <?= csrf_field(); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Apagar usuário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Remover <strong id="deleteUserName"></strong> do clube?</p>
                <small class="text-muted d-block">Ele deixa de aparecer nas listagens e não consegue mais entrar. Comentários e votos anteriores continuam visíveis, e o cadastro pode ser reativado depois.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Apagar usuário</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var form = document.getElementById('deleteUserForm');
        var nameHolder = document.getElementById('deleteUserName');

        document.querySelectorAll('.js-delete-user').forEach(function (button) {
            button.addEventListener('click', function () {
                form.action = '/admin/users/' + this.dataset.userId + '/delete';
                nameHolder.textContent = this.dataset.userName;
            });
        });
    })();
</script>
<?= $this->endSection(); ?>
