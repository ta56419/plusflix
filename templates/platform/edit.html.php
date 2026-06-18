<?php

/** @var \App\Model\Platform $platform */
/** @var \App\Service\Router $router */

$title = "Edytuj platformę '{$platform->getName()}'";
$bodyClass = "index";

ob_start(); ?>
    <div class="platform-admin">
        <h1><?= $title ?></h1>

        <form action="<?= $router->generatePath('platform-edit') ?>" method="post" class="edit-form">
            <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
            <input type="hidden" name="action" value="platform-edit">
            <input type="hidden" name="id" value="<?= $platform->getId() ?>">
        </form>

        <div class="actions">
            <a href="<?= $router->generatePath('platform-index') ?>">Powrót</a>
        </div>
    </div>

<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
