<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Cron
 *
 * @author soaser
 */
class Cible_Plugin_Resource_Cron extends Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        $options = $this->getOptions();

        if (array_key_exists('pluginPaths', $options))
        {
            $cron = new Cible_Service_Cron($options['pluginPaths']);
        }
        else
        {
            $cron = new Cible_Service_Cron(array(
                    'Cible_Plugin_Cron' => realpath(APPLICATION_PATH . '/lib/Cible/Plugins/Cron/'),
                ));
        }
        if (array_key_exists('actions', $options))
        {
            foreach ($options['actions'] as $name => $args)
            {
                $cron->addAction($name, $args);
            }
        }
        return $cron;
    }

}
