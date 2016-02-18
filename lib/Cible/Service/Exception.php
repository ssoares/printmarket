<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Exception
 *
 * @author soaser
 */
class Cible_Service_Exception extends Zend_Service_Exception
{
    public function __construct($message, $code, $previous)
    {
//        parent::__construct($message, $code, $previous);
        echo $message;
        exit;
    }
}
?>
