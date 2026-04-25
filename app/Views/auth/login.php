<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="form-panel rounded-4 p-4 p-lg-5">
            <h1 class="mb-3">Entrar no clube</h1>
            <p class="text-muted mb-4">Acesse sua conta para comentar, responder e acompanhar o livro atual.</p>

            <form method="post" action="/auth/login">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= old('phone'); ?>" placeholder="(11) 9-4634-2101" data-phone-mask>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
