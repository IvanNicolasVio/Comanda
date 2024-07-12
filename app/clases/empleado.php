<?php

include_once './db/AccesoDatos.php';
include_once './controllers/TiempoController.php';

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
        $this->fechaAlta = TiempoController::DarFechaActual();
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
        $empleado = new Empleado($params['nombre'],$params['contrasenia'],$params['funcion']);
        $empleado->DarAlta();
        return $empleado;
    }

    public static function ModificarEmpleado($nombre,$funcion){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE empleados SET funcion = :funcion WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':funcion', $funcion, PDO::PARAM_STR);
        $consulta->execute();
    }

    public static function AgregarFechaBaja($nombre){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("UPDATE empleados SET fecha_baja = :fecha_baja WHERE nombre = :nombre");
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindValue(':fecha_baja', TiempoController::DarFechaActual());
        $consulta->execute();
    }
    
    public static function MostrarEmpleados(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM empleados");
        $consulta->execute();
        $empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($empleados) {
            return $empleados;
        } else {
            return false;
        }
    }

    public static function MostrarEmpleadosAlta(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM empleados WHERE fecha_baja = '0000-00-00' ");
        $consulta->execute();
        $empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($empleados) {
            return $empleados;
        } else {
            return false;
        }
    }

    public static function MostrarEmpleadosXFuncion($funcion){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM empleados WHERE funcion = ? ");
        $consulta->bindValue(1, $funcion, PDO::PARAM_STR);
        $consulta->execute();
        $empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($empleados) {
            return $empleados;
        } else {
            return false;
        }
    }

    public static function TraerEmpleadoPorUsuarioContraseña($usuario,$contrasenia){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM empleados WHERE nombre = ? AND contrasenia = ? ");
        $consulta->bindValue(1, $usuario, PDO::PARAM_STR);
        $consulta->bindValue(2, $contrasenia, PDO::PARAM_STR);
        $consulta->execute();
        $empleado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($empleado) {
            return $empleado;
        } else {
            return false;
        }
    }

    public static function CheckNombre($usuario){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM empleados WHERE nombre = ?");
        $consulta->bindValue(1, $usuario, PDO::PARAM_STR);
        $consulta->execute();
        $empleado = $consulta->fetch(PDO::FETCH_ASSOC);
        if ($empleado) {
            return $empleado;
        } else {
            return false;
        }
    }

    public static function registrarIngreso($empleado){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into ingresos (nombre,fecha)values(:nombre,:fecha)");
        $consulta->bindValue(':nombre', $empleado['nombre'], PDO::PARAM_STR);
        $consulta->bindValue(':fecha',TiempoController:: DarFechaConHora());
        $consulta->execute();
    }

    public static function TraerIngresos($usuario){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT * FROM ingresos WHERE nombre = ?");
        $consulta->bindValue(1, $usuario, PDO::PARAM_STR);
        $consulta->execute();
        $empleado = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($empleado) {
            return $empleado;
        } else {
            return false;
        }
    }
}