<?php

include_once '../db/AccesoDatos.php';

class Mesa{

    public $id;
    public $nombreCliente;
    public $estado;
    public $foto;

    public function __construct($nombreCliente,$foto)
    {
        $this->nombreCliente = $nombreCliente;
        $this->foto = $foto;
    }
}