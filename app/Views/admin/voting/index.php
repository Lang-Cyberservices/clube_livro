<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="mb-1">Gerenciar votação</h1>
        <p class="text-muted mb-0">Abra a votação quando as sugestões estiverem prontas e finalize para criar automaticamente o próximo livro.</p>
    </div>
    <a href="/admin" class="btn btn-outline-secondary">Voltar ao painel</a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="form-panel rounded-4 p-4 h-100">
            <small class="text-uppercase text-muted fw-semibold">Status atual</small>
            <?php if ($session === null): ?>
                <h2 class="mt-2">Sem ciclo aberto</h2>
                <p class="text-muted mb-0">Assim que não houver livro em andamento, as sugestões poderão ser iniciadas.</p>
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

    <div class="col-lg-8">
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
                                <img src="<?= esc($suggestion['cover_image']); ?>" alt="Capa de <?= esc($suggestion['title']); ?>" class="book-cover" style="min-height: 260px;">
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
