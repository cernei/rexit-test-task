<?php

use Kernel\Router;
use src\Controllers\UsersController;
use src\Controllers\DictionariesController;

set_time_limit(0);
require_once  __DIR__ . '/../Kernel/Router.php';
require_once  __DIR__ . '/../Kernel/Database.php';
require_once  __DIR__ . '/../Kernel/QueryBuilder.php';
require_once  __DIR__ . '/../Kernel/helpers.php';
require_once  __DIR__ . '/../src/Migrations/UsersMigration.php';

$router = new Router();

// Add routes
$router->addRoute('users/paginate', UsersController::class, 'paginate');
$router->addRoute('users/upload', UsersController::class, 'upload');
$router->addRoute('users/exportCsv', UsersController::class, 'exportCsv');
$router->addRoute('users/truncate', UsersController::class, 'truncate');
$router->addRoute('dictionaries', DictionariesController::class, 'getAll');

// Route the URL
$routeInfo = $router->route($_SERVER['REQUEST_URI']);

// Output the result
if ($routeInfo) {
    require_once  __DIR__ . '/../' . str_replace('\\', '/', $routeInfo['class']) . '.php';

    $controller = new $routeInfo['class'];
    $request = json_decode(file_get_contents('php://input'), true) ?: [];

    echo $controller->{$routeInfo['method']}($request);
} else {
    header('HTTP/1.0 404 Not Found');
}
