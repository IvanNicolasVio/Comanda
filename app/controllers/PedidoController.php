<?php

include_once './clases/pedido.php';
include_once './clases/empleado.php';
include_once './clases/mesa.php';
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
        $data = $request->getAttribute('jwt_data');
        $funcion = $data->funcion;
        $pedidos = Pedido::obtenerPedidosPorFuncion($funcion);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }

    public function prepararPedido(Request $request, Response $response, $args) {
        $data = $request->getAttribute('jwt_data');
        $params = $request->getQueryParams();
        $id_producto = $params['id_producto'];
        $codigo = $params['codigo'];
        $funcion = $data->funcion;
        $id_empleado = $data->id;
        $pedidos = Pedido::modificarPedido($funcion, $id_producto, $id_empleado, $codigo);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }

    public function finalizarPedido(Request $request, Response $response, $args) {
        $data = $request->getAttribute('jwt_data');
        $params = $request->getQueryParams();
        $id_producto = $params['id_producto'];
        $codigo = $params['codigo'];
        $id_empleado = $data->id;
        $pedidos = Pedido::finalizarPedido($id_producto, $id_empleado, $codigo);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response;
    }

    public function entregarPedido(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $codigo = $params['codigo'];
        $pedidos = Pedido::mostrarPedidosXCodigo($codigo);
        if ($pedidos) {
            $todosListos = true;
            $todosEntregados = false;
            foreach ($pedidos as $pedido) {
                if($pedido['estado'] == 'entregado'){
                    $todosEntregados = true;
                    break;
                }
                else if ($pedido['estado'] !== 'listo para servir') {
                    $todosListos = false;
                    break;
                }
            }
            if($todosEntregados){
                $response->getBody()->write(json_encode(array('Status' => 'Los pedidos ya fueron entregados')));
            }
            else if ($todosListos) {
                Pedido::entregarPedidos($codigo);
                $codigoMesa = Pedido::mostrarMesaXCodigoPedido($codigo);
                $codigoMesa = $codigoMesa['codigo_mesa'];
                $estado = 'con cliente comiendo';
                Mesa::CambiarEstado($codigoMesa,$estado);
                $response->getBody()->write(json_encode(array('Status' => 'Pedidos entregados!')));
            } else {
                $response->getBody()->write(json_encode(array('Status' => 'No todos los pedidos estan listos')));
            }
        } else {
            $response->getBody()->write(json_encode(array('Status' => 'No hay pedidos para mostrar')));
        }
        
        return $response;
    }
}