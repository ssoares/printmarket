<?php
abstract class Cible_Plugins_Cron_CronAbstract implements Cible_Plugins_Cron_CronInterface
{
    public function lock()
    {
        if ($pid = $this->isLocked()) {
            throw new Cible_Plugin_Cron_Exception('This task is already locked.');
        }

        $pid = getmypid();
        if (!file_put_contents($this->_getLockFile(), $pid)) {
            throw new Cible_Plugin_Cron_Exception('A lock could not be obtained.');
        }

        return $pid;
    }

    public function unlock()
    {
        if (!file_exists($this->_getLockFile())) {
            throw new Cible_Plugin_Cron_Exception('This task is not locked.');
        }

        if (!unlink($this->_getLockFile())) {
            throw new Cible_Plugin_Cron_Exception('The lock could not be deleted.');
        }

        return true;
    }

    public function isLocked()
    {
        if (!file_exists($this->_getLockFile())) {
            return false;
        }

        return true;
    }

    protected function _getLockFile()
    {
        $fileName = 'cron.' . get_class($this) . '.lock';
        $lockFile = realpath(APPLICATION_PATH . '/localtmp/') . '/' . $fileName;
        return $lockFile;
    }
}