<?php
namespace App\Controller;

use App\Model\Movie;
use App\Service\Router;
use App\Service\Templating;

class HomeController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $topMovies = Movie::findTopByRating();

        return $templating->render('home/index.html.php', [
            'topMovies' => $topMovies,
            'router' => $router
        ]);
    }
}