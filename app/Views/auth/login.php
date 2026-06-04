<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="form-panel rounded-4 p-4 p-lg-5">
            <h1 class="mb-3">Entrar no clube</h1>
            <p class="text-muted mb-4">Acesse sua conta para comentar, responder e acompanhar o livro atual.</p>

            <form method="post" action="/auth/login">
                <?= csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">País</label>
                    <select name="country_id" class="form-select">
                        <?php foreach ($countries as $country): ?>
                            <option value="<?= $country['id']; ?>" <?= (int) old('country_id', 1) === (int) $country['id'] ? 'selected' : ''; ?>>
                                +<?= esc($country['code']); ?> <?= esc($country['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefone</label>
                    <?php
                        $selectedCountryId = (int) old('country_id', 1);
                        $loginSelectedMask = '';
                        foreach ($countries as $c) {
                            if ((int) $c['id'] === $selectedCountryId) {
                                $loginSelectedMask = $c['phone_mask'] ?? '';
                                break;
                            }
                        }
                    ?>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= old('phone'); ?>" placeholder="(00) 9-0000-0000" data-phone-mask="<?= esc($loginSelectedMask); ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Senha</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me" value="1">
                    <label class="form-check-label" for="remember_me">Permanecer conectado</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
        </div>
    </div>
</div>
<script>
    (function () {
        var countryMasks = <?= json_encode(array_column($countries, 'phone_mask', 'id')); ?>;
        var countrySelect = document.querySelector('select[name="country_id"]');
        var phoneInput = document.getElementById('phone');

        countrySelect.addEventListener('change', function () {
            var mask = countryMasks[this.value] || '';
            phoneInput.dataset.phoneMask = mask;
            window.reapplyPhoneMask(phoneInput);
        });
    })();
</script>
<?= $this->endSection(); ?>
