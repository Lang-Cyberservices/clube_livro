<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1"><?= esc($title); ?></h1>
                <p class="text-muted mb-0">Ao marcar o encontro como realizado, a data real passa a ser exibida publicamente e os comentários se tornam visíveis para todos os usuários logados.</p>
            </div>
            <a href="/admin/books" class="btn btn-outline-secondary">Voltar</a>
        </div>

        <div class="form-panel rounded-4 p-4 p-lg-5">
            <form method="post" action="<?= esc($action); ?>">
                <?= csrf_field(); ?>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Título</label>
                        <input type="text" name="title" class="form-control" value="<?= old('title', $book['title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Autor</label>
                        <input type="text" name="author" class="form-control" value="<?= old('author', $book['author'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Capa do livro (URL)</label>
                        <input type="url" name="cover_image" class="form-control" value="<?= old('cover_image', $book['cover_image'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sinopse</label>
                        <textarea name="description" rows="5" class="form-control"><?= old('description', $book['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data inicial da leitura</label>
                        <input type="date" name="start_reading_date" class="form-control" value="<?= old('start_reading_date', $book['start_reading_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data prevista do encontro</label>
                        <input type="date" name="scheduled_meeting_date" class="form-control" value="<?= old('scheduled_meeting_date', $book['scheduled_meeting_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data real do encontro</label>
                        <input type="date" name="actual_meeting_date" class="form-control" value="<?= old('actual_meeting_date', $book['actual_meeting_date'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="meeting_happened" name="meeting_happened" value="1" <?= old('meeting_happened', $book['meeting_happened'] ?? 0) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="meeting_happened">Encontro já aconteceu</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check form-switch mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_current" name="is_current" value="1" <?= old('is_current', $book['is_current'] ?? ($book === null ? 1 : 0)) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_current">Definir como livro atual</label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Salvar livro</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
