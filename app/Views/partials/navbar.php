<nav class="navbar navbar-expand-lg sticky-top mt-3 mx-3 rounded-4">
    <div class="container">
        <a class="navbar-brand brand-title fw-bold text-dark" href="/">Folhas ao Vento</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <li class="nav-item">
                    <a class="nav-link" href="/">Livro atual</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/livros-anteriores">Livros anteriores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/sobre">Sobre</a>
                </li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/perfil">Meu cadastro</a>
                    </li>
                    <?php if (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/admin">Painel admin</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <span class="nav-link text-muted">Olá, <?= esc(current_user()['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3" href="/logout">Sair</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-primary btn-sm px-3" href="/auth/login">Entrar</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
