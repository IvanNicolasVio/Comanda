<?php
include_once './clases/mesa.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MesaController {
    public function crear(Request $request, Response $response, $args) {
        $mesa = Mesa::CrearMesa();
        $response->getBody()->write(json_encode(array('Status'=>$mesa->codigo . ' dada de alta con exito!')));
        return $response;
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
        return $response;
    }

    public function TraerSinUso(Request $request, Response $response, $args) {
        $mesas = Mesa::MostrarSinUso();
        $mesas = json_encode($mesas);
        $response->getBody()->write($mesas);
        return $response;
    }
    
    public function TraerEnUso(Request $request, Response $response, $args) {
        $mesas = Mesa::MostrarEnUso();
        $mesas = json_encode($mesas);
        $response->getBody()->write($mesas);
        return $response;
    }
}