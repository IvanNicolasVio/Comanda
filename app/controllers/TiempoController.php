<?php

include_once './clases/Tiempo.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TiempoController {

    public static function DarFechaActual()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fechaFormateada = date_create("now");
        $fechaFormateada = date_format($fechaFormateada, 'Y-m-d');
        return $fechaFormateada;
    }

    public static function DarFechaConHora()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fechaFormateada = date_create("now");
        $fechaFormateada = date_format($fechaFormateada, 'Y-m-d H:i:s');
        return $fechaFormateada;
    }

    public static function obtenerTiempoEstimado(Request $request, Response $response, $args)
    {
        $params = $request->getQueryParams();
        $codigo = $params['pedido'];
        $pedidos = Tiempo::traerPedido($codigo);
        if ($pedidos) {
            if($pedidos['tiempo_final'] != null){
                $response->getBody()->write(json_encode(array('Ya se entrego tu pedido')));
            } elseif ($pedidos['tiempo_estimado'] == null) {
                $response->getBody()->write(json_encode(array('Todavía no empezaron a preparar el producto')));
            } else {
                $fechaEstimada = date_create($pedidos['tiempo_estimado']);
                $horaEstimadaFormateada = date_format($fechaEstimada, 'H:i');
                $response->getBody()->write(json_encode(array('Tu producto estará listo para ser entregado a las ' . $horaEstimadaFormateada . ' aproximadamente')));
            }
        } else {
            $response->getBody()->write(json_encode(array('Pedido no encontrado')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function traerPedidosYTiempo(Request $request, Response $response, $args)
    {
        $pedidos = Tiempo::traerPedidoYTiempo();
        if ($pedidos) {
            $response->getBody()->write(json_encode($pedidos));
        } else {
            $response->getBody()->write(json_encode(array('No existen pedidos')));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

}