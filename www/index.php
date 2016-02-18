<?php

header('X-UA-Compatible: IE=edge');
ini_set('magic_quotes_gpc', 'off');

// define the timezone
date_default_timezone_set('Canada/Eastern');
setlocale(LC_CTYPE, 'fr_CA.utf8');

$rootDir = dirname(dirname(__FILE__));
$application_path = "{$rootDir}/application";
$lib_path = "{$rootDir}/lib";

// loading configuration
$host = explode('.', $_SERVER['HTTP_HOST']);
if (in_array('sandboxes', $host) || in_array('localhost', $host)){
    $devUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $envVar = $devUri[1];
}else{
    if(strstr($host[0], 'www')){
        unset($host[0]);
    }
    $envVar = implode('.', $host);
}

define('APPLICATION_ENV', $envVar);
define('APPLICATION_PATH', $rootDir);
define('ISCRONJOB', false);
/**
 * Path to the GUI for the font office
 */
define("FRONTEND", $application_path . '/');

define("WURFL_DIR", $lib_path . '/Wurfl/'); // WURFL INSTALLATION DIR
define("RESOURCES_DIR", $rootDir . "/cache/wurfl/"); // DIRECTORY WHERE YOU PUT YOUR CONFIGURATION FILES
require_once WURFL_DIR . 'Application.php';
// setting up directories and loading of first classes
set_include_path('.'
    . PATH_SEPARATOR . "{$lib_path}"
    . PATH_SEPARATOR . "{$lib_path}/Cible/Models"
    . PATH_SEPARATOR . "{$lib_path}/QCal"
    . PATH_SEPARATOR . "{$lib_path}/TcPdf"
    . PATH_SEPARATOR . $lib_path . '/Wurfl/'
    . PATH_SEPARATOR . $rootDir . "/cache/wurfl/"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    array(
        'bootstrap' => array(
            'class' => 'Bootstrap_Multidb',
            'path' => $lib_path . '/Cible/Bootstrap/Multidb.php',
        ),
        'config' => $application_path . '/config/master.ini',
        'pluginPaths' => array(
            'Cible_Plugin_Cron' => $lib_path . '/Cible/Plugins/Resource'
        )
    )
);
$application->bootstrap()->run();

// Clear all variables so that they don't get in the global scope
unset($rootDir, $application_path);