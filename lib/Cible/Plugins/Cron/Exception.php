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
class Cible_Plugins_Cron_Exception extends Zend_Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null) {
        echo $message;
//        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return "[" . $this->code . "]: <span class=\"error\">" . $this->message . "</span>";
    }
}
?>
