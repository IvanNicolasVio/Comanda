<?php
include_once './clases/empleado.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmpleadoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $empleado = Empleado::CrearEmpleado($params);
        $response->getBody()->write(json_encode(array('Status'=>$empleado->nombre . ' dado de alta con exito!')));
        return $response;
    }
}