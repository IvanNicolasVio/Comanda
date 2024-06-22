<?php

class Fecha
{
    public static function DarFechaActual()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fechaFormateada = date_create("now");
        $fechaFormateada = date_format($fechaFormateada, 'Y-m-d');
        return $fechaFormateada;
    }

    public static function DarFechaConHora()
    {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fechaFormateada = date_create("now");
        $fechaFormateada = date_format($fechaFormateada, 'Y-m-d H:i:s');
        return $fechaFormateada;
    }
}

?>