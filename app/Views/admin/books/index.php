<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Livros</h1>
        <p class="text-muted mb-0">Cadastre, edite e defina qual leitura está em destaque.</p>
    </div>
    <a href="/admin/books/new" class="btn btn-primary">Cadastrar livro</a>
</div>

<div class="card border-0 p-3 p-lg-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Leitura</th>
                    <th>Encontro</th>
                    <th>Status</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td>
                            <strong><?= esc($book['title']); ?></strong>
                            <?php if ($book['is_current']): ?>
                                <span class="badge text-bg-dark ms-2">Atual</span>
                            <?php endif; ?>
                        </td>
                        <td><?= esc($book['author'] ?? '-'); ?></td>
                        <td><?= date('d/m/Y', strtotime($book['start_reading_date'])); ?></td>
                        <td><?= date('d/m/Y', strtotime($book['meeting_happened'] ? $book['actual_meeting_date'] : $book['scheduled_meeting_date'])); ?></td>
                        <td><?= $book['meeting_happened'] ? 'Realizado' : 'Pendente'; ?></td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="/admin/books/<?= $book['id']; ?>/edit" class="btn btn-sm btn-outline-secondary">Editar</a>
                                <?php if (! $book['is_current']): ?>
                                    <form method="post" action="/admin/books/<?= $book['id']; ?>/highlight">
                                        <?= csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-primary">Destacar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection(); ?>
