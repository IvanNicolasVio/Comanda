<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class CheckRolMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getParsedBody();

        if($params['funcion'] === 'Bartender' || $params['funcion'] === 'Cervecero' || $params['funcion'] === 'Cocinero' || $params['funcion'] === 'Mozo' || $params['funcion'] === 'Socio')
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $response->getBody()->write(json_encode(array('Error!'=>'Funcion inexistente')));
        }
        return $response;
    }
}