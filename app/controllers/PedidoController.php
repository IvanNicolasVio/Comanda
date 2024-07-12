<?php

include_once './clases/pedido.php';
include_once './clases/empleado.php';
include_once './clases/mesa.php';
include_once './clases/Tiempo.php';
include_once './controllers/MesaController.php';
include_once './controllers/ImagenController.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PedidoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $nombre_cliente = $params['nombre'];
        $codigo_mesa = $params['mesa'];
        $productos = $params['pedido'];
        $mesaAVerificar = Mesa::checkUtilizada($codigo_mesa);
        if($mesaAVerificar){
            $response->getBody()->write(json_encode(array('Status'=> 'La mesa: ' . $codigo_mesa . ' esta siendo utilizada')));
        }else{
            $numeroPedido = Pedido::Cargar($nombre_cliente,$codigo_mesa,$productos);
            Tiempo::cargarPedido($codigo_mesa,$numeroPedido);
            MesaController::Actualizar($params);
            $response->getBody()->write(json_encode(array('Status'=> 'Pedido numero: ' . $numeroPedido . ' cargado con exito')));
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function adjuntarFoto(Request $request, Response $response, $args){
        if (!isset($_FILES['foto']) || empty($_FILES['foto']['tmp_name'])) {
            $response->getBody()->write(json_encode(array('Error' => 'Ingrese una foto')));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $params = $request->getParsedBody();
        $codigo_mesa = $params['mesa'];
        $codigo_pedido = $params['pedido'];
        $fileFoto = $_FILES['foto'];
        $ruta = ImagenController::FotoMesa($codigo_mesa, $codigo_pedido, $fileFoto);
        $foto = Pedido::adjuntarFoto($codigo_mesa, $codigo_pedido, $ruta);
        if ($foto) {
            $response->getBody()->write(json_encode(array('Status' => 'Foto cargada con exito')));
        } else {
            $response->getBody()->write(json_encode(array('Error' => 'Problema al adjuntar la foto')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos(Request $request, Response $response, $args) {
        $pedidos = Pedido::MostrarPedidos();
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPendientes(Request $request, Response $response, $args) {
        $pedidos = Pedido::MostrarPendientes();
        if($pedidos){
            $pedidos = json_encode($pedidos);
            $response->getBody()->write($pedidos);
        }else{
            $response->getBody()->write(json_encode(array('Status'=> 'No hay pedidos pendientes'))); 
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerListos(Request $request, Response $response, $args) {
        $pedidos = Pedido::MostrarListos();
        if($pedidos){
            $pedidos = json_encode($pedidos);
            $response->getBody()->write($pedidos);
        }else{
            $response->getBody()->write(json_encode(array('Status'=> 'No hay pedidos listos'))); 
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorFuncion(Request $request, Response $response, $args) {
        $data = $request->getAttribute('jwt_data');
        $funcion = $data->funcion;
        $pedidos = Pedido::obtenerPedidosPorFuncion($funcion);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerPorEnPreparacion(Request $request, Response $response, $args) {
        $data = $request->getAttribute('jwt_data');
        $funcion = $data->funcion;
        $pedidos = Pedido::obtenerPedidosEnPreparacion($funcion);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function prepararPedido(Request $request, Response $response, $args) {
        $data = $request->getAttribute('jwt_data');
        $params = $request->getQueryParams();
        $id_producto = $params['id_producto'];
        $codigo = $params['codigo'];
        $tiempo = $params['tiempo'];
        $funcion = $data->funcion;
        $id_empleado = $data->id;
        Tiempo::ingresarTiempo($codigo,$tiempo);
        $pedidos = Pedido::modificarPedido($funcion, $id_producto, $id_empleado, $codigo);
        $pedidos = json_encode($pedidos);
        $response->getBody()->write($pedidos);
        return $response->withHeader('Content-Type', 'application/json');
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
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function entregarPedido(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $codigo = $params['codigo'];
        $pedidos = Pedido::mostrarPedidosXCodigo($codigo);
        if ($pedidos) {
            $todosListos = true;
            $todosEntregados = false;
            foreach ($pedidos as $pedido) {
                if ($pedido['estado'] == 'cancelado') {
                    continue;
                }
                
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
                Tiempo::comparar($codigo);
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
        
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cancelarUnPedido(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $codigo = $params['codigo'];
        $id_producto = $params['id_producto'];
        $status = Pedido::cancelarUnPedido($id_producto,$codigo);
        Tiempo::cancelar($codigo);
        $response->getBody()->write(json_encode($status));
        return $response->withHeader('Content-Type', 'application/json');
    }
}