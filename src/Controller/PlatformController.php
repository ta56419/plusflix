<?php
namespace App\Controller;

use App\Exception\NotFoundException;
use App\Model\Platform;
use App\Service\Router;
use App\Service\Templating;

class PlatformController
{
    public function indexAction(Templating $templating, Router $router): ?string
    {
        if (!isset($_COOKIE['role']) || $_COOKIE['role'] !== 'admin') {
            $html = $templating->render('admin/login.html.php', [
                'router' => $router,
            ]);
            return $html;
        }
        $platforms = Platform::findAll();
        $html = $templating->render('platform/index.html.php', [
            'platforms' => $platforms,
            'router' => $router,
        ]);
        return $html;
    }

    public function createAction(?array $requestPost, Templating $templating, Router $router): ?string
    {
        if ($requestPost) {
            $platform = Platform::fromArray($requestPost);
            $platform->save();

            $path = $router->generatePath('platform-index');
            $router->redirect($path);
            return null;
        } else {
            $platform = new Platform();
        }

        $html = $templating->render('platform/create.html.php', [
            'platform' => $platform,
            'router' => $router,
        ]);
        return $html;
    }

    public function editAction(int $platformId, ?array $requestPost, Templating $templating, Router $router): ?string
    {
        $platform = Platform::find($platformId);
        if (! $platform) {
            throw new NotFoundException("Missing platform with id $platformId");
        }

        if ($requestPost) {
            $platform->fill($requestPost);
            $platform->save();

            $path = $router->generatePath('platform-index');
            $router->redirect($path);
            return null;
        }

        $html = $templating->render('platform/edit.html.php', [
            'platform' => $platform,
            'router' => $router,
        ]);
        return $html;
    }

    public function showAction(int $platformId, Templating $templating, Router $router): ?string
    {
        $platform = Platform::find($platformId);
        if (! $platform) {
            throw new NotFoundException("Missing platform with id $platformId");
        }

        $html = $templating->render('platform/show.html.php', [
            'platform' => $platform,
            'router' => $router,
        ]);
        return $html;
    }

    public function deleteAction(int $platformId, Router $router): ?string
    {
        $platform = Platform::find($platformId);
        if (! $platform) {
            throw new NotFoundException("Missing platform with id $platformId");
        }

        $platform->delete();
        $path = $router->generatePath('platform-index');
        $router->redirect($path);
        return null;
    }
}
