<?php if (session('success')): ?>
    <div class="alert alert-success border-0 shadow-sm"><?= esc(session('success')); ?></div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger border-0 shadow-sm"><?= esc(session('error')); ?></div>
<?php endif; ?>

<?php if (session('errors')): ?>
    <div class="alert alert-warning border-0 shadow-sm">
        <strong>Revise os campos:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
