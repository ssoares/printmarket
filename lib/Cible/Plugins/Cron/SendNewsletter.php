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
class Cible_Plugin_Cron_SendNewsletter
    implements Cible_Plugins_Cron_CronInterface
{

    protected $_release;

    public function __construct($args = null)
    {
        if (is_array($args) && array_key_exists('release', $args))
        {
            $this->_release = $args['release'];
//            throw new Cible_Plugins_Cron_Exception("The FileToucher cron task plugin is not configured correctly.", 0, null);
        }
    }

    public function run()
    {

        if (!empty($this->_release))
        {
            var_dump($this->_release);
        }
        else
        {
            try
            {
                $controller = Zend_Controller_Front::getInstance();
                $controller->setDefaultModule('newsletter');
                $controller->setDefaultControllerName('cron');
                $controller->setDefaultAction('send-newsletter');
                $controller->dispatch();

            }
            catch (Exception $e)
            {
                echo $e->__toString();
                exit;
            }

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
?>
