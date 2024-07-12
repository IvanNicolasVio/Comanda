<?php
include_once './clases/mesa.php';
include_once './clases/pedido.php';
include_once './clases/producto.php';
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MesaController {
    public function crear(Request $request, Response $response, $args) {
        $mesa = Mesa::CrearMesa();
        $response->getBody()->write(json_encode(array('Status'=>$mesa->codigo . ' dada de alta con exito!')));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function Actualizar($params){
        $codigoMesa = $params['mesa'];
        $estado = 'con cliente esperando pedido';
        Mesa::CambiarEstado($codigoMesa,$estado);
    }

    public function TraerTodas(Request $request, Response $response, $args) {
        $mesas = Mesa::MostrarMesas();
        $mesas = json_encode($mesas);
        $response->getBody()->write($mesas);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerSinUso(Request $request, Response $response, $args) {
        $mesas = Mesa::MostrarSinUso();
        $mesas = json_encode($mesas);
        $response->getBody()->write($mesas);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function TraerEnUso(Request $request, Response $response, $args) {
        $mesas = Mesa::MostrarEnUso();
        if($mesas){
            $mesas = json_encode($mesas);
            $response->getBody()->write($mesas);
        }else{
            $response->getBody()->write(json_encode(array('Error!' => 'No hay mesas en uso')));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function entregarCuenta(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        $codigoMesa = $params['mesa'];
        $codigoPedido = $params['codigo'];
        $estado = 'con cliente pagando';
        $cuenta = array();
        $mesa = Mesa::MostrarUna($codigoMesa,'con cliente comiendo');
        if ($mesa) {
            $pedidos = Pedido::mostrarPedidosXCodigo($codigoPedido);
            $itemNumber = 1;
            $total = 0;
            foreach ($pedidos as $pedido) {
                if ($pedido['estado'] == 'cancelado') {
                    continue;
                }
                
                if ($pedido['estado'] != 'entregado') {
                    $response->getBody()->write(json_encode(array('Error!' => 'Pedido incorrecto')));
                    return $response->withHeader('Content-Type', 'application/json');
                } else {
                    $producto = Producto::ValidarProducto($pedido['id_producto']);
                    if ($producto) {
                        $itemDescription = "item {$itemNumber}, " . $producto['nombre'] . ' cantidad: ' . $pedido['cantidad'] . ' $' . $pedido['valor_total'];
                        $cuenta[] = $itemDescription;
                        $total = $total + $pedido['valor_total'];
                        $itemNumber++;
                    } else {
                        $response->getBody()->write(json_encode(array('Error!' => 'Producto no encontrado')));
                        return $response->withHeader('Content-Type', 'application/json');
                    }
                }
            }
            $totalCuenta = "TOTAL: $" . $total;
            $cuenta[] = $totalCuenta;
            Mesa::CambiarEstado($codigoMesa, $estado);
            $response->getBody()->write(json_encode(array('cuenta' => $cuenta)));
            return $response->withHeader('Content-Type', 'application/json');
        } else {
            $response->getBody()->write(json_encode(array('Error!' => 'Mesa no encontrada')));
            return $response->withHeader('Content-Type', 'application/json');
        }
    }

    public static function cerrarMesa(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        $codigoMesa = $params['mesa'];
        $estado = 'cerrada';
        $mesa = Mesa::MostrarUna($codigoMesa,'con cliente pagando');
        if($mesa){
            Mesa::CambiarEstado($codigoMesa, $estado);
            $response->getBody()->write(json_encode(array('Status' => 'Mesa ' . $codigoMesa . ' cerrada con exito')));
        }else{
            $response->getBody()->write(json_encode(array('Error!' => 'Mesa no encontrada')));
            
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function cancelarMesa(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        $codigoMesa = $params['mesa'];
        $estado = 'cerrada';
        $mesa = Mesa::TraerUna($codigoMesa);
        if($mesa){
            Mesa::CambiarEstado($codigoMesa, $estado);
            Pedido::cancelarPedidos($codigoMesa);
            Tiempo::cancelarPorMesa($codigoMesa);
            $response->getBody()->write(json_encode(array('Status' => 'Mesa ' . $codigoMesa . ' cerrada con exito')));
        }else{
            $response->getBody()->write(json_encode(array('Error!' => 'Mesa no encontrada')));
            
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function BorrarMesa(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        $codigoMesa = $params['mesa'];
        $mesa = Mesa::TraerUna($codigoMesa);
        if($mesa){
            $exito = Mesa::BorrarMesa($codigoMesa);
            if($exito > 0){
                $response->getBody()->write(json_encode(array('Status' => 'Mesa ' . $codigoMesa . ' borrada')));
            }
        }else{
            $response->getBody()->write(json_encode(array('Error!' => 'Mesa no encontrada')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function MostrarConMasUso(Request $request, Response $response, $args) {
        $mesa = Pedido::obtenerMesaMasUsada();
        if($mesa){
            $response->getBody()->write(json_encode('La mesa mas usada es: ' . $mesa['codigo_mesa'] . ' cantidad de usos: ' . $mesa['usos']));
        }else{
            $response->getBody()->write(json_encode(array('Error!' => 'No hay mesas en uso')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

}