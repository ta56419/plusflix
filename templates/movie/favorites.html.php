<?php
/** @var \App\Model\Movie[] $movies */
/** @var \App\Service\Router $router */
$title = 'Ulubione filmy';
$bodyClass = 'index';
ob_start(); ?>
<div class="container">
    <h1 class="section-title">Ulubione filmy</h1>
    <div class="actions">
        <a href="<?= $router->generatePath('') ?>">Powrót do strony głównej</a>
    </div>
    <section class="top-list-section">
        <div class="movies-list">
            <?php if (empty($movies)): ?>
                <div class="empty">Brak ulubionych filmów.</div>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card" onclick="window.location.href='<?= $router->generatePath('movie-show', ['id' => $movie->getId()]) ?>'">
                        <div class="movie-info">
                            <h3 class="movie-title">
                                    <?= htmlspecialchars($movie->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                            </h3>
                            <p class="movie-year"><?= htmlspecialchars((string) $movie->getYear(), ENT_QUOTES, 'UTF-8') ?></p>
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
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
