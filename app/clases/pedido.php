<?php

include_once './db/AccesoDatos.php';
include_once './clases/ControlTiempo.php';
include_once './clases/producto.php';

class Pedido{
    public $codigo;
    public $fechaPedido;
    
    public function __construct()
    {
        $this->codigo = substr(bin2hex(random_bytes(5)), 0, 5);;
        $this->fechaPedido = Fecha::DarFechaConHora();
    }

    public static function Cargar($params){
        $pedido = new Pedido();
        $nombre_cliente = $params['nombre'];
        $codigo_mesa = $params['mesa'];
        $productos = $params['pedido'];
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $data_array = json_decode($productos, true);
        foreach ($data_array as $item) {
            $precioProducto = Producto::ValidarProducto($item['id_producto']);
            $precioProducto = $precioProducto['valor'];
            $valorTotal = $precioProducto * $item['cantidad'];
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (codigo,codigo_mesa,nombre,id_producto,cantidad,estado,valor_total,fecha_pedido)values(:codigo,:codigo_mesa,:nombre,:id_producto,:cantidad,:estado,:valor_total,:fecha_pedido)");
            $consulta->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':codigo_mesa', $codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $item['id_producto'], PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $item['cantidad'], PDO::PARAM_INT);
            $consulta->bindValue(':estado', 'pendiente', PDO::PARAM_STR);
            $consulta->bindValue(':valor_total', $valorTotal, PDO::PARAM_INT);
            $consulta->bindValue(':fecha_pedido', $pedido->fechaPedido);
            $consulta->execute();
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
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM pedidos WHERE estado = 'pendiente' ");
        $consulta->execute();
        $pedidos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($pedidos) {
            return $pedidos;
        } else {
            return false;
        }
    }
}