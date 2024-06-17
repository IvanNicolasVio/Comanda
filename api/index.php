<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../api/controllers/EmpleadoController.php';
require_once '../api/controllers/MesaController.php';
require_once '../api/controllers/ProductoController.php';
require_once '../api/controllers/PedidoController.php';
require_once '../api/middleware/CheckMesaMW.php';
require_once '../api/middleware/CheckRolMW.php';
require_once '../api/middleware/CheckSectorMW.php';
require_once '../api/middleware/issetMW.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();

$app->group('/empleado', function(RouteCollectorProxy $group){
    $group->post('/crear',\EmpleadoController::class . ':crear')
        ->add(new CheckRolMW())
        ->add(new issetMW('usuario'));
});

$app->group('/mesas', function(RouteCollectorProxy $group){
    $group->post('/crear',\MesaController::class . ':crear');
});

$app->group('/producto', function(RouteCollectorProxy $group){
    $group->post('/crear',\ProductoController::class . ':crear')
    ->add(new CheckSectorMW())
    ->add(new issetMW('producto'));
});

$app->group('/pedido', function(RouteCollectorProxy $group){
    $group->post('/tomar',\PedidoController::class . ':crear')
    ->add(new CheckMesaMW())
    ->add(new issetMW('pedido'));
});


$app->run();
