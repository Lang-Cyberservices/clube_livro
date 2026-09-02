<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1"><?= esc($title); ?></h1>
                <p class="text-muted mb-0">Marque os membros que votaram neste livro. As alterações valem como voto do membro.</p>
            </div>
            <a href="/admin/votacao" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <div class="form-panel rounded-4 p-4 p-lg-5">
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <img src="<?= esc($suggestion['cover_image'] ?: base_url('img/cover.png')); ?>" onerror="this.onerror=null;this.src='<?= base_url('img/cover.png'); ?>';" alt="Capa de <?= esc($suggestion['title']); ?>" class="book-cover-card">
                </div>
                <div class="col-md-9">
                    <h2 class="mb-1"><?= esc($suggestion['title']); ?></h2>
                    <p class="text-muted mb-2">por <?= esc($suggestion['author']); ?></p>
                    <span class="badge text-bg-dark"><?= esc((string) $suggestion['vote_count']); ?> votos</span>
                    <p class="small text-muted mt-3 mb-0">Sugerido por <?= esc($suggestion['suggested_by']); ?></p>
                </div>
            </div>

            <form method="post" action="/admin/votacao/sugestao/<?= (int) $suggestion['id']; ?>/votos">
                <?= csrf_field(); ?>
                <small class="text-uppercase text-muted fw-semibold">Membros</small>
                <div class="row row-cols-1 row-cols-md-2 g-2 mt-1 mb-4">
                    <?php foreach ($users as $user): ?>
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="user_id[]" value="<?= (int) $user['id']; ?>" id="voter_<?= (int) $user['id']; ?>" <?= in_array((int) $user['id'], $voterIds, true) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="voter_<?= (int) $user['id']; ?>">
                                    <?= esc($user['name']); ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary">Salvar votos</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
