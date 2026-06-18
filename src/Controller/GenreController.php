<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Genre;
use App\Service\Router;
use App\Service\Templating;

class GenreController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $genres = Genre::findAll();
        if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
            $html = $templating->render('admin/login.html.php', [
                'router' => $router,
            ]);
            return $html;
        }
        $html = $templating->render('genre/index.html.php', [
            'genres' => $genres,
            'router' => $router,
        ]);
        return $html;
    }

    public function createAction(?array $requestPost, Templating $templating, Router $router): ?string
    {
        if ($requestPost) {
            $genre = Genre::fromArray($requestPost);
            $genre->save();

            $path = $router->generatePath('genre-index');
            $router->redirect($path);
            return null;
        } else {
            $genre = new Genre();
        }

        $html = $templating->render('genre/create.html.php', [
            'genre' => $genre,
            'router' => $router,
        ]);
        return $html;
    }

    public function editAction(int $genreId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $genre = Genre::find($genreId);
        if (! $genre) {
            throw new NotFoundException("Missing genre with id $genreId");
        }

        if ($requestPost) {
            $genre->fill($requestPost);
            $genre->save();

            $path = $router->generatePath('genre-index');
            $router->redirect($path);
            return null;
        }

        $html = $templating->render('genre/edit.html.php', [
            'genre' => $genre,
            'router' => $router,
        ]);
        return $html;
    }

    public function showAction(int $genreId, Templating $templating, Router $router): ?string
    {
        $genre = Genre::find($genreId);
        if (! $genre) {
            throw new NotFoundException("Missing genre with id $genreId");
        }

        $html = $templating->render('genre/show.html.php', [
            'genre' => $genre,
            'router' => $router,
        ]);
        return $html;
    }

    public function deleteAction(int $genreId, Router $router): ?string
    {
        $genre = Genre::find($genreId);
        if (! $genre) {
            throw new NotFoundException("Missing genre with id $genreId");
        }

        $genre->delete();
        $path = $router->generatePath('genre-index');
        $router->redirect($path);
        return null;
    }
}
