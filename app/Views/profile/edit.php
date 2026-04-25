<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Meu cadastro</h1>
                <p class="text-muted mb-0">Atualize seus dados de acesso e mantenha seu telefone sempre correto.</p>
            </div>
        </div>

        <div class="form-panel rounded-4 p-4 p-lg-5">
            <form method="post" action="/perfil">
                <?= csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name', $user['name']); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telefone</label>
                        <input type="text" name="phone" class="form-control" value="<?= old('phone', format_phone($user['phone'])); ?>" placeholder="(11) 9-4634-2101" data-phone-mask>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nova senha</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Confirmar nova senha</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Salvar alterações</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
