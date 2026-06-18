<?php
namespace App\Controller;

use App\Exception\NotFoundException;

use App\Service\Router;
use App\Service\Templating;

class AdminController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
            $html = $templating->render('admin/login.html.php', [
                'router' => $router,
            ]);
            return $html;
        }
        $html = $templating->render('admin/index.html.php', [
            'router' => $router,
        ]);
        return $html;
    }
}
