<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
class CheckSectorMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getParsedBody();

        if($params['sector'] === 'barra' || $params['sector'] === 'chopera' || $params['sector'] === 'cocina' || $params['sector'] === 'candybar')
        {
            $response = $handler->handle($request);
        }
        else
        {
            $response = new Response();
            $response->getBody()->write(json_encode(array('Error!'=>'Sector inexistente')));
        }
        return $response;
    }
}