<?php

include_once '../db/AccesoDatos.php';

class Producto{
    public $id;
    public $nombre;
    public $tipo;
    public $tiempo_estimado;

    public function __construct($nombre,$tipo,$tiempo_estimado)
    {
        $this->nombre = $nombre;
        $this->tipo = $tipo;
        $this->tiempo_estimado = $tiempo_estimado;
    }
}