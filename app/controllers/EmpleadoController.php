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

    public function CargarMuchosEmpleados(Request $request, Response $response, $args)
    {
        $parametros = $request->getUploadedFiles();
        if (empty($parametros)) {
            $response->getBody()->write(json_encode(['Status' => 'No se ha ingresado ningún archivo']));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $archivoCsv = $parametros['csv'];
        if ($archivoCsv->getError() === UPLOAD_ERR_OK) {
            $archivo = $archivoCsv->getClientFilename();
            $directorio = __DIR__ . '/../archivos/';
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $archivoCsv->moveTo($directorio . $archivo);
            $file = fopen($directorio . $archivo, 'r');
            $arrayFinal = [];
            while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {
                $nombre = $data[0];
                $contrasenia = $data[1];
                $funcion = $data[2];
                if (!in_array($funcion, ["Bartender", "Mozo", "Cervecero", "Cocinero", "Socio"])) {
                    $arrayFinal[] = ['Status' => 'Funcion no valida para ' . $nombre . ', no sera cargado'];
                    continue;
                }
                $empleado = Empleado::CheckNombre($nombre);
                if ($empleado) {
                    $arrayFinal[] = ['Status' => 'El usuario ' . $nombre . ' ya se encuentra cargado'];
                    continue;
                } else {
                    $params = [
                        'nombre' => $nombre,
                        'contrasenia' => $contrasenia,
                        'funcion' => $funcion,
                    ];
                    $empleado = Empleado::CrearEmpleado($params);
                    $arrayFinal[] = ['Status' => $empleado->nombre . ' dado de alta con éxito!'];
                }
            }
            fclose($file);
            $response->getBody()->write(json_encode($arrayFinal));
        } else {
            $response->getBody()->write(json_encode(['Error!' => 'Error al subir el archivo']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarMuchosEmpleados(Request $request, Response $response, $args)
    {
        $empleados = Empleado::MostrarEmpleados();
        if ($empleados) {
            $filename = "empleados.csv";
            $file = fopen('php://memory', 'w');
            foreach ($empleados as $empleado) {
                fputcsv($file, $empleado);
            }
            fseek($file, 0);
            $response = $response->withHeader('Content-Type', 'text/csv')
                                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->getBody()->write(stream_get_contents($file));
            fclose($file);
        } else {
            $response->getBody()->write(json_encode(['Error!' => 'No hay empleados']));
            return $response;
        }
        return $response;
    }
}