<?php

/** @var \App\Model\Genre $genre */
/** @var \App\Service\Router $router */

$title = 'Utwórz gatunek';
$bodyClass = "index";

ob_start(); ?>
 <div class="platform-admin">
    <h1>Utwórz gatunek</h1>
    <form action="<?= $router->generatePath('genre-create') ?>" method="post" class="edit-form">
        <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
        <input type="hidden" name="action" value="genre-create">
    </form>

    <div class="actions">
        <a href="<?= $router->generatePath('genre-index') ?>">Powrót</a>
    </div>
    </div>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
