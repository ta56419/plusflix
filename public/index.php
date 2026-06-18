<?php
session_start();
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php';
$config = new \App\Service\Config();
$templating = new \App\Service\Templating();
$router = new \App\Service\Router();
$action = $_REQUEST['action'] ?? null;
$actionData = getActionData($action);
$actionModel = getActionModel($actionData);
$actionType = getActionType($actionData);
switch ($actionType) {
    case null:
        $controller = new \App\Controller\HomeController();
        $view = $controller->indexAction($templating, $router);
        break;
    case 'index':
        $controller = getController($actionModel);
        $view = $controller->indexAction($templating, $router);
        break;
    case 'create':
        $controller = getController($actionModel);
        $view = $controller->createAction($_REQUEST['data'] ?? null, $templating, $router);
        break;
    case 'edit':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = getController($actionModel);
        $view = $controller->editAction($_REQUEST['id'], $_REQUEST['data'] ?? null, $templating, $router);
        break;
    case 'show':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = getController($actionModel);
        $view = $controller->showAction($_REQUEST['id'], $templating, $router);
        break;
    case 'delete':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = getController($actionModel);
        $view = $controller->deleteAction($_REQUEST['id'], $router);
        break;
    case 'admin':
        $actionSubtype = $actionData[2] ?? null;
        if ($actionSubtype === 'list') {
            if (! $_REQUEST['movieId']) {
                break;
            }
            $controller = getController($actionModel);
            if (method_exists($controller, 'adminListAction')) {
                $view = $controller->adminListAction($_REQUEST['movieId'], $templating, $router);
            }
        } else {
            $controller = getController($actionModel);
            if (method_exists($controller, 'adminAction')) {
                $view = $controller->adminAction($templating, $router);
            } else {
                $view = $controller->indexAction($templating, $router);
            }
        }
        break;
    case 'addReview':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = getController($actionModel);
        if (method_exists($controller, 'addReviewAction')) {
            $view = $controller->addReviewAction($_REQUEST['id'], $_REQUEST, $templating, $router);
        }
        break;
    case 'favorites':
        $controller = getController($actionModel);
        $view = $controller->favoritesAction($templating, $router);
        break;
    case 'toggleFavorite':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = getController($actionModel);
        $controller->toggleFavoriteAction($_REQUEST['id'], $router);
        break;
    default:
        $controller = new \App\Controller\HomeController();
        $view = $controller->indexAction($templating, $router);
        break;
}
function getActionData(?string $action): array {
    if (! $action) {
        return [];
    }
    return explode('-', $action);
}
function getActionModel(array $actionData): ?string {
    return $actionData[0] ?? null;
}
function getActionType(array $actionData): ?string {
    return $actionData[1] ?? null;
}
function getController(string $actionModel): ?object {
    return match ($actionModel) {
        'movie' => new \App\Controller\MovieController(),
        'genre' => new \App\Controller\GenreController(),
        'platform' => new \App\Controller\PlatformController(),
        'home' => new \App\Controller\HomeController(),
        'admin' => new \App\Controller\AdminController(),
        'login' => new \App\Controller\AdminLogInController(),
        'review' => new \App\Controller\ReviewController(),
        default => null,
    };
}
if ($view) {
    echo $view;
}
