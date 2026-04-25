<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="mb-4">
    <a href="/livros-anteriores" class="btn btn-outline-secondary btn-sm">Voltar para livros anteriores</a>
</div>

<?= view('home/_book_discussion', ['book' => $book, 'comments' => $comments, 'replies' => $replies, 'contextLabel' => 'Livro do arquivo']); ?>
<?= $this->endSection(); ?>
