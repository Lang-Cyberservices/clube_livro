<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<section class="hero-panel p-4 p-lg-5">
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
        <div>
            <span class="badge text-bg-dark mb-2">Arquivo do clube</span>
            <h1 class="display-5 mb-2">Livros anteriores</h1>
            <p class="text-muted mb-0">Selecione um livro para ver as datas do encontro e os comentarios da leitura.</p>
        </div>
    </div>

    <?php if ($books === []): ?>
        <p class="text-muted mb-0">Ainda nao ha livros anteriores cadastrados.</p>
    <?php else: ?>
        <div class="list-group list-group-flush">
            <?php foreach ($books as $book): ?>
                <?php $meetingDate = $book['meeting_happened'] && ! empty($book['actual_meeting_date']) ? $book['actual_meeting_date'] : $book['scheduled_meeting_date']; ?>
                <a href="/livros/<?= $book['id']; ?>" class="list-group-item list-group-item-action previous-book-item px-0 py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
                        <div>
                            <h2 class="h3 mb-1"><?= esc($book['title']); ?></h2>
                            <?php if (! empty($book['author'])): ?>
                                <p class="small text-uppercase text-muted fw-semibold mb-2">por <?= esc($book['author']); ?></p>
                            <?php endif; ?>
                            <p class="text-muted mb-0"><?= esc($book['description']); ?></p>
                        </div>
                        <div class="text-md-end">
                            <div class="small text-uppercase text-muted fw-semibold">Data do encontro</div>
                            <strong><?= date('d/m/Y', strtotime($meetingDate)); ?></strong>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?= $this->endSection(); ?>
