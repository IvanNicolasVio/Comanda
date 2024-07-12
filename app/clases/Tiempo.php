<?php
include_once './db/AccesoDatos.php';
class Tiempo
{
    public static function cargarPedido($mesa,$codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into control_tiempo (mesa,codigo)values(:mesa,:codigo)");
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_STR);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function traerPedido($codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM control_tiempo WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $pedidos = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }  
    

    public static function ingresarTiempo($codigo,$tiempo){
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $horaActual = date_create("now");
        $tiempo = $tiempo + 5;
        $tiempoEnSegundos = $tiempo * 60;
        date_add($horaActual, date_interval_create_from_date_string($tiempoEnSegundos . ' seconds'));
        $nuevaHora = date_format($horaActual, 'Y-m-d H:i:s');
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $pedidos = Tiempo::traerPedido($codigo);
        if($pedidos){
            if($pedidos['tiempo_estimado'] == null){
                $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE control_tiempo SET tiempo_estimado = :nuevaHora WHERE codigo = :codigo");
                $consulta->bindValue(':nuevaHora', $nuevaHora, PDO::PARAM_STR);
                $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                $consulta->execute();
            }else{
                $tiempoEstimadoActual = date_create($pedidos['tiempo_estimado']);
                $nuevaHoraDateTime = date_create($nuevaHora);
                if ($nuevaHoraDateTime > $tiempoEstimadoActual) {
                    $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE control_tiempo SET tiempo_estimado = :nuevaHora WHERE codigo = :codigo");
                    $consulta->bindValue(':nuevaHora', $nuevaHora, PDO::PARAM_STR);
                    $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
                    $consulta->execute();
                }
            }
        }
    }   

    public static function comparar($codigo)
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $horaActual = date_create("now");
        $tiempoFinal = date_format($horaActual, 'Y-m-d H:i:s');
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $pedidos = Tiempo::traerPedido($codigo);
        if ($pedidos) {
            $tiempoEstimado = date_create($pedidos['tiempo_estimado']);
            $tiempoFinalDateTime = date_create($tiempoFinal);

            if ($tiempoFinalDateTime <= $tiempoEstimado) {
                $estadoPedido = 'entregado a tiempo';
            } else {
                $estadoPedido = 'entregado con demora';
            }
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE control_tiempo SET tiempo_final = :tiempoFinal, estado = :estado WHERE codigo = :codigo");
            $consulta->bindValue(':tiempoFinal', $tiempoFinal, PDO::PARAM_STR);
            $consulta->bindValue(':estado', $estadoPedido, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();
        }
    }

    public static function cancelar($codigo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $pedidos = Tiempo::traerPedido($codigo);
        if ($pedidos) {
            $estadoPedido = 'cancelado';
            $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE control_tiempo SET estado = :estado WHERE codigo = :codigo");
            $consulta->bindValue(':estado', $estadoPedido, PDO::PARAM_STR);
            $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
            $consulta->execute();
        }
    }

    public static function cancelarPorMesa($mesa)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE control_tiempo SET estado = 'cancelado' WHERE mesa = :mesa AND estado IS NULL" );
        $consulta->bindValue(':mesa', $mesa, PDO::PARAM_STR);
        $consulta->execute();
        
    }

    public static function traerPedidoYTiempo(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT 
                                                            ct.mesa,
                                                            ct.codigo,
                                                            ct.tiempo_estimado
                                                        FROM control_tiempo ct
                                                        INNER JOIN pedidos_principal pp
                                                        ON ct.codigo = pp.codigo
                                                        WHERE pp.estado = 'con cliente esperando pedido'");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }  
}

?>