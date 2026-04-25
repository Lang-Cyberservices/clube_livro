<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<?= view('home/_book_discussion', ['book' => $book, 'comments' => $comments, 'replies' => $replies, 'contextLabel' => 'Livro atual']); ?>
<?= $this->endSection(); ?>
