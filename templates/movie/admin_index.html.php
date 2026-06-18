<?php
/** @var \App\Model\Movie[] $movies */
/** @var \App\Service\Router $router */
$title = 'Zarządzanie filmami';
$bodyClass = 'index';
ob_start(); ?>

    <div class="movie-admin">
        <h1>Zarządzanie filmami</h1>
        <div class="actions">
            <a href="<?= $router->generatePath('admin-index') ?>">Powrót do panelu</a>
            <span>·</span>
            <a href="<?= $router->generatePath('movie-create') ?>">Dodaj nowy film</a>
        </div>

        <table id="filmTable">
            <thead>
            <tr>
                <th>Tytuł</th>
                <th>Platforma</th>
                <th>Gatunki</th>
                <th>Rok</th>
                <th>Operacje</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($movies as $movie): ?>
                <tr>
                    <td><?= htmlspecialchars($movie->getTitle(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                            $names = array_map(fn($p) => htmlspecialchars($p->getName(), ENT_QUOTES, 'UTF-8'), $movie->getAvailability());
                            echo implode(', ', $names);
                        ?>
                    </td>
                    <td>
                        <?php
                            $gNames = array_map(fn($g) => htmlspecialchars($g->getName(), ENT_QUOTES, 'UTF-8'), $movie->getGenres());
                            echo implode(', ', $gNames);
                        ?>
                    </td>
                    <td><?= htmlspecialchars((string) $movie->getYear(), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="ops">
                        <a href="<?= $router->generatePath('review-admin-list', ['movieId' => $movie->getId()]) ?>">Komentarze</a>
                        <a href="<?= $router->generatePath('movie-edit', ['id' => $movie->getId()]) ?>">Edytuj</a>
                        <a href="<?= $router->generatePath('movie-delete', ['id' => $movie->getId()]) ?>"
                           onclick="return confirm('Usunąć film?');">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
