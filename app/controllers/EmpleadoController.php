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

    public function Modificar(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $nombre = $params['nombre'];
        $funcion = $params['funcion'];
        $empleado = Empleado::CheckNombre($nombre);
        if($empleado){
            Empleado::ModificarEmpleado($nombre,$funcion);
            $response->getBody()->write(json_encode(array('Status'=>$empleado['nombre'] . ' modificado!')));
        }else{
            $response->getBody()->write(json_encode(array('Error!'=>$nombre . ' inexistente')));
        }
        return $response;
    }

    public function SoftDelete(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $nombre = $params['nombre'];
        $empleado = Empleado::CheckNombre($nombre);
        if($empleado){
            Empleado::AgregarFechaBaja($nombre);
            $response->getBody()->write(json_encode(array('Status'=>$empleado['nombre'] . ' dado de baja')));
        }else{
            $response->getBody()->write(json_encode(array('Error!'=>$nombre . ' inexistente')));
        }
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
            if($empleado['fecha_baja'] != '0000-00-00'){
                $response = new Response();
                $data = json_encode(array('Error!' => 'Dado de baja'));
                $response->getBody()->write($data);

            }else{
                $data = AutentificadorJWT::CrearToken($empleado);
                $response->getBody()->write($data);
            }

        }else{
            $response->getBody()->write(json_encode(array('Status'=>'No existe el empleado')));
        }
        return $response->withHeader('Content-Type', 'application/json');;
    }

}