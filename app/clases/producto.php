<?php

include_once './db/AccesoDatos.php';

class Producto{
    public $nombre;
    public $sector;
    public $valor;

    public function __construct($nombre,$sector,$valor)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->valor = $valor;
    }

    public function DarAlta(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre,sector,valor)values(:nombre,:sector,:valor)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->bindValue(':valor', $this->valor, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function CrearProducto($params){
        $mesa = new Producto($params['nombre'],$params['sector'],$params['valor']);
        $mesa->DarAlta();
        return $mesa;
    }

    public static function ValidarProducto($id){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM productos WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        $producto = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($producto) {
            return $producto;
        } else {
            return false;
        }
    }

    public static function MostrarProductos(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM productos");
        $consulta->execute();
        $productos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($productos) {
            return $productos;
        } else {
            return false;
        }
    }

    

}