<?php

include_once './db/AccesoDatos.php';

class Encuesta{
    public static function Llenar($params){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into encuesta (codigo_mesa,codigo_pedido,mesa,restaurante,mozo,cocinero,descripcion)values(:codigo_mesa,:codigo_pedido,:mesa,:restaurante,:mozo,:cocinero,:descripcion)");
        $consulta->bindValue(':codigo_mesa', $params['codigo_mesa'], PDO::PARAM_STR);
        $consulta->bindValue(':codigo_pedido', $params['codigo_pedido'], PDO::PARAM_STR);
        $consulta->bindValue(':mesa', $params['mesa'], PDO::PARAM_INT);
        $consulta->bindValue(':restaurante', $params['restaurante'], PDO::PARAM_INT);
        $consulta->bindValue(':mozo', $params['mozo'], PDO::PARAM_INT);
        $consulta->bindValue(':cocinero', $params['cocinero'], PDO::PARAM_INT);
        $consulta->bindValue(':descripcion', $params['descripcion'], PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function traerMejoresEncuestas(){
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("SELECT codigo_mesa, mesa, restaurante, mozo, cocinero, descripcion, 
                                                        (mesa + restaurante + mozo + cocinero) / 4.0 AS promedio
                                                        FROM encuesta
                                                        ORDER BY promedio DESC
                                                        LIMIT 2");
        $consulta->execute();
        $encuestas = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($encuestas) {
            return $encuestas;
        } else {
            return false;
        }
    }
}