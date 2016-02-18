<?php

date_default_timezone_set('Canada/Eastern');
setlocale(LC_CTYPE, 'fr_CA.utf-8');

$rootDir = dirname(dirname(__FILE__));
$application_path = "{$rootDir}/application";
$extranet_path = "{$rootDir}/extranet/";
$lib_path = "{$rootDir}/lib";

// loading configuration
$envVar = 'production';
$host = array();
if (isset($_SERVER['HTTP_HOST']))
    $host = explode('.', $_SERVER['HTTP_HOST']);
if (empty($host))
{
    $tmpPath = str_replace('/', '.', $rootDir);
    $host = explode('.', trim($tmpPath, '.'));
}
if (in_array('sandboxes', $host) || in_array('localhost', $host))
{
    if (isset($_SERVER['REQUEST_URI']))
    {
        $devUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $envVar = $devUri[1] . '-dev';
    }
    else
        $envVar = end($host) . '-dev';
}
elseif (in_array('dev', $host))
    $envVar = $host[0] . '-dev';
elseif (in_array('staging', $host))
{
    if (isset($_SERVER['argv'][1]))  // si multi site (ex: n45 et polyform ( php /home/cible/vhost_file/n45.staging.ciblesolutions.com/cron/cron.php polyform
        $host[3] = $_SERVER['argv'][1];  //  et  php /home/cible/vhost_file/n45.staging.ciblesolutions.com/cron/cron.php n45    
    $envVar = $host[3] . '-staging'; // empty var prndra le $host[3] normal ec: php /home/cible/vhost_file/n45.staging.ciblesolutions.com/cron/cron.php
}
else
    $envVar = $host[1];


define('APPLICATION_ENV', $envVar);
define('APPLICATION_PATH', $rootDir);
define("ISCRONJOB", true);
/**
 * Path to the GUI for the back office
 */
define('FRONTEND', $extranet_path);

// setting up directories and loading of classes
set_include_path('.'
    . PATH_SEPARATOR . "$rootDir/cron"
    . PATH_SEPARATOR . "$rootDir/lib"
    . PATH_SEPARATOR . "$rootDir/lib/Cible/Models/"
    . PATH_SEPARATOR . FRONTEND . "modules/profile/models/"
    . PATH_SEPARATOR . FRONTEND . "modules/users/models/"
    . PATH_SEPARATOR . FRONTEND . "modules/utilities/models/"
    . PATH_SEPARATOR . FRONTEND . "includes"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
    Zend_Loader::registerAutoload();

require_once 'Zend/Application.php';

$application = new Zend_Application(
        APPLICATION_ENV,
        array(
            'bootstrap' => array(
                'class' => 'Bootstrap_Cron',
                'path' => $lib_path . '/Cible/Bootstrap/Cron.php',
            ),
            'config' => $application_path . '/config.ini',
        ),
        array(
            'pluginPaths' => array(
                'Cible_Plugin_Cron' => $lib_path . '/Cible/Plugins/Resource'
            )
        )
);
$application->bootstrap()->run();

// Clear all variables so that they don't get in the global scope
unset($rootDir, $application_path, $extranet_path, $lib_path);
