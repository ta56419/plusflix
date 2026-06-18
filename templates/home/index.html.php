<?php
/** @var \App\Model\Movie[] $topMovies */
/** @var \App\Service\Router $router */
$title = 'PLUSFLIX';
$bodyClass = 'index';
ob_start(); ?>
    <style>
        .platforms-list {
            display: flex;
            transition: transform 0.3s ease-in-out;
        }
    </style>
    <div class="container">
        <section class="hero-section">
            <h1 class="hero-title">Znajdź swój następny film</h1>
            <p class="hero-subtitle">Przeglądaj tysiące filmów i seriali z Netflix, Disney+, HBO i innych platform w jednym miejscu</p>
        </section>
        <section class="platforms-section">
    <h2 class="section-title">Dostępne platformy</h2>
    <div class="platforms-wrapper">
        <button class="nav-arrow left" aria-label="Poprzednie platformy">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
        </button>
        <div class="platforms-scroller">
            <div class="platforms-list">
                <a href="https://www.hbomax.com/pl/pl" class="platform-badge hbo" title="HBO Max">
                    <span class="platform-logo">H</span>
                </a>
                <a href="https://www.skyshowtime.com/pl" class="platform-badge skyshowtime" title="SkyShowtime">
                    <span class="platform-logo">S</span>
                </a>
                <a href="https://www.primevideo.com/" class="platform-badge prime" title="Prime Video">
                    <span class="platform-logo">P</span>
                </a>
                <a href="https://www.netflix.com/pl/" class="platform-badge netflix" title="Netflix">
                    <span class="platform-logo">N</span>
                </a>
                <a href="https://www.disneyplus.com/" class="platform-badge disney" title="Disney+">
                    <span class="platform-logo">D+</span>
                </a>
            </div>
        </div>
        <button class="nav-arrow right" aria-label="Następne platformy">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
        </button>
    </div>
</section>
        <section class="top-list-section">
            <h2 class="section-title">Topowe filmy</h2>
            <div class="movies-list">
                <?php foreach ($topMovies as $index => $movie): ?>
                    <div class="movie-card" onclick="window.location.href='<?= $router->generatePath('movie-show', ['id' => $movie->getId()]) ?>'">
                        <div class="rank-badge">
                            <?php if ($index == 0): ?>
                                <span class="crown-icon">👑</span>
                            <?php endif; ?>
                            <span class="rank-number"><?= $index + 1 ?></span>
                        </div>
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
            </div>
        </section>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.querySelector('.platforms-wrapper');
            const list = document.querySelector('.platforms-list');
            const leftArrow = document.querySelector('.nav-arrow.left');
            const rightArrow = document.querySelector('.nav-arrow.right');
            if (leftArrow && rightArrow && list && wrapper) {
                const itemWidth = list.querySelector('.platform-badge').offsetWidth;
                const gap = parseInt(getComputedStyle(list).gap) || 0;
                const step = itemWidth + gap;
                function shiftLeft() {
                    list.style.transition = 'transform 0.3s ease-in-out';
                    list.style.transform = `translateX(${step}px)`;
                    setTimeout(() => {
                        const last = list.lastElementChild;
                        if (last) {
                            list.insertBefore(last, list.firstElementChild);
                        }
                        list.style.transition = 'none';
                        list.style.transform = 'translateX(0)';
                    }, 300);
                }
                function shiftRight() {
                    list.style.transition = 'transform 0.3s ease-in-out';
                    list.style.transform = `translateX(-${step}px)`;
                    setTimeout(() => {
                        const first = list.firstElementChild;
                        if (first) {
                            list.appendChild(first);
                        }
                        list.style.transition = 'none';
                        list.style.transform = 'translateX(0)';
                    }, 300);
                }
                leftArrow.addEventListener('click', shiftLeft);
                rightArrow.addEventListener('click', shiftRight);
            }
        });
    </script>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
