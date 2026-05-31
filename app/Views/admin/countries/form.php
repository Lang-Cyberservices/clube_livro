<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1"><?= esc($title); ?></h1>
                <p class="text-muted mb-0">Informe o código DDI e o nome do país.</p>
            </div>
            <a href="/admin/paises" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <div class="form-panel rounded-4 p-4 p-lg-5">
            <form method="post" action="<?= esc($action); ?>">
                <?= csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Código DDI</label>
                        <input type="text" name="code" class="form-control" value="<?= old('code', $country['code'] ?? ''); ?>" placeholder="55">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Nome</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name', $country['name'] ?? ''); ?>" placeholder="Brasil">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Máscara do telefone</label>
                        <input type="text" name="phone_mask" class="form-control" value="<?= old('phone_mask', $country['phone_mask'] ?? ''); ?>" placeholder="(##) #-####-####">
                        <small class="text-muted"><code>#</code> representa um dígito. Deixe em branco para usar o formato padrão.</small>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Salvar país</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
