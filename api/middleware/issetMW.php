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
        $params = $request->getParsedBody();

        if($this->tipo == 'usuario'){
            if(isset($params['nombre']) && isset($params['contrasenia']) && isset($params['funcion']))
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $response->getBody()->write(json_encode(array('ERROR!'=>'PARAMETROS EQUIVOCADOS')));
            }
            return $response;
        }
        
    }
}