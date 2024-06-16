<?php

include_once './db/AccesoDatos.php';

class Mesa{

    public $codigo;
    public $estado;

    public function __construct()
    {
        $this->codigo = substr(bin2hex(random_bytes(5)), 0, 5);
        $this->estado = "";
    }

    public function DarAlta(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into mesas (codigo,estado)values(:codigo,:estado)");
        $consulta->bindValue(':codigo', $this->codigo, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function CrearMesa(){
        $mesa = new Mesa();
        $mesa->DarAlta();
        return $mesa;
    }

}