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
            <form id="adm-sug-form" method="post" action="/admin/votacao/sugestao" enctype="multipart/form-data" novalidate>
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">Membro</label>
                    <select name="user_id" class="form-select">
                        <option value="">Selecione...</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= esc((string) $user['id']); ?>" <?= (int) old('user_id') === (int) $user['id'] ? 'selected' : ''; ?>>
                                <?= esc($user['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
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
                            <input type="radio" class="btn-check" name="cover_source" id="adm_src_url" value="url" checked autocomplete="off">
                            <label class="btn btn-outline-secondary" for="adm_src_url">Link (URL)</label>
                            <input type="radio" class="btn-check" name="cover_source" id="adm_src_file" value="file" autocomplete="off">
                            <label class="btn btn-outline-secondary" for="adm_src_file">Subir imagem</label>
                        </div>
                    </div>
                    <div id="adm-wrap-url">
                        <input type="url" name="cover_image_url" class="form-control" placeholder="https://" value="<?= old('cover_image_url'); ?>">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div id="adm-wrap-file" class="d-none">
                        <input type="file" name="cover_image_file" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                        <div class="invalid-feedback"></div>
                        <small class="text-muted">JPG, PNG ou WEBP — máx. 5 MB</small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="description" rows="4" class="form-control"><?= old('description'); ?></textarea>
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Cadastrar sugestão</button>
            </form>
            <script>
            (function () {
                const form = document.getElementById('adm-sug-form');

                form.querySelectorAll('input[name="cover_source"]').forEach(r =>
                    r.addEventListener('change', () => {
                        document.getElementById('adm-wrap-url').classList.toggle('d-none', r.value !== 'url');
                        document.getElementById('adm-wrap-file').classList.toggle('d-none', r.value !== 'file');
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

                    const userSel = form.querySelector('[name="user_id"]');
                    check(userSel, userSel.value !== '', 'Selecione um membro.');

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

                    if (document.getElementById('adm_src_file').checked) {
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
                                <img src="<?= esc($suggestion['cover_image'] ?: base_url('img/cover.png')); ?>" alt="Capa de <?= esc($suggestion['title']); ?>" class="book-cover-card">
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
