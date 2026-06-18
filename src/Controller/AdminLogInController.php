<?php
namespace App\Controller;

use App\Exception\NotFoundException;

use App\Service\Router;
use App\Service\Templating;

class AdminLogInController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        $html = $templating->render('admin/login.html.php', [
            'router' => $router,
        ]);
        return $html;
    }
}
