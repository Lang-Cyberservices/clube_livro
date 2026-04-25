<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Painel administrativo</h1>
        <p class="text-muted mb-0">Gerencie livros, usuários e o estado do encontro atual.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/books/new" class="btn btn-primary">Cadastrar livro</a>
        <a href="/admin/books" class="btn btn-outline-secondary">Lista de livros</a>
        <a href="/admin/users" class="btn btn-outline-secondary">Usuários</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card border-0 p-4 h-100">
            <small class="text-muted text-uppercase fw-semibold">Usuários</small>
            <h2 class="mb-0"><?= esc((string) $stats['users']); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 p-4 h-100">
            <small class="text-muted text-uppercase fw-semibold">Livros</small>
            <h2 class="mb-0"><?= esc((string) $stats['books']); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 p-4 h-100">
            <small class="text-muted text-uppercase fw-semibold">Comentários</small>
            <h2 class="mb-0"><?= esc((string) $stats['comments']); ?></h2>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card border-0 p-4 h-100">
            <small class="text-muted text-uppercase fw-semibold">Respostas</small>
            <h2 class="mb-0"><?= esc((string) $stats['replies']); ?></h2>
        </div>
    </div>
</div>

<?php if ($currentBook): ?>
    <div class="card border-0 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <small class="text-muted text-uppercase fw-semibold">Livro atual</small>
                <h2 class="mb-1"><?= esc($currentBook['title']); ?></h2>
                <p class="mb-0 text-muted">
                    <?= $currentBook['meeting_happened'] ? 'Encontro realizado em ' . date('d/m/Y', strtotime($currentBook['actual_meeting_date'])) : 'Encontro previsto para ' . date('d/m/Y', strtotime($currentBook['scheduled_meeting_date'])); ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="/admin/books/new" class="btn btn-primary">Cadastrar novo livro</a>
                <a href="/admin/books/<?= $currentBook['id']; ?>/edit" class="btn btn-outline-secondary">Editar livro atual</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card border-0 p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <small class="text-muted text-uppercase fw-semibold">Livro atual</small>
                <h2 class="mb-1">Nenhum livro em destaque</h2>
                <p class="mb-0 text-muted">Cadastre uma nova leitura para começar o ciclo do clube.</p>
            </div>
            <a href="/admin/books/new" class="btn btn-primary">Cadastrar primeiro livro</a>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection(); ?>
