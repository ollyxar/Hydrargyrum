<?php

require_once 'library/Routes/Route.php';
require_once 'library/Controllers/TerminalController.php';
require_once 'library/Controllers/ForumController.php';
require_once 'library/Controllers/NewsController.php';

use \Hg\Routes\Route;

//$router = new Route('/users/terminal/2/edit?vasa=ololo', 'GET');
//$router = new Route('/forum/category-one?vasa=ololo', 'GET');
$router = new Route('/forum/news/footbal?vasa=ololo', 'GET');
//$router = new Route('/forum/mafia', 'POST');


// Routes file should be:

$router->get('users/terminal/{terminal}/edit', 'TerminalController@getMe');

$router->group('forum', function () use ($router) {
    $router->get('{name}', 'ForumController@getMe');
    $router->group('news', function () use ($router) {
        $router->get('{name}', 'NewsController@getMe');
    });
    $router->post('mafia', function () {
        return 'Mafia!';
    });
});

// end of Routes

$response = $router->action();

echo $response;