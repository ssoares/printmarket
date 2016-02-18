<?php
// ensure that the errors appear
header('X-UA-Compatible: IE=Edge');
ini_set('magic_quotes_gpc', 'off');

// define the timezone
date_default_timezone_set('Canada/Eastern');
setlocale(LC_CTYPE, 'fr_CA.utf8');
$rootDir = dirname(dirname(dirname(__FILE__)));
$application_path = "{$rootDir}/application";
$extranet_path = "{$rootDir}/extranet/";
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
define("WURFL_DIR", $lib_path . '/Wurfl/'); // WURFL INSTALLATION DIR
define("RESOURCES_DIR", $rootDir . "/cache/wurfl/"); // DIRECTORY WHERE YOU PUT YOUR CONFIGURATION FILES
/**
 * Path to the GUI for the back office
 */
define('FRONTEND', $extranet_path);

// setting up directories and loading of classes
set_include_path('.'
    . PATH_SEPARATOR . "$rootDir/lib"
    . PATH_SEPARATOR . "$rootDir/lib/Cible/Models/"
    . PATH_SEPARATOR . FRONTEND . "modules/profile/models/"
    . PATH_SEPARATOR . FRONTEND . "modules/users/models/"
    . PATH_SEPARATOR . FRONTEND . "modules/utilities/models/"
    . PATH_SEPARATOR . "{$lib_path}/TcPdf"
    . PATH_SEPARATOR . $lib_path . '/Wurfl/'
    . PATH_SEPARATOR . $rootDir . "/cache/wurfl/"
    . PATH_SEPARATOR . FRONTEND . "includes"
    . PATH_SEPARATOR . get_include_path());

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Application.php';
define('TOP_LEVEL_ERROR',
    '<h1>Web site configuration not found</h1>'
    . '<div style="margin-bottom:20px;">The URL you are trying to access does not exist. <br>'
    . 'Please validate the web site address or contact your administrator.</div>'
    ."<div>L'adresse du site que vous cherchez n'existe pas sur ce serveur."
    . "<br>Veuillez valider l'URL ou contactez votre administrateur.</div>");
try
{
    $application = new Zend_Application(
        APPLICATION_ENV,
        array(
        'bootstrap' => array(
            'class' => 'Bootstrap_Multidb',
            'path' => $lib_path . '/Cible/Bootstrap/Multidb.php',
        ),
        'config' => $application_path . '/config/master.ini',
        'pluginPaths' => array(
            'Cible_Plugin_Cron' => $lib_path . '/Cible/Plugins/Resource',
        ),
        )
    );
    $application->bootstrap()->run();
} catch(Exception $exc) {
    echo TOP_LEVEL_ERROR;
    echo $exc->getMessage();
    echo $exc->getTraceAsString();
}
// Clear all variables so that they don't get in the global scope
unset($rootDir, $application_path, $extranet_path, $lib_path);
