<?php
include_once './clases/encuesta.php';
include_once './clases/mesa.php';
include_once './clases/pedido.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ClienteController {
    
    public function LlenarEncuesta(Request $request, Response $response, $args){
        $params = $request->getParsedBody();
        $pedidoPago = Pedido::traerMesaPaga($params['codigo_pedido'],$params['codigo_mesa']);
        if($pedidoPago){
            $mesaCerrada = Mesa::MostrarUna($pedidoPago['codigo_mesa'],'cerrada');
            if($mesaCerrada){
                $id = Encuesta::Llenar($params);
                if($id){
                    $response->getBody()->write(json_encode(array('Status'=>'Gracias por llenar la encuesta, vuelva pronto!')));
                }else{
                    $response->getBody()->write(json_encode(array('Error!'=>'Error al llenar la encuesta, por favor intentelo de nuevo')));
                }
            }else{
                $response->getBody()->write(json_encode(array('Error!'=>'La mesa no esta cerrada')));
            }        
        }else{
            $response->getBody()->write(json_encode(array('Error!'=>'El pedido no esta entregado')));
        }
        return $response;
    }
}