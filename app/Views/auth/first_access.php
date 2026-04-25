<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="form-panel rounded-4 p-4 p-lg-5">
            <h1 class="mb-3">Troque sua senha</h1>
            <p class="text-muted mb-4">No primeiro acesso, a troca de senha e obrigatoria antes de continuar.</p>

            <form method="post" action="/auth/primeiro-acesso">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label for="password" class="form-label">Nova senha</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar nova senha</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Salvar nova senha</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
