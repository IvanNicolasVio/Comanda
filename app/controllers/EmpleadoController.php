<?php
include_once './clases/empleado.php';

include_once './clases/AutenticadorJWT.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class EmpleadoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $empleado = Empleado::CrearEmpleado($params);
        $response->getBody()->write(json_encode(array('Status'=>$empleado->nombre . ' dado de alta con exito!')));
        return $response;
    }

    public function TraerTodos(Request $request, Response $response, $args) {
        $empleados = Empleado::MostrarEmpleados();
        $empleados = json_encode($empleados);
        $response->getBody()->write($empleados);
        return $response;
    }

    public function TraerAltas(Request $request, Response $response, $args) {
        $empleados = Empleado::MostrarEmpleadosAlta();
        $empleados = json_encode($empleados);
        $response->getBody()->write($empleados);
        return $response;
    }

    public function TraerPorFuncion(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $empleados = Empleado::MostrarEmpleadosXFuncion($params['funcion']);
        $empleados = json_encode($empleados);
        $response->getBody()->write($empleados);
        return $response;
    }

    public function Logearse(Request $request, Response $response, $args){
        $params = $request->getQueryParams();
        $usuario = $params['nombre'];
        $contrasenia = $params['contrasenia'];
        $empleado = Empleado::TraerEmpleadoPorUsuarioContraseña($usuario,$contrasenia);
        if($empleado){
            $data = AutentificadorJWT::CrearToken($empleado);
            $response->getBody()->write($data);
        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No existe el empleado')));
        }
        return $response;
    }

}