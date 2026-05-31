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
                        <label class="form-label">País</label>
                        <select name="country_id" class="form-select">
                            <?php foreach ($countries as $country): ?>
                                <option value="<?= $country['id']; ?>" <?= (int) old('country_id', $user['country_id'] ?? 1) === (int) $country['id'] ? 'selected' : ''; ?>>
                                    +<?= esc($country['code']); ?> <?= esc($country['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <?php
                            $selectedCountryId = (int) old('country_id', $user['country_id'] ?? 1);
                            $selectedMask = '';
                            foreach ($countries as $c) {
                                if ((int) $c['id'] === $selectedCountryId) {
                                    $selectedMask = $c['phone_mask'] ?? '';
                                    break;
                                }
                            }
                        ?>
                        <input type="text" name="phone" class="form-control" value="<?= old('phone', format_phone($user['phone'] ?? '', $selectedMask ?: null)); ?>" placeholder="(00) 0-0000-0000" data-phone-mask="<?= esc($selectedMask); ?>">
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
<script>
    (function () {
        var countryMasks = <?= json_encode(array_column($countries, 'phone_mask', 'id')); ?>;
        var countrySelect = document.querySelector('select[name="country_id"]');
        var phoneInput = document.querySelector('input[name="phone"]');

        countrySelect.addEventListener('change', function () {
            var mask = countryMasks[this.value] || '';
            phoneInput.dataset.phoneMask = mask;
            window.reapplyPhoneMask(phoneInput);
        });
    })();
</script>
<?= $this->endSection(); ?>
