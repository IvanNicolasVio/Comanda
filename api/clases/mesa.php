<?php

include_once './db/AccesoDatos.php';

class Mesa{

    public $codigo;
    public $estado;

    public function __construct()
    {
        $this->codigo = substr(bin2hex(random_bytes(5)), 0, 5);
        $this->estado = "cerrada";
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

    public static function CambiarEstado($codigoMesa,$estado){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE mesas SET estado = :estado WHERE codigo = :codigo");
        $consulta->bindValue(':codigo', $codigoMesa, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $estado, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function MostrarMesas(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas");
        $consulta->execute();
        $empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($empleados) {
            return $empleados;
        } else {
            return false;
        }
    }

    public static function MostrarSinUso(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas WHERE estado = 'cerrada' ");
        $consulta->execute();
        $mesas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($mesas) {
            return $mesas;
        } else {
            return false;
        }
    }

    public static function MostrarEnUso(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM mesas WHERE estado != 'cerrada' ");
        $consulta->execute();
        $mesas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($mesas) {
            return $mesas;
        } else {
            return false;
        }
    }

}