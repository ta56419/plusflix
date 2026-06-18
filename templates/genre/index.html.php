<?php
/** @var \App\Model\Genre[] $genres */
/** @var \App\Service\Router $router */
$title = 'Gatunki filmów';
$bodyClass = 'index';
ob_start(); ?>
    <div class="genre-admin">
        <h1>Gatunki filmowe</h1>
        <div class="actions">
            <a href="<?= $router->generatePath('admin-index') ?>">Powrót do panelu</a>
            <span>·</span>
            <a href="<?= $router->generatePath('genre-create') ?>">Dodaj nowy gatunek</a>
        </div>

        <?php if (empty($genres)): ?>
            <div class="empty">Brak dostępnych gatunków.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Operacje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($genres as $genre): ?>
                    <tr>
                        <td><?= $genre->getName() ?></td>
                        <td class="ops">
                            <a href="<?= $router->generatePath('genre-edit', ['id' => $genre->getId()]) ?>">Edytuj</a>
                            <a href="<?= $router->generatePath('genre-delete', ['id' => $genre->getId()]) ?>"
                               onclick="return confirm('Czy na pewno chcesz usunąć ten gatunek?');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
