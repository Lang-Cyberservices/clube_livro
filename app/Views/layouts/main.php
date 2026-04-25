<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Clube do Livro'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;700&family=Manrope:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --club-bg: #f6efe6;
            --club-paper: #fffaf4;
            --club-ink: #2f241f;
            --club-accent: #b85c38;
            --club-accent-dark: #8d4124;
            --club-border: rgba(47, 36, 31, 0.12);
        }
        body {
            background:
                radial-gradient(circle at top right, rgba(184, 92, 56, 0.12), transparent 28%),
                linear-gradient(180deg, #fcf7f0 0%, var(--club-bg) 100%);
            color: var(--club-ink);
            font-family: 'Manrope', sans-serif;
            min-height: 100vh;
        }
        h1, h2, h3, h4, .brand-title {
            font-family: 'Cormorant Garamond', serif;
        }
        .navbar,
        .card,
        .hero-panel,
        .form-panel {
            background: rgba(255, 250, 244, 0.94);
            backdrop-filter: blur(10px);
            border: 1px solid var(--club-border);
            box-shadow: 0 18px 45px rgba(47, 36, 31, 0.08);
        }
        .navbar-brand {
            font-size: 1.75rem;
            letter-spacing: 0.04em;
        }
        .btn-primary {
            background-color: var(--club-accent);
            border-color: var(--club-accent);
        }
        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--club-accent-dark);
            border-color: var(--club-accent-dark);
        }
        .hero-panel {
            border-radius: 28px;
            overflow: hidden;
        }
        .book-cover {
            width: 100%;
            height: 100%;
            min-height: 420px;
            object-fit: cover;
        }
        .comment-card,
        .reply-card {
            border-radius: 18px;
            border: 1px solid var(--club-border);
            background: rgba(255, 255, 255, 0.8);
        }
        .reply-card {
            margin-left: 1.5rem;
            border-left: 4px solid rgba(184, 92, 56, 0.3);
        }
        .stat-card {
            border-radius: 22px;
        }
        .table thead th {
            color: #6d564c;
            font-weight: 700;
        }
        .about-logo {
            width: min(240px, 60vw);
            height: auto;
            display: block;
            margin-inline: auto;
        }
        .previous-book-item {
            background: transparent;
            border: 0;
            border-bottom: 1px solid var(--club-border);
        }
        .previous-book-item:last-child {
            border-bottom: 0;
        }
        .previous-book-item:hover {
            background: rgba(184, 92, 56, 0.06);
        }
    </style>
</head>
<body>
    <?= $this->include('partials/navbar'); ?>

    <main class="container py-4 py-lg-5">
        <?= $this->include('partials/flash'); ?>
        <?= $this->renderSection('content'); ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('[data-phone-mask]').forEach(function (input) {
            function applyPhoneMask(value) {
                var digits = value.replace(/\D/g, '').slice(0, 11);

                if (digits.length === 0) {
                    return '';
                }

                if (digits.length <= 2) {
                    return '(' + digits;
                }

                if (digits.length <= 3) {
                    return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
                }

                if (digits.length <= 7) {
                    return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 3) + '-' + digits.slice(3);
                }

                return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 3) + '-' + digits.slice(3, 7) + '-' + digits.slice(7, 11);
            }

            input.addEventListener('input', function () {
                input.value = applyPhoneMask(input.value);
            });

            input.value = applyPhoneMask(input.value);
        });
    </script>
</body>
</html>
