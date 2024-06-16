<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../api/controllers/EmpleadoController.php';
require_once '../api/middleware/CheckRolMW.php';
require_once '../api/middleware/issetMW.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();

$app->group('/empleado', function(RouteCollectorProxy $group){
    $group->post('/crear',\EmpleadoController::class . ':crear')
        ->add(new CheckRolMW())
        ->add(new issetMW());
});


$app->run();
