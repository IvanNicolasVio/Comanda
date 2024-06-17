<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../api/controllers/EmpleadoController.php';
require_once '../api/controllers/MesaController.php';
require_once '../api/controllers/ProductoController.php';
require_once '../api/controllers/PedidoController.php';
require_once '../api/middleware/CheckMesaMW.php';
require_once '../api/middleware/CheckRolMW.php';
require_once '../api/middleware/CheckSectorMW.php';
require_once '../api/middleware/CheckPedidoMW.php';
require_once '../api/middleware/issetMW.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();

$app->group('/empleados', function(RouteCollectorProxy $group){
    $group->post('/crear',\EmpleadoController::class . ':crear')
        ->add(new CheckRolMW())
        ->add(new issetMW('usuario'));
    $group->get('/traerTodos',\EmpleadoController::class . ':TraerTodos');
    $group->get('/traerAltas',\EmpleadoController::class . ':TraerAltas');
    $group->get('/traerPorFuncion',\EmpleadoController::class . ':TraerPorFuncion')
        ->add(new CheckRolMW());
});
$app->group('/mesas', function(RouteCollectorProxy $group){
    $group->post('/crear',\MesaController::class . ':crear');
    $group->get('/traerTodas',\MesaController::class . ':TraerTodas');
    $group->get('/traerSinUso',\MesaController::class . ':TraerSinUso');
    $group->get('/traerEnUso',\MesaController::class . ':TraerEnUso');
});
$app->group('/productos', function(RouteCollectorProxy $group){
    $group->post('/crear',\ProductoController::class . ':crear')
    ->add(new CheckSectorMW())
    ->add(new issetMW('producto'));
    $group->get('/traerTodos',\ProductoController::class . ':TraerTodos');
});
$app->group('/pedidos', function(RouteCollectorProxy $group){
    $group->post('/tomar',\PedidoController::class . ':crear')
    ->add(new CheckPedidoMW())
    ->add(new CheckMesaMW())
    ->add(new issetMW('pedido'));
    $group->get('/traerTodos',\PedidoController::class . ':TraerTodos');
    $group->get('/traerPendientes',\PedidoController::class . ':TraerPendientes');
});


$app->run();
