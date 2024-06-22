<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class issetMW{

    private $tipo;

    public function __construct($tipo)
    {
        $this->tipo = $tipo;
    }
    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();

        if($this->tipo == 'usuario'){
            if(isset($params['nombre']) && isset($params['contrasenia']) && isset($params['funcion']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;

        }elseif($this->tipo == 'producto'){
            if(isset($params['nombre']) && isset($params['sector']) && isset($params['valor']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;

        }elseif($this->tipo == 'pedido'){
            if(isset($params['mesa']) && isset($params['nombre']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;

        }elseif($this->tipo == 'funcion'){
            if(isset($params['funcion']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!'=>'Parametros equivocados')));
            }
            return $response;
        }
        
    }
}
