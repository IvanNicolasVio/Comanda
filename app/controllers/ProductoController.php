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

    public function TraerTodos(Request $request, Response $response, $args) {
        $productos = Producto::MostrarProductos();
        $productos = json_encode($productos);
        $response->getBody()->write($productos);
        return $response;
    }

    public function Modificar(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $nombre = $params['nombre'];
        $valor = $params['valor'];
        $producto = Producto::modificarProducto($valor,$nombre);
        $producto = json_encode($producto);
        $response->getBody()->write($producto);
        return $response;
    }

    public function Borrar(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $id = $params['id'];
        $producto = Producto::BorrarProducto($id);
        $producto = json_encode($producto);
        $response->getBody()->write($producto);
        return $response;
    }
}