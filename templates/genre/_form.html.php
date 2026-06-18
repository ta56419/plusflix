<?php
    /** @var \App\Model\Genre $genre */
?>

<div class="genre-form">
    <label for="name">Nazwa gatunku
        <input type="text" id="name" name="data[name]" value="<?= $genre ? $genre->getName() : '' ?>">
    </label>

    <div class="actions">
        <button type="submit" class="btn-primary">Zapisz</button>
    </div>
</div>
