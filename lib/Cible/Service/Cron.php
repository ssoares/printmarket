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
class Cible_Service_Cron
{

    protected $_loader;
    protected $_actions = array();
    protected $_actionsArgs = array();
    protected $_errors = array();

    public function __construct(array $pluginPaths)
    {
        $this->_loader = new Zend_Loader_PluginLoader($pluginPaths);
    }

    /**
     * Get loader
     *
     * @return Zend_Loader_PluginLoader
     */
    public function getLoader()
    {
        return $this->_loader;
    }

    /**
     * Runs all registered cron actions.
     *
     * @return string any errors that may have occurred
     */
    public function run()
    {
        foreach ($this->_actions as $key => $action)
        {
            $class = $this->getLoader()->load($action);
            if ((bool)$this->_actionsArgs[$key])
            {
                $action = new $class;
                if (!($action instanceof Cible_Plugins_Cron_CronInterface))
                {
                    throw new Cible_Service_Exception('One of the specified actions is not the right kind of class.',0,null);
                }
                try
                {
                    $action->run();
                }
                catch (Cible_Plugin_Cron_Exception $e)
                {
                    $this->addError($e->getMessage());
                }
                catch (Exception $e)
                {
                    if (APPLICATION_ENV == 'development')
                    {
                        $this->addError('[DEV]: ' . $e->getMessage());
                    }
                    else
                    {
                        $this->addError('An undefined error occurred.');
                    }
                }
            }
        }
        $errors = $this->getErrors();
        if (count($errors) > 0)
        {
            $output = '&lt;html&gt;&lt;head&gt;&lt;title&gt;Cron errors&lt;/title&gt;&lt;/head&gt;&lt;body&gt;';
            $output .= '&lt;h1&gt;Cron errors&lt;/h1&gt;';
            $output .= '&lt;ul&gt;';
            foreach ($errors as $error)
            {
                $output .= '&lt;li&gt;' . $error . '&lt;/li&gt;';
            }
            $output .= '&lt;/ul&gt;';
            $output .= '&lt;/body&gt;&lt;/html&gt;';
        }
        else
        {
            $output = null;
        }
        return $output;
    }

    public function addAction($action, $args = null)
    {
        $key = count($this->_actions) + 1;
        $this->_actions[$key] = $action;
        $this->_actionsArgs[$key] = $args;
        return $this;
    }

    public function addError($message)
    {
        $this->_errors[] = $message;
        return $this;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

}
