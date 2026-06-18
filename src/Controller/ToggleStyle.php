<?php
namespace App\Controller;

use App\Exception\NotFoundException;

use App\Service\Router;
use App\Service\Templating;
class ToggleStyle
{
    public function toggleTheme()
    {
        $_SESSION['theme'] = ($_SESSION['theme'] === 'alt') ? 'default' : 'alt';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}