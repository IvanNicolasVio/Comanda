<?php

include_once '../db/AccesoDatos.php';

class Pedido{
    public $id;
    public $id_mesa;
    public $nombre;
    public $cantidad;
    public $estado;
    public $tiempo_estimado;

    public function __construct($nombre,$cantidad,$tiempo_estimado)
    {
        $this->nombre = $nombre;
        $this->cantidad = $cantidad;
        $this->tiempo_estimado = $tiempo_estimado;
    }
}