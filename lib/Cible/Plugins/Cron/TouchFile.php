<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TouchFile
 *
 * @author soaser
 */
class Cible_Plugin_Cron_TouchFile implements Cible_Plugins_Cron_CronInterface
{

    protected $_filename;

    public function __construct($args = null)
    {
        if (!is_array($args) || !array_key_exists('filename', $args))
        {
            throw new Cible_Plugins_Cron_Exception("The FileToucher cron task plugin is not configured correctly.", 0, null);
        }
        $this->_filename = $args['filename'];
    }

    public function run()
    {
        $result = touch($this->_filename);
        
        if (!$result)
        {
            throw new Cible_Plugins_Cron_Exception('The file timestamp could not be updated.', 0 , null);
        }
    }

    /**
     * Lock
     * @return integer pid of this process
     * @throws Blahg_Plugin_Cron_Exception if already locked
     */
    public function lock(){}

    /**
     * Unlock
     * @return boolean true if successful
     * @throws Blahg_Plugin_Cron_Exception if an error occurs
     */
    public function unlock(){}

    /**
     * Is locked
     * @return integer|boolean pid of existing process or false if there isn't one
     */
    public function isLocked(){}

}
