<?php
    /** @var \App\Model\Platform $platform */
?>
<div class="platform-form">
    <label for="name">Nazwa platformy
        <input type="text" id="name" name="data[name]" value="<?= $platform ? $platform->getName() : '' ?>" placeholder="np. Apple TV+" required>
    </label>
    <label for="price">Cena (zł/mc)
        <input type="number" id="price" name="data[price]" min="0" step="0.01" value="<?= $platform ? $platform->getPrice() : '' ?>" placeholder="39.99" required>
    </label>

    <div class="actions">
        <button type="submit" class="btn-primary">Zapisz</button>
    </div>
</div>
