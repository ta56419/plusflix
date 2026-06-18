<?php
/** @var \App\Service\Router $router */
$title = 'Panel administracyjny';
$bodyClass = 'index';
ob_start(); ?>
    <div class="admin-panel">
        <h1>Panel administracyjny</h1>
        <div class="actions">
            <a href="<?= $router->generatePath('') ?>">Powrót do strony głównej</a>
        </div>

        <div class="admin-grid">
            <div class="admin-card">
                <div class="card-title">Filmy</div>
                <a href="<?= $router->generatePath('movie-admin') ?>" class="btn-primary">Zarządzaj filmami</a>
            </div>
            <div class="admin-card">
                <div class="card-title">Gatunki</div>
                <a href="<?= $router->generatePath('genre-index') ?>" class="btn-primary">Zarządzaj gatunkami</a>
            </div>
            <div class="admin-card">
                <div class="card-title">Platformy</div>
                <a href="<?= $router->generatePath('platform-index') ?>" class="btn-primary">Zarządzaj platformami</a>
            </div>
        </div>
    </div>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
