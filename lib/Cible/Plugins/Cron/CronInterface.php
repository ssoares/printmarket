<?php
interface Cible_Plugins_Cron_CronInterface
{
    public function __construct($args = null);

    /**
     * Lock
     * @return integer pid of this process
     * @throws Blahg_Plugin_Cron_Exception if already locked
     */
    public function lock();

    /**
     * Unlock
     * @return boolean true if successful
     * @throws Blahg_Plugin_Cron_Exception if an error occurs
     */
    public function unlock();

    /**
     * Is locked
     * @return integer|boolean pid of existing process or false if there isn't one
     */
    public function isLocked();

    public function run();
}