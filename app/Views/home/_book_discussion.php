<?php if ($book === null): ?>
    <div class="hero-panel p-5 text-center">
        <h1 class="display-5 mb-3">Nenhum livro em destaque no momento</h1>
        <p class="lead text-muted mb-0">Assim que o administrador cadastrar uma leitura atual, ela aparecerá aqui.</p>
    </div>
<?php else: ?>
    <section class="hero-panel p-0 mb-5">
        <div class="row g-0 align-items-stretch">
            <div class="col-lg-4">
                <img src="<?= esc($book['cover_image'] ?: base_url('img/cover.png')); ?>" alt="Capa do livro <?= esc($book['title']); ?>" class="book-cover">
            </div>
            <div class="col-lg-8 p-4 p-lg-5">
                <?php if (! empty($contextLabel)): ?>
                    <span class="badge text-bg-dark mb-3"><?= esc($contextLabel); ?></span>
                <?php endif; ?>
                <h1 class="display-4 mb-3"><?= esc($book['title']); ?></h1>
                <?php if (! empty($book['author'])): ?>
                    <p class="text-uppercase text-muted fw-semibold mb-3">por <?= esc($book['author']); ?></p>
                <?php endif; ?>
                <p class="fs-5 text-secondary mb-4"><?= esc($book['description']); ?></p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 p-3">
                            <small class="text-uppercase text-muted fw-semibold">Início da leitura</small>
                            <strong><?= date('d/m/Y', strtotime($book['start_reading_date'])); ?></strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 p-3">
                            <small class="text-uppercase text-muted fw-semibold">
                                <?= $book['meeting_happened'] ? 'Data do encontro' : 'Data prevista do encontro'; ?>
                            </small>
                            <strong>
                                <?= date('d/m/Y', strtotime($book['meeting_happened'] ? $book['actual_meeting_date'] : $book['scheduled_meeting_date'])); ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 p-3">
                            <small class="text-uppercase text-muted fw-semibold">Status do encontro</small>
                            <strong><?= $book['meeting_happened'] ? 'Realizado' : 'Aguardando'; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="row g-4">
        <div class="col-lg-5">
            <div class="form-panel rounded-4 p-4 h-100">
                <h2 class="mb-3">Discussão do livro</h2>
                <?php if (! is_logged_in()): ?>
                    <p class="text-muted mb-4">Faça login para comentar e acompanhar as respostas.</p>
                    <a href="/auth/login" class="btn btn-primary">Entrar para comentar</a>
                <?php else: ?>
                    <?php if (! $book['meeting_happened']): ?>
                        <div class="alert alert-light border mb-4">
                            Antes do encontro, cada leitor visualiza apenas os próprios comentários e respostas.
                        </div>
                    <?php endif; ?>
                    <form method="post" action="/comments">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="book_id" value="<?= esc($book['id']); ?>">
                        <div class="mb-3">
                            <label for="content" class="form-label">Seu comentário</label>
                            <textarea name="content" id="content" rows="5" class="form-control" placeholder="Compartilhe uma ideia, reflexão ou pergunta..."><?= old('content'); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Publicar comentário</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="form-panel rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0">Comentários</h2>
                    <?php if (is_logged_in()): ?>
                        <span class="badge rounded-pill text-bg-secondary"><?= count($comments); ?> visíveis para você</span>
                    <?php endif; ?>
                </div>

                <?php if (! is_logged_in()): ?>
                    <p class="text-muted mb-0">Os comentários ficam disponíveis para usuários autenticados.</p>
                <?php elseif ($comments === []): ?>
                    <p class="text-muted mb-0">Nenhum comentário disponível para exibição neste momento.</p>
                <?php else: ?>
                    <div class="d-grid gap-3">
                        <?php foreach ($comments as $comment): ?>
                            <article class="comment-card p-3 p-lg-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong><?= esc($comment['author_name']); ?></strong>
                                        <div class="text-muted small"><?= date('d/m/Y H:i', strtotime($comment['created_at'])); ?></div>
                                    </div>
                                </div>
                                <p class="mb-3"><?= nl2br(esc($comment['content'])); ?></p>

                                <form method="post" action="/comments/<?= $comment['id']; ?>/replies" class="mb-3">
                                    <?= csrf_field(); ?>
                                    <label class="form-label small">Responder comentário</label>
                                    <div class="input-group">
                                        <input type="text" name="content" class="form-control" placeholder="Escreva sua resposta">
                                        <button class="btn btn-outline-secondary" type="submit">Responder</button>
                                    </div>
                                </form>

                                <?php foreach ($replies[$comment['id']] ?? [] as $reply): ?>
                                    <div class="reply-card p-3 mt-3">
                                        <strong><?= esc($reply['author_name']); ?></strong>
                                        <div class="text-muted small mb-2"><?= date('d/m/Y H:i', strtotime($reply['created_at'])); ?></div>
                                        <p class="mb-0"><?= nl2br(esc($reply['content'])); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
