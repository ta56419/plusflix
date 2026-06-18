<?php
namespace App\Controller;
use App\Exception\NotFoundException;
use App\Model\Movie;
use App\Model\Review;
use App\Service\Router;
use App\Service\Templating;
class MovieController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $q = $_GET['q'] ?? '';
        $genreIds = array_map('intval', $_GET['genre'] ?? []);
        $platformIds = array_map('intval', $_GET['platform'] ?? []);
        $limit = 10;
        if (isset($_GET['limit'])) {
            $candidateLimit = (int) $_GET['limit'];
            if ($candidateLimit >= 1 && $candidateLimit <= 100) {
                $limit = $candidateLimit;
            }
        }
        $movies = Movie::findByCriteria($q, $genreIds, $platformIds, null, $limit);

        $html = $templating->render('movie/index.html.php', [
            'movies' => $movies,
            'router' => $router,
            'currentLimit' => $limit,
        ]);
        return $html;
    }
    public function adminAction(Templating $templating, Router $router): ?string
    {
        if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
            $html = $templating->render('admin/login.html.php', [
                'router' => $router,
            ]);
            return $html;
        }
        $movies = Movie::findAll();
        $html = $templating->render('movie/admin_index.html.php', [
            'movies' => $movies,
            'router' => $router,
        ]);
        return $html;
    }
    public function createAction(?array $requestPost, Templating $templating, Router $router): ?string
    {
        if ($requestPost) {
            $movie = Movie::fromArray($requestPost);
            $movie->save();
            $path = $router->generatePath('movie-admin');
            $router->redirect($path);
            return null;
        } else {
            $movie = new Movie();
        }
        $html = $templating->render('movie/create.html.php', [
            'movie' => $movie,
            'router' => $router,
            'genres' => \App\Model\Genre::findAll(),
            'platforms' => \App\Model\Platform::findAll(),
        ]);
        return $html;
    }
    public function editAction(int $movieId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $movie = Movie::find($movieId);
        if (! $movie) {
            throw new NotFoundException("Missing movie with id $movieId");
        }
        if ($requestPost) {
            $movie->fill($requestPost);
            $movie->save();
            $path = $router->generatePath('movie-admin');
            $router->redirect($path);
            return null;
        }
        $html = $templating->render('movie/edit.html.php', [
            'movie' => $movie,
            'router' => $router,
            'genres' => \App\Model\Genre::findAll(),
            'platforms' => \App\Model\Platform::findAll(),
        ]);
        return $html;
    }
    public function showAction(int $movieId, Templating $templating, Router $router): ?string
    {
        $movie = Movie::find($movieId);
        if (! $movie) {
            throw new NotFoundException("Missing movie with id $movieId");
        }
        $html = $templating->render('movie/show.html.php', [
            'movie' => $movie,
            'router' => $router,
        ]);
        return $html;
    }
    public function deleteAction(int $movieId, Router $router): ?string
    {
        $movie = Movie::find($movieId);
        if (! $movie) {
            throw new NotFoundException("Missing movie with id $movieId");
        }
        $movie->delete();
        $path = $router->generatePath('movie-admin');
        $router->redirect($path);
        return null;
    }
    public function addReviewAction(int $movieId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $movie = Movie::find($movieId);
        if (! $movie) {
            throw new NotFoundException("Missing movie with id $movieId");
        }
        if ($requestPost) {
            if (isset($requestPost['comment'])) {
                $requestPost['comment'] = htmlspecialchars($requestPost['comment'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
            unset($requestPost['id']);
            $review = Review::fromArray($requestPost);
            $movie->addReview($review);
            $path = $router->generatePath('movie-show', ['id' => $movieId]);
            $router->redirect($path);
            return null;
        }
        $html = $templating->render('movie/show.html.php', [
            'movie' => $movie,
            'router' => $router,
        ]);
        return $html;
    }
    public function favoritesAction(Templating $templating, Router $router): ?string
    {
        $favoriteIds = $_SESSION['favorites'] ?? [];
        $movies = Movie::findByCriteria('', [], [], $favoriteIds);
        $html = $templating->render('movie/favorites.html.php', [
            'movies' => $movies,
            'router' => $router,
        ]);
        return $html;
    }
    public function toggleFavoriteAction(int $movieId, Router $router): void
    {
        if (!isset($_SESSION['favorites'])) {
            $_SESSION['favorites'] = [];
        }
        $key = array_search($movieId, $_SESSION['favorites']);
        $isFavorite = false;
        if ($key !== false) {
            unset($_SESSION['favorites'][$key]);
        } else {
            $_SESSION['favorites'][] = $movieId;
            $isFavorite = true;
        }
        if (isset($_REQUEST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'isFavorite' => $isFavorite,
                'buttonText' => $isFavorite ? 'Usuń z ulubionych' : 'Dodaj do ulubionych'
            ]);
            exit;
        } else {
            $referer = $_SERVER['HTTP_REFERER'] ?? $router->generatePath('movie-show', ['id' => $movieId]);
            $router->redirect($referer);
        }
    }
}
