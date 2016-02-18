<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SendNewsletter
 *
 * @author soaser
 */
class Cible_Plugin_Cron_SendFDPFile implements Cible_Plugins_Cron_CronInterface
{
    const SEND_ACTION = 'sendFile';
    public function __construct($arg = null)
    {
    }

    public function run()
    {
        try
        {
            if (isset($_SERVER['argv'][1])
                && $_SERVER['argv'][1] == self::SEND_ACTION){
                $controller = Zend_Controller_Front::getInstance();
                $controller->setDefaultModule('donation');
                $controller->setDefaultControllerName('cron');
                $controller->setDefaultAction('send-fdp-file');
                $controller->dispatch();
            }
        }
        catch (Exception $e)
        {
            echo $e->__toString();
            exit;
        }

    }

    /**
     * Lock
     * @return integer pid of this process
     * @throws Blahg_Plugin_Cron_Exception if already locked
     */
    public function lock()
    {

    }

    /**
     * Unlock
     * @return boolean true if successful
     * @throws Blahg_Plugin_Cron_Exception if an error occurs
     */
    public function unlock()
    {

    }

    /**
     * Is locked
     * @return integer|boolean pid of existing process or false if there isn't one
     */
    public function isLocked()
    {

    }

}