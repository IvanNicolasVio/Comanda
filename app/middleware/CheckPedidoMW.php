<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
include_once './clases/producto.php';

class CheckPedidoMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getParsedBody();
        $pedido = $params['pedido'];
        $data_array = json_decode($pedido, true);
        $banderaExiste = false;
        $banderaId = false;
        $banderaCantidad = false;
        if (is_array($data_array) && !empty($data_array)) {
            foreach ($data_array as $item) {
                if (isset($item['id_producto']) && isset($item['cantidad'])) {
                    if(Producto::ValidarProducto($item['id_producto'])){
                        if($item['cantidad'] > 0){
                            $banderaExiste = true;
                        }else{
                            $banderaExiste = false;
                            $banderaCantidad = true;
                            break;
                        }
                    }else{
                        $banderaExiste = false;
                        $banderaId = true;
                        break;
                    }
                }
            }
        }

        if($banderaExiste)
        {
            $response = $handler->handle($request);
        }else{
            if($banderaId)
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Producto incorrecto')));
            }elseif($banderaCantidad)
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Cantidad incorrecta')));
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Pedido incorrecto')));
            }
        }
        
        
        return $response;
    }
}