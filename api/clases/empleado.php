<?php

include_once './clases/Validador.php';
include_once './db/AccesoDatos.php';
include_once './clases/ControlTiempo.php';

class Empleado{
    public $nombre;
    private $contrasenia;
    public $funcion;
    public $fechaAlta;
    public $fechaBaja;

    public function __construct($nombre,$contrasenia,$funcion)
    {
        $this->nombre = $nombre;
        $this->contrasenia = $contrasenia;
        $this->funcion = $funcion;
        $this->fechaAlta = Fecha::DarFechaActual();
        $this->fechaBaja = "";
    }

    public function DarAlta(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into empleados (contrasenia,nombre,funcion,fecha_alta,fecha_baja)values(:contrasenia,:nombre,:funcion,:fecha_alta,:fecha_baja)");
        $consulta->bindValue(':contrasenia', $this->contrasenia, PDO::PARAM_STR);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':funcion', $this->funcion, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_alta', $this->fechaAlta);
        $consulta->bindValue(':fecha_baja', $this->fechaBaja);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function CrearEmpleado($params){
        $nombre = isset($params['nombre']) ? $params['nombre'] : null;
        $contrasenia = isset($params['contrasenia']) ? $params['contrasenia'] : null;
        $funcion = isset($params['funcion']) ? $params['funcion'] : null;
        if(Validador::ValidarFuncion($funcion)){
            $empleado = new Empleado($nombre,$contrasenia,$funcion);
            $empleado->DarAlta();
            return $empleado;
        }else{
            return false;
        }
    }
    public static function DarBaja(){

    }
}