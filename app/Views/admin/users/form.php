<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1"><?= esc($title); ?></h1>
                <p class="text-muted mb-0">Cadastre leitores e administradores com controle de acesso por perfil.</p>
            </div>
            <a href="/admin/users" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <div class="form-panel rounded-4 p-4 p-lg-5">
            <form method="post" action="<?= esc($action); ?>">
                <?= csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name', $user['name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control" value="<?= old('phone', format_phone($user['phone'] ?? '')); ?>" placeholder="(11) 9-4634-2101" data-phone-mask>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Perfil</label>
                        <select name="role" class="form-select">
                            <option value="user" <?= old('role', $user['role'] ?? 'user') === 'user' ? 'selected' : ''; ?>>Usuário</option>
                            <option value="admin" <?= old('role', $user['role'] ?? 'user') === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><?= $user ? 'Nova senha (opcional)' : 'Senha'; ?></label>
                        <input type="text" name="password" class="form-control" value="123mudar4">
                        <small class="text-muted d-block mt-1">Ao definir uma senha, o usuario precisara troca-la no primeiro acesso.</small>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Salvar usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
