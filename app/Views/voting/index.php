<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="form-panel rounded-4 p-4 h-100">
            <small class="text-uppercase text-muted fw-semibold">Próximo livro</small>
            <h1 class="mb-3">Votação</h1>

            <?php if (! $canManageSuggestions && $session === null): ?>
                <p class="text-muted mb-0">No momento há um livro em andamento e nenhuma votação aberta.</p>
            <?php elseif ($session === null): ?>
                <p class="text-muted mb-0">Ainda não existe um ciclo de votação aberto.</p>
            <?php else: ?>
                <div class="card border-0 p-3 mb-4">
                    <small class="text-uppercase text-muted fw-semibold">Status</small>
                    <strong class="fs-5">
                        <?= $session['status'] === 'active' ? 'Votação ativa' : 'Coleta de sugestões'; ?>
                    </strong>
                    <span class="text-muted">
                        <?= $session['status'] === 'active'
                            ? 'Escolha uma opção e registre seu voto.'
                            : 'Cada usuário pode cadastrar até duas sugestões.'; ?>
                    </span>
                </div>

                <?php if ($session['status'] === 'collecting' && $canManageSuggestions): ?>
                    <div class="alert alert-light border">
                        Você já cadastrou <strong><?= esc((string) $userSuggestionCount); ?></strong> de 2 sugestões neste ciclo.
                    </div>

                    <?php if ($userSuggestionCount < 2): ?>
                        <form id="sug-form" method="post" action="/votacao/sugestoes" enctype="multipart/form-data" novalidate>
                            <?= csrf_field(); ?>
                            <div class="mb-3">
                                <label class="form-label">Título</label>
                                <input type="text" name="title" class="form-control" value="<?= old('title'); ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Autor</label>
                                <input type="text" name="author" class="form-control" value="<?= old('author'); ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Imagem da capa <span class="text-muted fw-normal">— opcional</span></label>
                                <div class="mb-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="cover_source" id="sug_src_url" value="url" checked autocomplete="off">
                                        <label class="btn btn-outline-secondary" for="sug_src_url">Link (URL)</label>
                                        <input type="radio" class="btn-check" name="cover_source" id="sug_src_file" value="file" autocomplete="off">
                                        <label class="btn btn-outline-secondary" for="sug_src_file">Subir imagem</label>
                                    </div>
                                </div>
                                <div id="sug-wrap-url">
                                    <input type="url" name="cover_image_url" class="form-control" placeholder="https://" value="<?= old('cover_image_url'); ?>">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div id="sug-wrap-file" class="d-none">
                                    <input type="file" name="cover_image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                    <div class="invalid-feedback"></div>
                                    <small class="text-muted">JPG, PNG ou WEBP — máx. 5 MB</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Descrição</label>
                                <textarea name="description" rows="5" class="form-control"><?= old('description'); ?></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Cadastrar sugestão</button>
                        </form>
                        <script>
                        (function () {
                            const form = document.getElementById('sug-form');

                            form.querySelectorAll('input[name="cover_source"]').forEach(r =>
                                r.addEventListener('change', () => {
                                    document.getElementById('sug-wrap-url').classList.toggle('d-none', r.value !== 'url');
                                    document.getElementById('sug-wrap-file').classList.toggle('d-none', r.value !== 'file');
                                })
                            );

                            form.addEventListener('submit', function (e) {
                                let ok = true;

                                function check(el, condition, msg) {
                                    el.classList.remove('is-valid', 'is-invalid');
                                    const fb = el.parentElement.querySelector('.invalid-feedback');
                                    if (!condition) {
                                        el.classList.add('is-invalid');
                                        if (fb) fb.textContent = msg;
                                        ok = false;
                                    } else {
                                        el.classList.add('is-valid');
                                    }
                                }

                                const title = form.querySelector('[name="title"]');
                                check(title,
                                    title.value.trim().length >= 3 && title.value.trim().length <= 255,
                                    title.value.trim().length === 0 ? 'Informe o título.' : 'Mínimo 3 caracteres.');

                                const author = form.querySelector('[name="author"]');
                                check(author,
                                    author.value.trim().length >= 3 && author.value.trim().length <= 255,
                                    author.value.trim().length === 0 ? 'Informe o autor.' : 'Mínimo 3 caracteres.');

                                const desc = form.querySelector('[name="description"]');
                                check(desc,
                                    desc.value.trim().length >= 20,
                                    desc.value.trim().length === 0 ? 'Informe a descrição.' : 'Mínimo 20 caracteres.');

                                if (document.getElementById('sug_src_file').checked) {
                                    const fileEl = form.querySelector('[name="cover_image_file"]');
                                    if (fileEl.files.length > 0) {
                                        const f = fileEl.files[0];
                                        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                                        if (!allowed.includes(f.type)) {
                                            check(fileEl, false, 'Use JPG, PNG ou WEBP.');
                                        } else if (f.size > 5 * 1024 * 1024) {
                                            check(fileEl, false, 'Máximo 5 MB.');
                                        } else {
                                            check(fileEl, true, '');
                                        }
                                    }
                                } else {
                                    const urlEl = form.querySelector('[name="cover_image_url"]');
                                    if (urlEl.value.trim() !== '') {
                                        try { new URL(urlEl.value.trim()); check(urlEl, true, ''); }
                                        catch (_) { check(urlEl, false, 'Informe uma URL válida.'); }
                                    }
                                }

                                if (!ok) e.preventDefault();
                            });
                        })();
                        </script>
                    <?php else: ?>
                        <p class="text-muted mb-0">Seu limite de sugestões neste ciclo já foi atingido.</p>
                    <?php endif; ?>
                <?php elseif ($session['status'] === 'active'): ?>
                    <form method="post" action="/votacao/votar">
                        <?= csrf_field(); ?>
                        <div class="d-grid gap-3">
                            <?php foreach ($suggestions as $suggestion): ?>
                                <label class="card border-0 p-3">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="suggestion_id"
                                            value="<?= $suggestion['id']; ?>"
                                            <?= (int) ($userVote['suggestion_id'] ?? 0) === (int) $suggestion['id'] ? 'checked' : ''; ?>
                                        >
                                        <span class="form-check-label fw-semibold"><?= esc($suggestion['title']); ?></span>
                                    </div>
                                    <div class="small text-muted mt-2">por <?= esc($suggestion['author']); ?> • sugerido por <?= esc($suggestion['suggested_by']); ?></div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-3">Registrar voto</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted mb-0">Aguarde a abertura da votação pelo administrador.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="form-panel rounded-4 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <small class="text-uppercase text-muted fw-semibold">Sugestões</small>
                    <h2 class="mb-0">Opções cadastradas</h2>
                </div>
                <?php if ($session && $session['status'] === 'active'): ?>
                    <span class="badge rounded-pill text-bg-dark"><?= count($suggestions); ?> opções</span>
                <?php endif; ?>
            </div>

            <?php if ($suggestions === []): ?>
                <p class="text-muted mb-0">Nenhuma sugestão cadastrada até agora.</p>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($suggestions as $suggestion): ?>
                        <div class="col-md-6">
                            <article class="card border-0 h-100 overflow-hidden">
                                <img src="<?= esc($suggestion['cover_image'] ?: base_url('img/cover.png')); ?>" alt="Capa de <?= esc($suggestion['title']); ?>" class="book-cover-card">
                                <div class="p-4">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <h3 class="h4 mb-1"><?= esc($suggestion['title']); ?></h3>
                                            <p class="text-muted mb-0">por <?= esc($suggestion['author']); ?></p>
                                        </div>
                                        <?php if ($session && $session['status'] === 'active'): ?>
                                            <span class="badge text-bg-secondary"><?= esc((string) $suggestion['vote_count']); ?> votos</span>
                                        <?php endif; ?>
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
