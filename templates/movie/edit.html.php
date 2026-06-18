<?php
/** @var \App\Model\Movie $movie */
/** @var \App\Service\Router $router */
/** @var \App\Model\Genre[] $genres */
/** @var \App\Model\Platform[] $platforms */

$title = 'Edytuj film';
$bodyClass = 'index';

ob_start(); ?>
    <div class="movie-admin">
        <h1>Edytuj film</h1>

        <form action="<?= $router->generatePath('movie-edit') ?>" method="post" class="edit-form">
            <?php require __DIR__ . DIRECTORY_SEPARATOR . '_form.html.php'; ?>
            <input type="hidden" name="action" value="movie-edit">
            <input type="hidden" name="id" value="<?= $movie->getId() ?>">
        </form>

        <div class="actions">
            <a href="<?= $router->generatePath('movie-admin') ?>">Powrót do listy filmów</a>
        </div>
    </div>
<?php $main = ob_get_clean();

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
