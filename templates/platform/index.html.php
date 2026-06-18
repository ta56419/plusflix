<?php
/** @var \App\Model\Platform[] $platforms */
/** @var \App\Service\Router $router */
$title = 'Platformy';
$bodyClass = 'index';
ob_start(); ?>

    <div class="platform-admin">
        <h1>Platformy</h1>
        <div class="actions">
            <a href="<?= $router->generatePath('admin-index') ?>">Powrót do panelu</a>
            <span>·</span>
            <a href="<?= $router->generatePath('platform-create') ?>">Dodaj nową platformę</a>
        </div>

        <?php if (empty($platforms)): ?>
            <div class="empty">Brak dostępnych platform.</div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th>Nazwa</th>
                    <th>Cena</th>
                    <th>Operacje</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($platforms as $platform): ?>
                    <tr>
                        <td><?= $platform->getName() ?></td>
                        <td><?= $platform->getPrice() ?> zł</td>
                        <td class="ops">
                            <a href="<?= $router->generatePath('platform-edit', ['id' => $platform->getId()]) ?>">Edytuj</a>
                            <a href="<?= $router->generatePath('platform-delete', ['id' => $platform->getId()]) ?>"
                               onclick="return confirm('Czy na pewno chcesz usunąć tę platformę?');">Usuń</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
