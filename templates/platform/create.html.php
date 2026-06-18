<?php

/** @var \App\Model\Platform $platform */
/** @var \App\Service\Router $router */

$title = 'Utwórz platformę';
$bodyClass = 'index';

ob_start(); ?>
    <div class="platform-admin">
        <h1>Utwórz platformę</h1>

        <form action="<?= $router->generatePath('platform-create') ?>" method="post" class="edit-form">
            <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
            <input type="hidden" name="action" value="platform-create">
        </form>

        <div class="actions">
            <a href="<?= $router->generatePath('platform-index') ?>">Powrót</a>
        </div>
    </div>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
