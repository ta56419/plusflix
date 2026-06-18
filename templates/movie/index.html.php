<?php
/** @var \App\Model\Movie[] $movies */
/** @var \App\Service\Router $router */
$title = 'Lista filmów';
$bodyClass = 'index';
ob_start(); ?>
<div class="container">
    <h1 class="section-title">Lista filmów</h1>

    <div class="actions">
        <a href="<?= $router->generatePath('') ?>">Powrót do strony głównej</a>
    </div>
    
    <div class="list-controls" style="display:flex; gap:15px; align-items:center;">
            <label for="limit-select" style="color:#a0a0b0; font-size:0.9rem;">Wyników na stronę:</label>
            <select id="limit-select" onchange="changeLimit(this.value)" style="padding: 8px 12px; background:#1b1b2f; color:white; border:1px solid #2a2a3e; border-radius:8px; cursor:pointer;">
                <?php foreach ([5, 15, 25] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($currentLimit == $opt) ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <section class="top-list-section">
        <div class="movies-list">
            <?php if (empty($movies)): ?>
                <div class="empty">Brak wyników wyszukiwania.</div>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card" onclick="window.location.href='<?= $router->generatePath('movie-show', ['id' => $movie->getId()]) ?>'">
                        <div class="movie-info">
                            <h3 class="movie-title">
                                    <?= $movie->getTitle() ?>
                            </h3>
                            <p class="movie-year"><?= $movie->getYear() ?></p>
                        </div>
                        <div class="movie-platform">
                            <?php foreach ($movie->getAvailability() as $platform):
                                $clean_name = preg_replace('/[^a-z0-9]/', '', strtolower($platform->getName()));
                                $display_text = ($clean_name === 'disney') ? 'D+' : strtoupper(substr($clean_name, 0, 1));
                                $url = match($clean_name) {
                                    'hbo' => 'https://www.hbomax.com/pl/pl',
                                    'skyshowtime' => 'https://www.skyshowtime.com/pl',
                                    'prime' => 'https://www.primevideo.com/',
                                    'netflix' => 'https://www.netflix.com/pl/',
                                    'disney' => 'https://www.disneyplus.com/',
                                    default => '#',
                                };
                            ?>
                                <a href="<?= $url ?>" onclick="event.stopPropagation()" class="platform-badge <?= htmlspecialchars($clean_name, ENT_QUOTES, 'UTF-8') ?>" title="<?= htmlspecialchars($platform->getName(), ENT_QUOTES, 'UTF-8') ?>">
                                    <span class="platform-logo"><?= htmlspecialchars($display_text, ENT_QUOTES, 'UTF-8') ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
<script>
function changeLimit(newLimit){
    const url = new URL(window.location.href);
    url.searchParams.set('limit', newLimit);
    window.location.href = url.toString();
}    
</script>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
