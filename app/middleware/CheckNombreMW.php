<?php
include_once './db/AccesoDatos.php';
include_once './clases/empleado.php';
include_once './clases/producto.php';
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckNombreMW{

    private $tipo;

    public function __construct($tipo = 'usuario')
    {
        $this->tipo = $tipo;
    }

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getParsedBody();

        if($this->tipo == 'usuario'){
            $usuario = $params['nombre'];
            $resultado = Empleado::CheckNombre($usuario);
            if ($resultado) {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!' => 'El usuario ya se encuentra creado')));
            } else {
                $response = $handler->handle($request);
            }
        }elseif($this->tipo == 'producto'){
            $producto = $params['nombre'];
            $resultado = Producto::CheckNombre($producto);
            if ($resultado) {
                $response = new Response();
                $response->getBody()->write(json_encode(array('Error!' => 'El producto ya se encuentra creado')));
            } else {
                $response = $handler->handle($request);
            }
        }
        
        return $response;
    }
}