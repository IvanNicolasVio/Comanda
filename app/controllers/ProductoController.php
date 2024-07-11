<?php
include_once './clases/producto.php';

use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController {
    public function crear(Request $request, Response $response, $args) {
        $params = $request->getParsedBody();
        $producto = Producto::CrearProducto($params);
        $response->getBody()->write(json_encode(array('Status'=>$producto->nombre . ' dado de alta con exito!')));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos(Request $request, Response $response, $args) {
        $productos = Producto::MostrarProductos();
        $productos = json_encode($productos);
        $response->getBody()->write($productos);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function modificar(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $nombre = $params['nombre'];
        $valor = $params['valor'];
        $producto = Producto::modificarProducto($valor,$nombre);
        $producto = json_encode($producto);
        $response->getBody()->write($producto);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function Borrar(Request $request, Response $response, $args) {
        $params = $request->getQueryParams();
        $id = $params['id'];
        $producto = Producto::BorrarProducto($id);
        $producto = json_encode($producto);
        $response->getBody()->write($producto);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function CargarMuchosProductos(Request $request, Response $response, $args)
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
                $sector = $data[1];
                $valor = $data[2];
                if (!in_array($sector, ["barra", "chopera", "cocina", "candybar"])) {
                    $arrayFinal[] = ['Status' => 'Sector no existe para ' . $nombre . ', no sera cargado'];
                    continue;
                }
                $producto = Producto::CheckNombre($nombre);
                if ($producto) {
                    Producto::modificarProducto($valor,$nombre);
                    $arrayFinal[] = ['Status' => 'El producto ' . $nombre . ' se ha actualizado'];
                } else {
                    $params = [
                        'nombre' => $nombre,
                        'sector' => $sector,
                        'valor' => $valor,
                    ];
                    $producto = Producto::CrearProducto($params);
                    $arrayFinal[] = ['Status' => $producto->nombre . ' dado de alta con éxito!'];
                }
            }
            fclose($file);
            $response->getBody()->write(json_encode($arrayFinal));
        } else {
            $response->getBody()->write(json_encode(['Error!' => 'Error al subir el archivo']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarMuchosProductos(Request $request, Response $response, $args)
    {
        $productos = Producto::MostrarProductos();
        if ($productos) {
            $filename = "empleados.csv";
            $file = fopen('php://memory', 'w');
            foreach ($productos as $producto) {
                fputcsv($file, $producto);
            }
            fseek($file, 0);
            $response = $response->withHeader('Content-Type', 'text/csv')
                                ->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->getBody()->write(stream_get_contents($file));
            fclose($file);
        } else {
            $response->getBody()->write(json_encode(['Error!' => 'No hay productos']));
            return $response;
        }
        return $response;
    }

    public function generarCarta(Request $request, Response $response, $args){
        $productos = Producto::MostrarProductos();
        if($productos){
            $item = 1;
            $pdf = new \TCPDF();
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 15);
            $html = '<h1>Carta: </h1><ul>';
            foreach($productos as $producto){
                $html .= '<li>' . $item . '- ' .  $producto['nombre'] .'-----' . $producto['valor'] . '</li>';
                $item++;
            }
            $html .= '</ul>';
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->lastPage();
            $pdfOutput = $pdf->Output('cuenta.pdf', 'S');
        
            $response = $response->withHeader('Content-Type', 'application/pdf')
                                ->withHeader('Content-Disposition', 'attachment; filename="cuenta.pdf"')
                                ->withBody(new \Slim\Psr7\Stream(fopen('php://memory', 'r+')));
            $response->getBody()->write($pdfOutput);
            
        } else {
            $response->getBody()->write(json_encode(array('Error!' => 'Mesa no encontrada')));
        }
        return $response;
    }
    
}