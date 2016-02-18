<?php
    class Cible_View_Helper_IsInArrays
    {
        public function isInArrays($key, $array){
            $fn = function($array) use ($key){
                return in_array($key, $array);
            };

            return in_array(true, array_map($fn, $array));
        }
    }