<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
        $theme = $_SESSION['theme'] ?? 'default';
        $theme = $theme === 'alt' ? 'contrast' : $theme;
        $themeCss = $theme === 'contrast'
            ? 'contrast.min.css'
            : ($theme === 'light' ? 'light.min.css' : 'style.min.css');
    ?>
    <link rel="stylesheet" href="/assets/dist/<?= $themeCss ?>">
    <title><?= $title ?? 'Plusflix' ?></title>
    <style>
        .app-header .search-pill,
        .app-header .saved-btn {
            cursor: default;
        }
        label {
            color: #eee;
        }
    </style>
</head>
<body <?= isset($bodyClass) ? "class='$bodyClass'" : '' ?>>
<?php
use App\Model\Genre;
use App\Model\Platform;
$genres = Genre::findAll();
$platforms = Platform::findAll();
$q = htmlspecialchars($_GET['q'] ?? '');
$selectedGenres = $_GET['genre'] ?? [];
$selectedPlatforms = $_GET['platform'] ?? [];
?>
<header class="app-header">
    <div class="header-container">


        <a href="<?= $router->generatePath('') ?>" class="logo"><span class="logo-plus">PLUS</span><span class="logo-flix">FLIX</span></a>
        <form action="/index.php" method="GET" class="search-form">
            <input type="hidden" name="action" value="movie-index">
            <div class="search-pill">
                <button type="submit" class="icon-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>
                <input type="text" name="q" placeholder="Szukaj filmów..." class="search-input" value="<?= $q ?>">
                <button type="button" class="icon-btn" id="filter-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg></button>
            </div>
            <div class="filters" id="filters">
                <h4>Gatunki</h4>
                <div class="filter-group">
                    <?php foreach ($genres as $genre): ?>
                        <label><input type="checkbox" name="genre[]" value="<?= $genre->getId() ?>" <?= in_array($genre->getId(), $selectedGenres) ? 'checked' : '' ?>> <?= $genre->getName() ?></label>
                    <?php endforeach; ?>
                </div>
                <h4>Platformy</h4>
                <div class="filter-group">
                    <?php foreach ($platforms as $platform): ?>
                        <label><input type="checkbox" name="platform[]" value="<?= $platform->getId() ?>" <?= in_array($platform->getId(), $selectedPlatforms) ? 'checked' : '' ?>> <?= $platform->getName() ?></label>
                    <?php endforeach; ?>
                </div>
                <button type="submit">Zastosuj filtry</button>
            </div>
        </form>
        <div class="toggle-modes">
            <label for="theme-select">Motyw</label>
            <select id="theme-select" name="theme-select" aria-label="Wybierz motyw">
                <option value="default">Ciemny</option>
                <option value="light">Jasny</option>
                <option value="contrast">Wysoki kontrast</option>
            </select>
        </div>
        <a class="saved-btn" href="<?= $router->generatePath('movie-favorites') ?>" aria-label="Ulubione filmy"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg></a>
    </div>
</header>
<main><?= $main ?? null ?></main>
<footer class="app-footer"><p>© <?= date('Y') ?> PLUSFLIX</p></footer>
<script>
    document.getElementById('filter-btn').addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('filters').classList.toggle('active');
    });

    document.addEventListener('DOMContentLoaded', () => {
        const link = document.querySelector('link[rel="stylesheet"]');
        const themeSelect = document.getElementById('theme-select');

        const applyTheme = (theme) => {
            switch (theme) {
                case 'contrast':
                    link.href = '/assets/dist/contrast.min.css';
                    break;
                case 'light':
                    link.href = '/assets/dist/light.min.css';
                    break;
                default:
                    link.href = '/assets/dist/style.min.css';
            }
        };

        const storedTheme = localStorage.getItem('theme');
        const legacyContrast = localStorage.getItem('contrast-enabled') === 'true';
        const initialTheme = storedTheme || (legacyContrast ? 'contrast' : 'default');

        if (themeSelect) {
            themeSelect.value = initialTheme;
            applyTheme(initialTheme);

            themeSelect.addEventListener('change', (e) => {
                const theme = e.target.value;
                localStorage.setItem('theme', theme);
                if (theme !== 'contrast') {
                    localStorage.setItem('contrast-enabled', 'false');
                }
                applyTheme(theme);
            });
        }
    });
</script>
</body>
</html>
