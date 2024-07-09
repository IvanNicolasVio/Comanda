<?php

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class issetMW {

    private $parametrosACheck;

    public function __construct(array $parametrosACheck)
    {
        $this->parametrosACheck = $parametrosACheck;
    }

    public function __invoke(Request $request, RequestHandler $handler) {
        $params = $request->getMethod() === 'POST' ? $request->getParsedBody() : $request->getQueryParams();

        if ($this->checkParams($params, $this->parametrosACheck)) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(['Error!' => 'Parametros equivocados']));
        }

        return $response;
    }

    private function checkParams($params, $parametrosACheck) {
        foreach ($parametrosACheck as $param) {
            if (!isset($params[$param])) {
                return false;
            }
        }
        return true;
    }
}