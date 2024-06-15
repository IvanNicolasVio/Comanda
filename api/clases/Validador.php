<?php

    class Validador{
        public static function ValidarFuncion($string)
        {
            if($string === 'Bartender' || $string === 'Cervecero' || $string === 'Cocinero' || $string === 'Mozo' || $string === 'Socio')
            {
                return true;
            }
            else
            {
                echo json_encode(['error' => 'tipo invalido']);
                return false;
            }
        }

        public static function ValidarInt($int){
            if(is_string($int)){
                if((int)$int){
                    return (int)$int;
                }else{
                    echo json_encode(['error' => 'no es posible convertir']);
                    return false;
                }  
            }elseif(is_int($int)){
                return $int;
            }else{
                echo json_encode(['error' => 'no es strng']);
                return false;
            }
        }
    }

?>