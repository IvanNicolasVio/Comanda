<?php

include_once './clases/pedido.php';
include_once './clases/empleado.php';
include_once './controllers/MesaController.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $numeroPedido = Pedido::Cargar($params);
        MesaController::Actualizar($params);
        $response->getBody()->write(json_encode(array('Status'=> 'Pedido numero: ' . $numeroPedido . ' cargado con exito')));
        return $response;
    }

    public function TraerTodos(Request $request, Response $response, $args) {
        $pedidos = Pedido::MostrarPedidos();
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }

    public function TraerPendientes(Request $request, Response $response, $args) {
        $pedidos = Pedido::MostrarPendientes();
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }

    public function TraerPorFuncion(Request $request, Response $response, $args) {
        $funcion = $request->getAttribute('funcion');
        $pedidos = Pedido::obtenerPedidosPorFuncion($funcion);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }
}