<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckNumsMW {

    private $claves;

    public function __construct(array $claves) {
        $this->claves = $claves;
    }

    public function __invoke(Request $request, RequestHandler $handler) {
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();
        foreach ($this->claves as $clave) {
            if (!isset($params[$clave]) || !$this->isValidIntInRange($params[$clave])) {
                $response = new Response();
                $response->getBody()->write(json_encode(['Error!' => 'Parametro invalido: ' . $clave]));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }
        return $handler->handle($request);
    }

    private function isValidIntInRange($value) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false && $value >= 1 && $value <= 10;
    }
}