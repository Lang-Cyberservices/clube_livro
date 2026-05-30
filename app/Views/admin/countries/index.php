<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Países</h1>
        <p class="text-muted mb-0">Gerencie os países e seus códigos DDI para telefone.</p>
    </div>
    <a href="/admin/paises/new" class="btn btn-primary">Novo país</a>
</div>

<div class="card border-0 p-3 p-lg-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código DDI</th>
                    <th>Nome</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($countries as $country): ?>
                    <tr>
                        <td><?= esc((string) $country['id']); ?></td>
                        <td>+<?= esc($country['code']); ?></td>
                        <td><?= esc($country['name']); ?></td>
                        <td class="text-end">
                            <a href="/admin/paises/<?= $country['id']; ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>
