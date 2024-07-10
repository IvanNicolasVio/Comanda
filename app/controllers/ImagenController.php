<?php

  class ImagenController
  {
    public static function FotoMesa($mesa,$codigo, $fileFoto)
    {
        $nombreTemporal = $fileFoto['tmp_name'];
        $nombreOriginal = $fileFoto['name'];
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $directorio = "./ImagenesDeMesas/2024";
        $nombreNuevo = "mesa_" .$mesa . "_pedido_" . $codigo . "." . $extension;
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        $rutaGuardado = $directorio . "/" . $nombreNuevo;
        if (move_uploaded_file($nombreTemporal, $rutaGuardado)) {
            return $rutaGuardado;
        } else {
            return false;
        }
    }
}