<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<?php if ($book === null && is_logged_in() && $votingSession !== null): ?>
    <div class="card border-0 p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <small class="text-uppercase text-muted fw-semibold">Próxima leitura</small>
                <h2 class="mb-1">A escolha do próximo livro já começou</h2>
                <p class="mb-0 text-muted">
                    <?= $votingSession['status'] === 'active'
                        ? 'A votação está aberta para os usuários logados.'
                        : 'Estamos coletando sugestões antes da abertura da votação.'; ?>
                </p>
            </div>
            <a href="/votacao" class="btn btn-primary">Ir para votação</a>
        </div>
    </div>
<?php endif; ?>
<?= view('home/_book_discussion', ['book' => $book, 'comments' => $comments, 'replies' => $replies, 'contextLabel' => 'Livro atual']); ?>
<?= $this->endSection(); ?>
