<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<section class="hero-panel px-4 py-5 p-lg-5">
    <div class="row justify-content-center text-center">
        <div class="col-lg-8">
            <img src="/img/folhas.png" alt="Folhas ao Vento" class="about-logo mb-4">
            <h1 class="display-4 mb-3">Sobre o grupo</h1>
            <p class="fs-5 text-secondary mb-3">
                O Folhas ao Vento e um grupo de leitura virtual criado por um grupo de amigos que compartilham o amor pela leitura.
            </p>
            <p class="text-muted mb-4">
                Somos bem informais, nos reunimos virtualmente uma vez por mes (mais ou menos) para falar do livro da vez, sempre com bom humor e de forma descontraida.
            </p>
            <p class="mb-2">Caso queira se juntar a nós, entre em contato com Tiago.</p>
            <a href="https://wa.me/5511946342101" target="_blank" rel="noopener noreferrer" class="btn btn-primary px-4">
                WhatsApp: 11 94634-2101
            </a>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
