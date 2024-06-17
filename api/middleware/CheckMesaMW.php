<?php
include_once './db/AccesoDatos.php';
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class CheckMesaMW{

    public function __invoke(Request $request, RequestHandler $handler){
        $params = $request->getParsedBody();
        $id_mesa = $params['mesa'];
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas WHERE codigo = ?");
        $consulta->bindValue(1, $id_mesa, PDO::PARAM_STR);
        $consulta->execute();
        $result = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
            $response->getBody()->write(json_encode(array('ERROR!' => 'Mesa no encontrada')));
        }
        return $response;
    }
}