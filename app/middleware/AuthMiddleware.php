<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

include_once './clases/AutenticadorJWT.php';

class AuthMiddleware
{
    private $funcion;

    public function __construct($funcion = null)
    {
        $this->funcion = $funcion;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $header = $request->getHeaderLine('Authorization');
        
        if (empty($header) || strpos($header, 'Bearer ') !== 0) {
            $response = new Response();
            $payload = json_encode(array('Error!' => 'Token no proporcionado o mal formado'));
            $response->getBody()->write($payload);
        }
        $token = trim(str_replace('Bearer ', '', $header));
        try {
            AutentificadorJWT::VerificarToken($token);
            $data = AutentificadorJWT::ObtenerData($token);
            $funcion = $data->funcion;
            if($funcion == $this->funcion || $funcion == 'Socio' || $funcion == 'Admin'){
                $response = $handler->handle($request);
            } else {
                $response = new Response();
                $payload = json_encode(array('Error!' => 'Acceso denegado'));
                $response->getBody()->write($payload);
            }
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('Error!' => 'TOKEN incorrecto'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}