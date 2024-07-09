<?php

include_once './db/AccesoDatos.php';
include_once './clases/ControlTiempo.php';
include_once './clases/producto.php';

class Pedido{
    public $codigo;
    public $fechaPedido;
    
    public function __construct()
    {
        $this->codigo = substr(bin2hex(random_bytes(5)), 0, 5);
        $this->fechaPedido = Fecha::DarFechaConHora();
    }

    public static function Cargar($params){
        $pedido = new Pedido();
        $nombre_cliente = $params['nombre'];
        $codigo_mesa = $params['mesa'];
        $productos = $params['pedido'];
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos_principal (codigo,codigo_mesa,nombre,estado,fecha_pedido)values(:codigo,:codigo_mesa,:nombre,:estado,:fecha_pedido)");
        $consulta->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $nombre_cliente, PDO::PARAM_STR);
        $consulta->bindValue(':estado', 'con cliente esperando pedido', PDO::PARAM_STR);
        $consulta->bindValue(':fecha_pedido', $pedido->fechaPedido);
        $consulta->execute();

        $data_array = json_decode($productos, true);
        foreach ($data_array as $item) {
            $precioProducto = Producto::ValidarProducto($item['id_producto']);
            $precioProducto = $precioProducto['valor'];
            $valorTotal = $precioProducto * $item['cantidad'];
            $consulta_sec = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos_secundario (codigo,id_producto,cantidad,valor_total,estado)values(:codigo,:id_producto,:cantidad,:valor_total,:estado)");
            $consulta_sec->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
            $consulta_sec->bindValue(':id_producto', $item['id_producto'], PDO::PARAM_INT);
            $consulta_sec->bindValue(':cantidad', $item['cantidad'], PDO::PARAM_INT);
            $consulta_sec->bindValue(':valor_total', $valorTotal, PDO::PARAM_INT);
            $consulta_sec->bindValue(':estado', 'pendiente', PDO::PARAM_STR);
            $consulta_sec->execute();
        }
        return $pedido->codigo;
    }

    public static function MostrarPedidos(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }

    public static function MostrarPendientes(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos_secundario WHERE estado = 'pendiente' ");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }

    public static function obtenerPedidosPorFuncion($funcion){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        switch($funcion){
            case 'Cocinero':
                $sentencia = " WHERE pr.sector = 'candybar' OR pr.sector = 'cocina' AND ";
                break;
    
            case 'Bartender':
                $sentencia = " WHERE pr.sector = 'barra' AND ";
                break;
    
            case 'Cervecero':
                $sentencia = " WHERE pr.sector = 'chopera' AND ";
                break;
    
            case 'Socio':
                $sentencia = " WHERE ";
                break;
    
        }
        $sql = "SELECT  ps.codigo, ps.id_producto, pr.nombre, ps.cantidad, pr.sector 
                FROM pedidos_secundario ps
                INNER JOIN productos pr ON ps.id_producto = pr.id" . $sentencia . "ps.estado = 'pendiente'";
    
        $consulta = $objetoAccesoDato->RetornarConsulta($sql);
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    
        if (count($pedidos) > 0) {
            return $pedidos;
        } else {
            return array('Status' => 'No hay pedidos para la funcion especificada');
        }
    }

    public static function modificarPedido($funcion, $id_producto, $id_empleado, $codigo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        switch($funcion){
            case 'Cocinero':
                $sentencia = " AND pr.sector IN ('candybar', 'cocina')";
                break;

            case 'Bartender':
                $sentencia = " AND pr.sector = 'barra'";
                break;

            case 'Cervecero':
                $sentencia = " AND pr.sector = 'chopera'";
                break;

            case 'Socio':
                $sentencia = ""; 
                break;
        }

        $sql = "UPDATE pedidos_secundario ps
                INNER JOIN productos pr ON ps.id_producto = pr.id
                SET ps.id_empleado = :id_empleado, ps.estado = 'en preparacion'
                WHERE ps.id_producto = :id_producto
                AND ps.codigo = :codigo" . $sentencia . " AND ps.estado = 'pendiente'";

        $consulta = $objetoAccesoDato->RetornarConsulta($sql);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            return array('Status' => 'Pedido actualizado');
        } else {
            return array('Status' => 'No hay pedidos pendientes para la función especificada, o el producto no corresponde al sector del empleado, o el código no coincide');
        }
    }

    public static function finalizarPedido($id_producto, $id_empleado, $codigo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $sql = "UPDATE pedidos_secundario 
                SET estado = 'listo para servir'
                WHERE codigo = :codigo AND id_producto = :id_producto AND id_empleado = :id_empleado AND estado = 'en preparacion'";

        $consulta = $objetoAccesoDato->RetornarConsulta($sql);
        $consulta->bindValue(':id_producto', $id_producto, PDO::PARAM_INT);
        $consulta->bindValue(':id_empleado', $id_empleado, PDO::PARAM_INT);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();

        if ($consulta->rowCount() > 0) {
            return array('Status' => 'Pedido finalizado');
        } else {
            return array('Status' => 'No hay pedidos para finalizar');
        }
    }

    public static function mostrarPedidosXCodigo($codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos_secundario WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }

    public static function mostrarMesaXCodigoPedido($codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos_principal WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();
        $pedidos = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }

    public static function entregarPedidos($codigo){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $sql = "UPDATE pedidos_secundario 
                SET estado = 'entregado'
                WHERE codigo = :codigo AND estado = 'listo para servir'";
        $consulta = $objetoAccesoDato->RetornarConsulta($sql);
        $consulta->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consulta->execute();


        $sqlDos = "UPDATE pedidos_principal 
                SET estado = 'entregado'
                WHERE codigo = :codigo AND estado = 'con cliente esperando pedido'";
        $consultaDos = $objetoAccesoDato->RetornarConsulta($sqlDos);
        $consultaDos->bindValue(':codigo', $codigo, PDO::PARAM_STR);
        $consultaDos->execute();
    }
    

}