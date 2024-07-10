<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once '../app/controllers/EmpleadoController.php';
require_once '../app/controllers/MesaController.php';
require_once '../app/controllers/ProductoController.php';
require_once '../app/controllers/PedidoController.php';
require_once '../app/controllers/ClienteController.php';
require_once '../app/middleware/CheckNombreMW.php';
require_once '../app/middleware/CheckMesaMW.php';
require_once '../app/middleware/CheckRolMW.php';
require_once '../app/middleware/CheckSectorMW.php';
require_once '../app/middleware/CheckPedidoMW.php';
require_once '../app/middleware/CheckNumsMW.php';
require_once '../app/middleware/CheckIntMW.php';
require_once '../app/middleware/issetMW.php';
require_once '../app/middleware/AuthMiddleware.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

$app = AppFactory::create();

$app->group('/log', function(RouteCollectorProxy $group){
    $group->get('',\EmpleadoController::class . ':Logearse');
});

$app->group('/empleados', function(RouteCollectorProxy $group){
    $group->post('/crear',\EmpleadoController::class . ':crear')
        ->add(new CheckNombreMW())
        ->add(new CheckRolMW())
        ->add(new issetMW(['nombre','contrasenia','funcion']))
        ->add(new AuthMiddleware(['Socio']));

    $group->put('/modificar',\EmpleadoController::class . ':Modificar')
        ->add(new CheckRolMW())
        ->add(new issetMW(['nombre','funcion']))
        ->add(new AuthMiddleware(['Socio']));
    
    $group->delete('/borrar',\EmpleadoController::class . ':SoftDelete')
    ->add(new issetMW(['nombre']))
    ->add(new AuthMiddleware(['Socio']));

    $group->get('/traerTodos',\EmpleadoController::class . ':TraerTodos')
        ->add(new AuthMiddleware(['Socio']));
    $group->get('/traerAltas',\EmpleadoController::class . ':TraerAltas')
        ->add(new AuthMiddleware(['Socio']));
    $group->get('/traerPorFuncion',\EmpleadoController::class . ':TraerPorFuncion')
        ->add(new CheckRolMW())
        ->add(new issetMW(['funcion']))
        ->add(new AuthMiddleware(['Socio']));
    $group->post('/cargarCsv',\EmpleadoController::class . ':CargarMuchosEmpleados')
        ->add(new AuthMiddleware(['Socio']));
    $group->get('/descargarCsv',\EmpleadoController::class . ':DescargarMuchosEmpleados')
        ->add(new AuthMiddleware(['Socio']));
});

$app->group('/mesas', function(RouteCollectorProxy $group){
    $group->post('/crear',\MesaController::class . ':crear')
        ->add(new AuthMiddleware(['Socio']));
    $group->put('/cancelar',\MesaController::class . ':cancelarMesa')
        ->add(new issetMW(['mesa']))
        ->add(new AuthMiddleware(['Socio']));
    $group->delete('/borrar',\MesaController::class . ':BorrarMesa')
        ->add(new issetMW(['mesa']))
        ->add(new AuthMiddleware(['Socio']));
    $group->get('/traerTodas',\MesaController::class . ':TraerTodas')
        ->add(new AuthMiddleware(['Socio','Mozo']));
    $group->get('/traerSinUso',\MesaController::class . ':TraerSinUso')
        ->add(new AuthMiddleware(['Socio','Mozo']));
    $group->get('/traerEnUso',\MesaController::class . ':TraerEnUso')
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->put('/entregarCuenta',\MesaController::class . ':entregarCuenta')
    ->add(new issetMW(['mesa','codigo']))
    ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->put('/cerrar',\MesaController::class . ':cerrarMesa')
    ->add(new issetMW(['mesa']))
    ->add(new AuthMiddleware(['Socio']));
});

$app->group('/productos', function(RouteCollectorProxy $group){
    $group->post('/crear',\ProductoController::class . ':crear')
        ->add(new CheckSectorMW())
        ->add(new CheckIntMW(['valor']))
        ->add(new CheckNombreMW('producto'))
        ->add(new issetMW(['nombre','sector','valor']))
        ->add(new AuthMiddleware(['Socio']));

    $group->get('/traerTodos',\ProductoController::class . ':TraerTodos')
        ->add(new AuthMiddleware(['Socio','Mozo']));
    $group->put('/modificar',\ProductoController::class . ':modificar')
        ->add(new CheckIntMW(['valor']))
        ->add(new issetMW(['nombre','valor']))
        ->add(new AuthMiddleware(['Socio']));

    $group->delete('/borrar',\ProductoController::class . ':Borrar')
        ->add(new issetMW(['id']))
        ->add(new AuthMiddleware(['Socio']));

    $group->post('/cargarCsv',\ProductoController::class . ':CargarMuchosProductos')
        ->add(new AuthMiddleware(['Socio']));
    $group->get('/descargarCsv',\ProductoController::class . ':DescargarMuchosProductos')
        ->add(new AuthMiddleware(['Socio']));
        
});

$app->group('/pedidos', function(RouteCollectorProxy $group){
    $group->post('/tomar',\PedidoController::class . ':crear')
        ->add(new CheckPedidoMW())
        ->add(new CheckMesaMW())
        ->add(new issetMW(['mesa','nombre','pedido']))
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->post('/adjuntarFoto',\PedidoController::class . ':adjuntarFoto')
        ->add(new CheckMesaMW())
        ->add(new issetMW(['mesa','pedido']))
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->get('/traerTodos',\PedidoController::class . ':TraerTodos')
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->get('/traerPendientes',\PedidoController::class . ':TraerPendientes')
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->get('/mostrarPedidos',\PedidoController::class . ':TraerPorFuncion')
        ->add(new AuthMiddleware(['Socio','Bartender','Cervecero','Cocinero']));
        
    $group->put('/preparar',\PedidoController::class . ':prepararPedido')
        ->add(new issetMW(['codigo','id_producto']))
        ->add(new AuthMiddleware(['Socio','Bartender','Cervecero','Cocinero']));

    $group->put('/finalizar',\PedidoController::class . ':finalizarPedido')
        ->add(new issetMW(['codigo','id_producto']))
        ->add(new AuthMiddleware(['Socio','Bartender','Cervecero','Cocinero']));

    $group->put('/entregar',\PedidoController::class . ':entregarPedido')
        ->add(new issetMW(['codigo']))
        ->add(new AuthMiddleware(['Socio','Mozo']));

    $group->put('/cancelar',\PedidoController::class . ':cancelarUnPedido')
    ->add(new issetMW(['codigo','id_producto']))
    ->add(new AuthMiddleware(['Socio','Mozo']));
});

$app->group('/encuesta', function(RouteCollectorProxy $group){
    $group->post('/llenar',\ClienteController::class . ':LlenarEncuesta')
    ->add(new CheckNumsMW(['mesa','restaurante','mozo','cocinero']))
    ->add(new issetMW(['codigo_mesa','codigo_pedido','mesa','restaurante','mozo','cocinero','descripcion']));
});


$app->run();
