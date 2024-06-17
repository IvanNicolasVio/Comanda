<?php

include_once './db/AccesoDatos.php';

class Producto{
    public $nombre;
    public $sector;

    public function __construct($nombre,$sector)
    {
        $this->nombre = $nombre;
        $this->sector = $sector;
    }

    public function DarAlta(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into productos (nombre,sector)values(:nombre,:sector)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':sector', $this->sector, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function CrearProducto($params){
        $mesa = new Producto($params['nombre'],$params['sector']);
        $mesa->DarAlta();
        return $mesa;
    }
}