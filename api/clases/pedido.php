<?php

include_once './db/AccesoDatos.php';

class Pedido{
    public $codigo;
    

    public function __construct()
    {
        $this->codigo = substr(bin2hex(random_bytes(5)), 0, 5);;
    }

    public static function Cargar($params){
        $pedido = new Pedido();
        $nombre_cliente = $params['nombre'];
        $codigo_mesa = $params['mesa'];
        $productos = $params['pedido'];
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $data_array = json_decode($productos, true);
        foreach ($data_array as $item) {
            $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into pedidos (codigo,nombre,id_mesa,id_producto,cantidad,estado,tiempo)values(:codigo,:nombre,:id_mesa,:id_producto,:cantidad,:estado,:tiempo)");
            $consulta->bindValue(':codigo', $pedido->codigo, PDO::PARAM_STR);
            $consulta->bindValue(':nombre', $nombre_cliente, PDO::PARAM_STR);
            $consulta->bindValue(':id_mesa', $codigo_mesa, PDO::PARAM_STR);
            $consulta->bindValue(':id_producto', $item['id_producto'], PDO::PARAM_INT);
            $consulta->bindValue(':cantidad', $item['cantidad'], PDO::PARAM_INT);
            $consulta->bindValue(':estado', 'pendiente', PDO::PARAM_STR);
            $consulta->bindValue(':tiempo',null);
            $consulta->execute();
        }
        return $pedido->codigo;
    }
}