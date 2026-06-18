<?php
/** @var \App\Model\Movie $movie */
/** @var \App\Model\Genre[] $genres */
/** @var \App\Model\Platform[] $platforms */

$currentGenreIds = array_map(fn($g) => $g->getId(), $movie ? $movie->getGenres() : []);
$currentPlatIds = array_map(fn($p) => $p->getId(), $movie ? $movie->getAvailability() : []);
?>
<div class="movie-form">
    <label>Film
        <input type="text" name="data[title]" placeholder="Tytuł filmu" value="<?= $movie ? $movie->getTitle() : '' ?>" required>
    </label>
    <label>Platformy</label>
    <div class="platform-box" id="filmPlatforms">
        <?php foreach ($platforms as $platform): ?>
            <label>
                <input type="checkbox" name="data[availabilityIds][]" value="<?= $platform->getId() ?>" <?= in_array($platform->getId(), $currentPlatIds) ? 'checked' : '' ?>>
                <?= $platform->getName() ?>
            </label>
        <?php endforeach; ?>
    </div>
    <label>Gatunki filmu</label>
    <div class="genre-box" id="filmGenres">
        <?php foreach ($genres as $genre): ?>
            <label>
                <input type="checkbox" name="data[genreIds][]" value="<?= $genre->getId() ?>" <?= in_array($genre->getId(), $currentGenreIds) ? 'checked' : '' ?>>
                <?= $genre->getName() ?>
            </label>
        <?php endforeach; ?>
    </div>
    <label>Rok produkcji
        <input type="number" name="data[year]" min="1900" max="2099" placeholder="2025" value="<?= $movie ? $movie->getYear() : '' ?>">
    </label>
    <label>Reżyser
        <input type="text" name="data[director]" placeholder="Imię i nazwisko reżysera" value="<?= $movie ? $movie->getDirector() : '' ?>">
    </label>
    <label>Długość (minuty)
        <input type="number" name="data[duration]" min="1" placeholder="120" value="<?= $movie ? $movie->getDuration() : '' ?>">
    </label>

    <label>Opis
        <textarea name="data[description]" rows="3" placeholder="Krótki opis"><?= $movie ? $movie->getDescription() : '' ?></textarea>
    </label>

    <div class="actions">
        <button type="submit" class="btn-primary"><?= ($movie && $movie->getId()) ? 'Zapisz zmiany' : 'Zapisz film' ?></button>
    </div>
</div>
