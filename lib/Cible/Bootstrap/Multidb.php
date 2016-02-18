<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//require_once 'Bootstrap.php';

/**
 * Description of Cron
 *
 * @author soaser
 */
class Bootstrap_Multidb extends Zend_Application_Bootstrap_Bootstrap {

    const NAME = 'multidb';
    const CFG_EXT = '.ini';

    /**
     * String to define if we are int the front office
     */
    const FO_NAME = '/application/';

    /**
     * String to define if we are int the back office
     */
    const BO_NAME = '/extranet/';

    protected $_extranetPath = '';
    protected $_applicationPath = '';
    protected $_currentSite = '';
    protected $_moduleDirectory = 'modules';
    protected $_importsList = array();

    public function run() {
        try {
            Zend_Registry::set('config', $this->config);
            $this->bootstrap('FrontController');
            $front = $this->getResource('FrontController');
            if (!preg_match(self::BO_NAME, FRONTEND)) {
                $router = $front->getRouter();
                $route = new Zend_Controller_Router_Route(
                        'rss/:lang/:catID/:feed', array(
                    'lang' => 'en',
                    'catID' => '1',
                    'feed' => 'rss.xml',
                    'module' => 'rss',
                    'controller' => 'index',
                    'action' => 'read'
                        )
                );
                $router->addRoute('rss', $route);
            }

            $front->dispatch();
        } catch (Exception $e) {
            echo 'An error has occured.' . PHP_EOL;
            echo "<pre>";
            echo($e);
            echo "</pre>";
            exit;
        }
    }

    /**
     * Initialize Module
     *
     * @return Zend_Application_Module_Autoloader
     */
    protected function _initAutoload() {
        /**
         * Path to the front-office folders.
         */
        $this->_applicationPath = APPLICATION_PATH . self::FO_NAME;
        /**
         * Path to the back-office folders.
         */
        $this->_appPath = FRONTEND;

        if (preg_match(self::FO_NAME, FRONTEND))
            define('SESSIONNAME', str_replace('/', '', self::FO_NAME));
        elseif (preg_match(self::BO_NAME, FRONTEND))
            define('SESSIONNAME', str_replace('/', '', self::BO_NAME));
    }

    protected function _initSession() {
        $session = new Zend_Session_Namespace(SESSIONNAME);
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity() && preg_match(self::BO_NAME, FRONTEND))
            $session->unsetAll();

        return $session;
    }

    protected function _initConfig() {
        $entity = '';
        // Load the config files with parameters form the front-office and back-office.
        if (isset($this->session->currentSite)
            && $this->session->currentSite != $this->_options['cfgKey']){
            $entity = $this->session->currentSite;
        }
        $config = $this->_setConfig($entity);

        return $config;
    }

    protected function _initLoadEntitiesConfig()
    {
        $list = array_values( preg_grep( '/^((?!master.ini|config.ini).)*$/', glob($this->_applicationPath . "config/*.ini") ) );
        $type = $this->_options['type'];
        foreach ($list as $value)
        {
            $tmpCfg = new Zend_Config_Ini($value);
            $file = pathinfo($value);
            $tmpConfigs[$file['filename']] = $multisite[] = array('name' => $file['filename'],
                'label' => $tmpCfg->general->label,
                'active' => $tmpCfg->$type->active,
                'firstRun' => $tmpCfg->$type->firstRun,
                'parent' => $tmpCfg->general->parent,
                );
            if ($tmpCfg->general->children){
                $tmpConfigs[$file['filename']]['children'] = $tmpCfg->general->children->toArray();
            }
        }
        $this->config->multisite = $multisite;
        $this->config->levels = array();
        $related = $this->_getFirstParentChildren($tmpConfigs);
        $this->_getParentChildrenLevels($tmpConfigs);
        $this->config->relatedEntities = $related;
        // ensure that the errors appear
        //error_reporting(E_ALL ^ E_NOTICE | E_STRICT);
        if (ISDEV){
            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', true);
            ini_set('xdebug.max_nesting_level', 2000);
        }

    }

    /**
     * Add databases to the registry
     *
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function _initDbRegistry() {
        $this->bootstrap('multidb');
        $multidb = $this->getPluginResource('multidb');
        Zend_Registry::set('dbs', $multidb);
        $dbAdapter = $multidb->getDefaultDb();

        try{
            if (isset($this->session->currentSite) && !preg_match(self::FO_NAME, FRONTEND)){

            }else {
            }

            $dbAdapter = $multidb->getDb($this->session->currentSite);
        }catch(Exception $exc){
            $currentDbName = $dbAdapter->fetchOne("select DATABASE();");
            foreach ($this->config->multisite as $data) {
                if ((bool) $data->active) {
                    $name = $data->name;
                    $dbName = $this->config->resources->multidb->$name->dbname;
                    if ($dbName === $currentDbName)
                        $this->session->currentSite = $data->name;
                }
            }
            echo $exc->getTraceAsString();
        }

        if (version_compare(phpversion(), '5.3.6', '<'))
            $dbAdapter->query('SET NAMES utf8');

        Zend_Db_Table::setDefaultAdapter($dbAdapter);
        Zend_Registry::set('db', $dbAdapter);

        return $dbAdapter;
    }

    protected function _initDefaultlanguage()
    {
        if (preg_match(self::FO_NAME, FRONTEND)){
            $langSuffix = substr(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2);
            $default = strtolower(Cible_FunctionsGeneral::getLanguageSuffix($this->config->defaultInterfaceLanguage));
            $langId = Cible_FunctionsGeneral::getLanguageID($langSuffix);
            $isActive = Cible_FunctionsGeneral::languageIsAvailable($langId);
            if ($isActive){
                $this->config->defaultInterfaceLanguage = $langId;
            }
        }

        $this->config->readOnly();
    }

    protected function _initView() {
        $lib_path = APPLICATION_PATH . "/lib";
        // Initialisons la vue
        $view = new Zend_View();

        $view->doctype('HTML5');
        $view->addHelperPath("Cible/View/Helper", "Cible_View_Helper");
        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->addBasePath("{$lib_path}/Cible/View");
        $view->addBasePath("{$lib_path}/ZendX/JQuery/View");
        $view->addBasePath("{$lib_path}/Cible/Validate");
        $view->assign('currentSite', $this->session->currentSite);
        $view->setEncoding('utf-8');
        mb_internal_encoding('utf-8');
        Zend_Controller_Action_HelperBroker::addHelper(new Cible_Controller_Action_Helper_SwitchDB());
        Zend_Controller_Action_HelperBroker::addHelper(new Cible_Controller_Action_Helper_WriteFile());
        Zend_Controller_Action_HelperBroker::addHelper(new Cible_Controller_Action_Helper_Reports());
        // Ajoutons la  au ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'ViewRenderer'
        );

        $viewRenderer->setView($view);
        Zend_Layout::startMvc(array('layoutPath' => $this->_appPath . "layouts"));
        // Retourner la vue pour qu'elle puisse être stockée par le bootstrap
        return $view;
    }

    protected function _initRequest() {
        // Vérifie que le controleur frontal est bien présent, et le récupére
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController');
        $front->addModuleDirectory($this->_appPath . $this->_moduleDirectory)
                ->throwExceptions(false);
        if (preg_match(self::BO_NAME, FRONTEND))
            $front->registerPlugin(new Cible_Plugins_Auth());

        // Initialise l'objet request
        $request = new Zend_Controller_Request_Http();

        // On l'ajoute au controleur frontal
        $front->setRequest($request);
        $this->view->request = $request;
        // Le bootstrap va stocker cette valeur dans la clé 'request'
        // de son conteneur
        return $request;
    }

    protected function _initCache() {
        $frontendOptions = array(
            'lifetime' => 0,
            'automatic_serialization' => false
        );
        /**
         *  Enable (static texts) cache lifetime accordingto the cache duration
         * value in the config (default value: 7200 = 2 hours)
         */
        if ((bool) $this->config->cache->enabled){
            $frontendOptions = array(
                'lifetime' => $this->config->cache->duration,
                'automatic_serialization' => true
            );
        }
        $tmpFolder = '/tmp/';
        // Directory where to put the cache files
        if (!is_dir(APPLICATION_PATH . $tmpFolder . $this->session->currentSite))
            mkdir(APPLICATION_PATH . $tmpFolder . $this->session->currentSite);
        $backendOptions = array(
            'cache_dir' => APPLICATION_PATH . $tmpFolder . $this->session->currentSite
        );
        $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
        if ((bool) $this->config->cache->enabled){
            Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        }
        Zend_Registry::set('cache', $cache);
    }

    protected function _initPaths() {
        $www_root = rtrim(dirname(dirname(filter_input(INPUT_SERVER, 'PHP_SELF'))), '/') . "/";
        if (empty($www_root)){
            $www_root = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/') . "/";
        }
        $tmpPath = "";
        $root = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $isHttpsOn = filter_input(INPUT_SERVER, 'HTTPS');
        if (empty($root)){
            $root = $_SERVER['DOCUMENT_ROOT'];
        }
        if (empty($isHttpsOn) && isset($_SERVER['HTTPS'])){
            $isHttpsOn = $_SERVER['HTTPS'];
        }

        $isSandbox = preg_match('/sandboxes/', $this->config->domainName);
        if (($isSandbox) && (!preg_match('/www/', $www_root))) {
            $tmpPath = $this->config->document_root . "/";
            $www_root .= $tmpPath;
        }
        $fullDocumentRoot = rtrim($root,'/') . $www_root;
        
        if($isSandbox){
            $fullDocumentRoot = rtrim($root);
        }
        $protocol = 'http://';
        if ($isHttpsOn){
            $protocol = 'https://';
        }
        Zend_Registry::set('protocol', $protocol);
        $this->view->assign('protocol', $protocol);
        $absolute_web_root = $protocol . $this->config->domainName . $www_root;

        if (preg_match(self::FO_NAME, FRONTEND)) {
            $this->_currentSite = $this->_options['cfgKey'];
        } elseif (!empty($this->session->currentSite)){
                $this->_currentSite = $this->session->currentSite;
                Zend_Registry::set('currentTheme', $this->session->currentSite);
        }
        $lucenePath = APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite . '/indexation/all_index';
        $lucenePathPdf = APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite . '/indexation/pdf';

        if (!is_dir(APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite)) {
            mkdir(APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite);
        }
        if (!is_dir(APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite . '/indexation/')) {
            mkdir(APPLICATION_PATH . "/{$this->config->document_root}/" . $this->_currentSite . '/indexation/');
            mkdir($lucenePath);
        }
        if (!is_dir($lucenePathPdf))
            mkdir($lucenePathPdf);

        Zend_Registry::set('lucene_index', $lucenePath);
        Zend_Registry::set('lucene_pdf', $lucenePathPdf);
        Zend_Registry::set('document_root', $www_root);
        Zend_Registry::set('serverDocumentRoot', $root);
        Zend_Registry::set('fullDocumentRoot', $fullDocumentRoot);
        Zend_Registry::set('logPath', $root . $www_root . '../log');
        Zend_Registry::set('web_root', $www_root);
        Zend_Registry::set('www_root', $www_root);
        Zend_Registry::set('absolute_web_root', $absolute_web_root);
        $lang = $this->config->defaultInterfaceLanguage;
        Zend_Registry::set('siteName', $this->config->site->title->$lang);

        $jquery = $this->view->jQuery();

        $jquery->setCdnVersion($this->config->jquery->version);
        $jquery->setUiCdnVersion($this->config->jquery->ui);
        $jquery->addStylesheet($this->view->locateFile("jquery-ui-1.8.2.custom.css", "jquery/smoothness"));

        $appConfigCronSend = $this->config->resources->cron->actions->SendNewsletter;
        Zend_Registry::set('appConfigCronSend', $appConfigCronSend);
        if ((bool)$this->config->log->active && ISDEV){
            $orderLogPath = Zend_Registry::get('logPath'). "/";
            $suffix      = date('Ymd');
            $logFileName = SESSIONNAME . '-' . APPLICATION_ENV .'-queries_' . $suffix . '.log';
            $file     = $orderLogPath . $logFileName;
            $stream = new Zend_Log_Writer_Stream($file, 'a');
            Zend_Registry::set('LOG', new Zend_Log($stream));
        }
    }

    /**
     * Initialize Modules path for commons modules and according to the current DB.
     *
     * @return void
     */
    protected function _initModulesPath()
    {
        $path = array();
        $modules = Cible_FunctionsModules::getModules();

        set_include_path('.' . $this->_appPath . "$this->_moduleDirectory"
                . PATH_SEPARATOR . $this->_appPath . "$this->_moduleDirectory/default/models/"
                . PATH_SEPARATOR . $this->_appPath . "$this->_moduleDirectory/search/models/"
                . PATH_SEPARATOR . get_include_path());

        foreach ($modules as $module){
            $path[] = $this->_appPath . "$this->_moduleDirectory/" . $module['M_MVCModuleTitle'] . "/models/";
        }
        set_include_path(get_include_path()
                . PATH_SEPARATOR . implode(PATH_SEPARATOR, $path));
        array_push($modules, array('M_MVCModuleTitle' => 'menu'));

        if (preg_match(self::BO_NAME, FRONTEND))
        {
            foreach ($this->config->multisite as $params) {
                if ((bool) $params->firstRun) {
                    $sitePath = APPLICATION_PATH . "/{$this->config->document_root}/" . $params->name;
                    if (!is_dir($sitePath))
                        mkdir($sitePath);

                    Cible_FunctionsModules::firstRun($modules, $sitePath);
                }
            }
        }
    }

    private function _setConfig($entity = "")
    {
        try
        {
            define('ISDEV', (bool)$this->_options['isdev']);
            if (empty($entity)){
                $entity = $this->_options['cfgKey'];
            }
            if (empty($this->session->currentSite)){
                $this->session->currentSite = $entity;
            }
            $configFile = 'config/' . $entity . self::CFG_EXT;
            $cfg = new Zend_Config_Ini($this->_applicationPath . $configFile, $this->_options['type'], true);
            $cfgGeneral = new Zend_Config_Ini($this->_applicationPath . $configFile, 'general', true);
            $cfgImages = new Zend_Config_Ini($this->_applicationPath . $configFile, 'images', true);

        } catch (Exception $ex) {
            echo TOP_LEVEL_ERROR;
//            echo $ex->getMessage();
            exit;
        }

        $defaultCfg = new Zend_Config_Ini($this->_appPath . "config/config.ini", 'general', true);
        $defaultImgCfg = new Zend_Config_Ini($this->_applicationPath . "config/config.ini", 'images', true);
        $config = new Zend_Config($this->getOptions(), true);
        $config->merge($defaultCfg);
        $config->merge($defaultImgCfg);
        if (!empty($cfgGeneral)){
            $config->merge($cfgGeneral);
        }
        if (!empty($cfgImages)){
            $config->merge($cfgImages);
        }
        $config->merge($cfg);

        Zend_Registry::set('config', $config);
        $this->setOptions($config->toArray());

        return $config;
    }

    private function _getFirstParentChildren($configs, $name = '')
    {

        $name = empty($name)?$this->config->name:$name;

        $hasParent = !empty($configs[$name]['parent']) ? true : false;
        if ($hasParent){
            $first = $this->_getFirstParentChildren($configs, $configs[$name]['parent']);
        }else{
            if (!empty($configs[$name]['children'])){
                $first[$name] = $configs[$name]['label'];
                foreach($configs[$name]['children'] as $child){
                    $first[$child] = $configs[$child]['label'];
                }
            }else{
                $first = array();
            }
        }

        return $first;
    }

    private function _getParentChildrenLevels($configs, $name = '', $level = 0)
    {
        $name = empty($name)?$this->config->name:$name;
        $test = $this->config->levels->toArray();
        $this->config->levels = array_merge($test, array($name => $level++));

        $hasParent = !empty($configs[$name]['parent']) ? true : false;
        if ($hasParent){
            $this->_getParentChildrenLevels($configs, $configs[$name]['parent'], $level);
        }
    }
}
