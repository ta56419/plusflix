<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Review;
use App\Service\Router;
use App\Service\Templating;

class ReviewController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $reviews = Review::findAll();
        $html = $templating->render('review/index.html.php', [
            'reviews' => $reviews,
            'router' => $router,
        ]);
        return $html;
    }

    public function deleteAction(int $reviewId, Router $router): ?string
    {
        $review = Review::find($reviewId);
        if (! $review) {
            throw new NotFoundException("Missing review with id $reviewId");
        }

        $review->delete();
        $path = $router->generatePath('review-index');
        $router->redirect($path);
        return null;
    }
}
