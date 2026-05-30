<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Gerenciar votação</h1>
        <p class="text-muted mb-0">Inicie a coleta de sugestões, abra a votação e finalize para criar automaticamente o próximo livro.</p>
    </div>
    <a href="/admin" class="btn btn-outline-secondary">Voltar ao painel</a>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="form-panel rounded-4 p-4 h-100">
            <small class="text-uppercase text-muted fw-semibold">Status atual</small>
            <?php if ($session === null): ?>
                <h2 class="mt-2">Sem ciclo aberto</h2>
                <p class="text-muted mb-0">Assim que não houver livro em andamento, as sugestões serão iniciadas automaticamente.</p>
            <?php else: ?>
                <h2 class="mt-2"><?= $session['status'] === 'active' ? 'Votação ativa' : 'Coleta de sugestões'; ?></h2>
                <p class="text-muted">Existem <?= count($suggestions); ?> sugestões neste ciclo.</p>

                <?php if ($session['status'] === 'collecting'): ?>
                    <form method="post" action="/admin/votacao/ativar" class="mb-3">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-primary w-100">Ativar votação</button>
                    </form>
                <?php endif; ?>

                <?php if ($session['status'] === 'active'): ?>
                    <form method="post" action="/admin/votacao/finalizar">
                        <?= csrf_field(); ?>
                        <button type="submit" class="btn btn-danger w-100">Finalizar votação</button>
                    </form>
                    <p class="small text-muted mt-3 mb-0">Ao finalizar, a sugestão mais votada vira o novo livro atual com início hoje e encontro agendado para daqui a 30 dias.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($session !== null && $session['status'] === 'collecting'): ?>
    <div class="col-lg-4">
        <div class="form-panel rounded-4 p-4 h-100">
            <small class="text-uppercase text-muted fw-semibold">Admin</small>
            <h2 class="mb-3">Cadastrar sugestão</h2>
            <form method="post" action="/admin/votacao/sugestao">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Membro</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= esc((string) $user['id']); ?>" <?= (int) old('user_id') === (int) $user['id'] ? 'selected' : ''; ?>>
                                <?= esc($user['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Título</label>
                    <input type="text" name="title" class="form-control" value="<?= old('title'); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Autor</label>
                    <input type="text" name="author" class="form-control" value="<?= old('author'); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Imagem da capa (URL) <span class="text-muted fw-normal">— opcional</span></label>
                    <input type="url" name="cover_image" class="form-control" value="<?= old('cover_image'); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" rows="4" class="form-control"><?= old('description'); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cadastrar sugestão</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?= ($session !== null && $session['status'] === 'collecting') ? 'col-lg-4' : 'col-lg-8'; ?>">
        <div class="form-panel rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <small class="text-uppercase text-muted fw-semibold">Sugestões do ciclo</small>
                    <h2 class="mb-0">Candidatas</h2>
                </div>
                <a href="/votacao" class="btn btn-outline-secondary btn-sm">Ver página pública</a>
            </div>

            <?php if ($suggestions === []): ?>
                <p class="text-muted mb-0">Nenhuma sugestão cadastrada até agora.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($suggestions as $suggestion): ?>
                        <div class="col-md-6">
                            <article class="card border-0 h-100 overflow-hidden">
                                <?php if (! empty($suggestion['cover_image'])): ?>
                                    <img src="<?= esc($suggestion['cover_image']); ?>" alt="Capa de <?= esc($suggestion['title']); ?>" class="book-cover" style="min-height: 260px;">
                                <?php else: ?>
                                    <div class="book-cover d-flex align-items-center justify-content-center bg-light text-muted" style="min-height: 260px;">Sem capa</div>
                                <?php endif; ?>
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <h3 class="h4 mb-1"><?= esc($suggestion['title']); ?></h3>
                                            <p class="text-muted mb-0">por <?= esc($suggestion['author']); ?></p>
                                        </div>
                                        <span class="badge text-bg-dark"><?= esc((string) $suggestion['vote_count']); ?> votos</span>
                                    </div>
                                    <p class="mb-3"><?= esc($suggestion['description']); ?></p>
                                    <small class="text-muted">Sugerido por <?= esc($suggestion['suggested_by']); ?></small>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>
