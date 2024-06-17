<?php
include_once './clases/producto.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $producto = Producto::CrearProducto($params);
        $response->getBody()->write(json_encode(array('Status'=>$producto->nombre . ' dado de alta con exito!')));
        return $response;
    }
}