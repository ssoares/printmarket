<?php
    class Cible_View_Helper_Dump
    {
        public function dump($var, $title='Debug'){
            $tmp = is_array($var) ? print_r($var, true) : $var;

            echo "<strong>$title</strong><br /><pre>$tmp</pre>";
        }
    }