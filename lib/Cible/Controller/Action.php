<?php

abstract class Cible_Controller_Action extends Zend_Controller_Action implements Cible_Controller_SiteMapInterface {

    const SEPARATOR = '||';
    const IS_MOBILE = 'mobile';
    const IS_TABLET = 'tablet';

    protected $_db;
    protected $_moduleID = 0;
    protected $_isXmlHttpRequest = false;
    protected static $defaultEditLanguage = 1;
    protected $_defaultEditLanguage = 1;
    protected $_currentEditLanguage = 1;
    protected $_defaultInterfaceLanguage = 1;
    protected $_currentInterfaceLanguage = 1;
    protected $_siteType = 's';
    protected $_currentSite = '';
    protected $_config;
    protected $_registry;
    protected $type;
    protected $_showActionButton = true;
    protected $_showBlockTitle = false;
    protected $_showBlockId = 0;
    protected $_addSubFolder = false;
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_filesFolder;
    protected $_rootFilesPath;
    protected $_moduleTitle = '';
    protected $_currentAction = '';
    protected $_isSecured = false;
    protected $_blockId = null;
    protected $_duplicateId = 0;
    protected $_defaultAdapter = null;
    protected $_duplicateData = array();
    protected $_cibleAdmin = false;
    protected $_device = null;
    protected $_deviceType = null;
    protected $_imageSrc = '';
    protected $_isNewImage = true;
    protected $_previousImage = '';
    protected $_dataId = null;
    protected $_imgIndex = 'image';
    protected $_dataIdField = '';
    protected $_editMode = false;

    public function siteMapAction(array $dataXml = array()) {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $xmlString = "";
        if (count($dataXml) > 0) {
            $xmlString = header('Content-Type: text/xml');
            $xmlString .= '<?xml version="1.0" encoding="UTF-8"?>';
            $xmlString .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
            xmlns:xsi="https://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
            https://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

            foreach ($dataXml as $i) {
                $xmlString .= "<url><loc>";
                $xmlString .= $i[0];
                $xmlString .= '</loc><lastmod>';
                $xmlString .= $i[3];
                $xmlString .= '</lastmod><changefreq>';
                $xmlString .= $i[2];
                $xmlString .= '</changefreq><priority>';
                $xmlString .= $i[1];
                $xmlString .= '</priority></url>';
            }
            $xmlString .= '</urlset>';
        }

        echo $xmlString;
    }

    public function getRobot() {

    }

    /**
     * Set the if the block id must be displayed.
     *
     * @return void
     */
    public function setBlockId($value) {
        $this->_showBlockId = $value;
        $this->view->blockId = $value;
        $this->_blockId = $value;
    }

    /**
     * Set the if the block title must be displayed.
     *
     * @return void
     */
    public function setShowBlockTitle($value) {
        $this->_showBlockTitle = $value;
    }

    /**
     * Set the module id.
     *
     * @return int
     */
    public function setModuleId() {
        $this->_moduleID = Cible_FunctionsModules::getModuleIDByName($this->_request->getModuleName());
    }

    /**
     * Get the module id from controller.
     *
     * @return int
     */
    public function getModuleID() {
        return $this->_moduleID;
    }

    /**
     * Get the typ os site from controller.
     *
     * @return char
     */
    public function getSiteType() {
        return $this->_siteType;
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init() {
        parent::init();

        if (!defined('ISCRONJOB'))
            $this->switchProtocol();

        $this->_autoloadModulesClasses();

        $this->_registry = Zend_Registry::getInstance();

        $this->view->assign('current_module', $this->_request->getModuleName());
        $this->view->assign('protocol', Zend_Registry::get('protocol'));
        if ($this->_getParam('enableDictionnary')) {
            $this->_registry->set('enableDictionnary', 'true');
            $this->view->assign('enableDictionnary', 'true');
        } else {
            $this->_registry->set('enableDictionnary', 'false');
            $this->view->assign('enableDictionnary', 'false');
        }

        $this->_db = $this->_registry->get('db');

        $this->_config = $this->_registry->get('config');

        if ($this->_config->defaultEditLanguage) {
            $this->_defaultEditLanguage = $this->_config->defaultEditLanguage;
            self::$defaultEditLanguage = $this->_config->defaultEditLanguage;
        }
        if ($this->_config->dictionnary_is_allowed == true)
            $this->_registry->set('dictionnary_is_allowed', 'true');
        else
            $this->_registry->set('dictionnary_is_allowed', 'false');

        $_request = $this->_request;

        $session = new Cible_Sessions(SESSIONNAME);
        if ($this->_request->isXmlHttpRequest()) {


            if (!empty($session->languageID))
                $this->_registry->set('languageID', $session->languageID);

            if (!empty($session->siteType))
                $this->_siteType = $session->siteType;

            $this->_isXmlHttpRequest = true;
            Zend_Registry::set('isXmlHttpRequest', $this->_isXmlHttpRequest);
            $this->disableLayout();

            if ($_request->isPost()) {
                foreach ($_request->getPost() as $key => $value)
                    $_request->setPost($key, $value);

                $this->setRequest($_request);
            }
        } else {

            $this->view->assign('params', $_request->getParams());
        }
        $siteType = $_request->getParam('site');

        if (!empty($siteType)) {
            $this->_siteType = $siteType;
            $session->siteType = $this->_siteType;
        } elseif (isset($session->siteType))
            $this->_siteType = $session->siteType;

        if (Zend_Registry::isRegistered('currentSite'))
            $this->_currentSite = Zend_Registry::get('currentSite');

        if (!preg_match('/extranet/', FRONTEND)) {
//            $this->setDevice();
//            $this->setDeviceType();
        }

        $islogged = Cible_FunctionsGeneral::getAuthentication();
        Zend_Registry::set('user', $islogged);
        $this->setShowBlockTitle((bool) $this->_getParam('showHeader'));
        $this->setBlockId($this->_getParam('BlockID'));

        $dataPath = SESSIONNAME == 'extranet' ? "../../" : "../";
        $dataPath .= $this->_config->document_root
                . '/' . $session->currentSite . "/data/";

        $this->_imagesFolder = $dataPath
                . "images/";
            if (!empty($this->_moduleTitle)){
            $this->_imagesFolder .= $this->_moduleTitle . "/";
        }

        $this->_rootImgPath = Zend_Registry::get("www_root")
                . $session->currentSite . "/data/images/";
        if (!empty($this->_moduleTitle)){
            $this->_rootImgPath .= $this->_moduleTitle . "/";
        }
        $this->_filesFolder = $dataPath
                . "files/";

        $this->_rootFilesPath = Zend_Registry::get("fullDocumentRoot")
            . Zend_Registry::get("www_root")
            . $session->currentSite . "/data/files/";
        if ($this->_addSubFolder) {
            $this->_imagesFolder .= $this->_currentAction . '/';
            $this->_rootImgPath .= $this->_currentAction . '/';
            $this->_filesFolder .= $this->_currentAction . '/';
            $this->_rootFilesPath .= $this->_currentAction . '/';
        }

        Zend_Registry::set('imagesFolder', $this->_imagesFolder);
        Zend_Registry::set('rootImgPath', $this->_rootImgPath);
        Zend_Registry::set('rootFilesPath', $this->_rootFilesPath);

        $this->view->showBlockTitle = $this->_showBlockTitle;
        $this->view->assign('request', $_request);



    }

    public static function getDefaultEditLanguage() {
        return self::$defaultEditLanguage;
    }

    public static function getRobotString() {
        return self::getRobot();
    }

    public function getCurrentInterfaceLanguage() {
        return $this->_currentInterfaceLanguage;
    }

    public function getCurrentEditLanguage() {
        return $this->_currentEditLanguage;
    }

    /*     * *
     * disableLayout enables you to disable layout wihtout having to remember how to do it with the layout helper
     *
     */

    protected function disableLayout() {
        $this->_helper->layout->disableLayout();
    }

    /*     * *
     * disableView enables you to disable the viewrenderer wihtout having to remember how to do it with the viewrenderer helper
     *
     */

    protected function disableView() {
        $this->_helper->viewRenderer->setNoRender();
    }

    protected function retrieveActions() {

        $class = new ReflectionClass($this);
        $methods = array();

        foreach ($class->getMethods() as $_method) {
            $_name = $_method->getName();

            if ($_method->isProtected() || $_method->isPublic()) {
                if (strlen($_name) > 6 && substr($_name, -6) == 'Action') {
                    array_push($methods, $_name);
                }
            }
        }

        return $methods;
    }

    protected function retrieveProperties() {

        $class = new ReflectionClass($this);
        $properties = array();

        foreach ($class->getProperties() as $_property) {
            $_name = $_property->getName();

            if (strlen($_name) > 6 && substr($_name, -6) == 'Action') {
                array_push($properties, $_name);
            }
        }

        return $properties;
    }

    public function toExcelAction() {
        if (empty($this->filename))
            throw new Exception('You must define $this->filename for the output filename');

        if (empty($this->select))
            throw new Exception('You must define $this->select a select statement');

        if (empty($this->fields))
            throw new Exception('You must define $this->fields as an array with all the fields');


        $this->disableLayout();
        $this->disableView();

        if ($this->select) {

            if ($this->filters) {
                $filters = $this->filters;

                foreach ($filters as $key => $filter) {
                    $filter_val = $this->_getParam($key);
                    if (!empty($filter_val))
                        $this->select->where("{$filter['associatedTo']} = ?", $filter_val);
                }
            }

            if ($this->_getParam('order')) {

                if (in_array($this->_getParam('order'), array_keys($this->fields))) {

                    $direction = 'ASC';
                    if (in_array($this->_getParam('order-direction'), array('ASC', 'DESC')))
                        $direction = $this->_getParam('order-direction');

                    $this->select->order("{$this->_getParam('order')} {$direction}");
                }
            }

            $searchfor = $this->_getParam('searchfor');

            if ($searchfor) {

                $searching_on = array();
                $search_keywords = explode(' ', $searchfor);

                foreach ($this->tables as $table => $columns) {
                    foreach ($columns as $column) {
                        array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$searchfor}%"));
                        foreach ($search_keywords as $keyword)
                            array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$keyword}%"));
                    }
                }

                if (!empty($searching_on))
                    $this->select->where(implode(' OR ', $searching_on));
            }

            $results = $this->_db->fetchAll($this->select);

            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
            $cacheSettings = array('memoryCacheSize ' => '8MB');
            if (!PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings))
                die($cacheMethod . " caching method is not available" . EOL);

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);

            $column = 0;
            foreach ($this->fields as $field_name => $field_value) {
                if (is_array($field_value)){
                    if (!empty($field_value['label'])){
                        $label = $field_value['label'];
                    }elseif(!empty($field_value['useFormLabel'])){
                        $label = $this->view->getCibleText("form_label_{$field_name}");
                    }else{
                        $label = $this->view->getCibleText("list_column_{$field_name}");
                    }
                }else{
                    $label = $field_value;
                }
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $label);
                $column++;
            }

            $key = 2;
            foreach ($results as $value) {

                foreach (array_keys($this->fields) as $i => $field_value) {
                    if (!empty($value[$field_value])) {
                        $val = $value[$field_value];
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $key, $val);
                    }
                }
                $key++;
            }

            // load the appropriate IO Factory writer
            switch ($this->type) {
                case 'Excel5':
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->type);
                    // output the appropriate headers
                    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    header("Content-Disposition: attachment;filename={$this->filename}");
                    break;

                case 'CSV':
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->type);
                    $objWriter->setDelimiter(';');
                    $objWriter->setLineEnding("\r\n");
                    $objWriter->setUseBOM(true);
                    header("Content-type: application/vnd.ms-excel");
                    header("Content-Disposition: attachment;filename={$this->filename}");
                    break;

                default:
                    if (empty($this->type))
                        $this->type = 'Excel2007';
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $this->type);
                    // output the appropriate headers
                    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    header("Content-Disposition: attachment;filename={$this->filename}");
                    break;
            }

            // output the file
            $objWriter->save('php://output');

            exit;
        }
    }

    public function toPdfAction() {
        if (empty($this->filename))
            throw new Exception('You must define $this->filename for the output filename');

        if (empty($this->select))
            throw new Exception('You must define $this->select a select statement');

        if (empty($this->fields))
            throw new Exception('You must define $this->fields as an array with all the fields');


        $this->disableLayout();
        $this->disableView();

        if ($this->select) {

            if ($this->filters) {
                $filters = $this->filters;

                foreach ($filters as $key => $filter) {
                    $filter_val = $this->_getParam($key);
                    if (!empty($filter_val))
                        $this->select->where("{$filter['associatedTo']} = ?", $filter_val);
                }
            }

            if ($this->_getParam('order')) {

                if (in_array($this->_getParam('order'), $this->fields)) {

                    $direction = 'ASC';
                    if (in_array($this->_getParam('order-direction'), array('ASC', 'DESC')))
                        $direction = $this->_getParam('order-direction');

                    $this->select->order("{$this->_getParam('order')} {$direction}");
                }
            }

            $searchfor = $this->_getParam('searchfor');

            if ($searchfor) {

                $searching_on = array();
                $search_keywords = explode(' ', $searchfor);

                foreach ($this->tables as $table => $columns) {
                    foreach ($columns as $column) {
                        array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$searchfor}%"));
                        foreach ($search_keywords as $keyword)
                            array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$keyword}%"));
                    }
                }

                if (!empty($searching_on))
                    $this->select->where(implode(' OR ', $searching_on));
            }

            $results = $this->_db->fetchAll($this->select);

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);


            foreach (array_keys($this->fields) as $i => $field_value) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, 1, $field_value);
            }

            $key = 2;
            foreach ($results as $value) {

                foreach (array_keys($this->fields) as $i => $field_value) {
                    if (!empty($value[$field_value]))
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $key, $value[$field_value]);
                }
                $key++;
            }

            // load the appropriate IO Factory writer
            $objWriter = new PHPExcel_Writer_PDF($objPHPExcel);
            // output the appropriate headers
            header("Content-type: application/pdf");
            header("Content-Disposition: attachment;filename={$this->filename}");

            // output the file
            $objWriter->save('php://output');

            exit;
        }
    }

    /**
     * Create an csv file for data export using PHPExcel library.
     *
     * @return void
     */
    public function toCsvAction() {
        if (empty($this->filename))
            throw new Exception('You must define $this->filename for the output filename');

        if (empty($this->select))
            throw new Exception('You must define $this->select a select statement');

        if (empty($this->fields))
            throw new Exception('You must define $this->fields as an array with all the fields');


        $this->disableLayout();
        $this->disableView();

        if ($this->select) {

            if ($this->filters) {
                $filters = $this->filters;

                foreach ($filters as $key => $filter) {
                    $filter_val = $this->_getParam($key);
                    if (!empty($filter_val))
                        $this->select->where("{$filter['associatedTo']} = ?", $filter_val);
                }
            }

            if ($this->_getParam('order')) {

                if (in_array($this->_getParam('order'), array_keys($this->fields))) {

                    $direction = 'ASC';
                    if (in_array($this->_getParam('order-direction'), array('ASC', 'DESC')))
                        $direction = $this->_getParam('order-direction');

                    $this->select->order("{$this->_getParam('order')} {$direction}");
                }
            }

            $searchfor = $this->_getParam('searchfor');

            if ($searchfor) {

                $searching_on = array();
                $search_keywords = explode(' ', $searchfor);

                foreach ($this->tables as $table => $columns) {
                    foreach ($columns as $column) {
                        array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$searchfor}%"));
                        foreach ($search_keywords as $keyword)
                            array_push($searching_on, $this->_db->quoteInto("{$table}.{$column} LIKE ?", "%{$keyword}%"));
                    }
                }

                if (!empty($searching_on))
                    $this->select->where(implode(' OR ', $searching_on));
            }

            $results = $this->_db->fetchAll($this->select);

            $objPHPExcel = new PHPExcel();

            $objPHPExcel->setActiveSheetIndex(0);

            $column = 0;
            $key = 1;
            // Insert columns label, if needed set $key = 2
            if ($this->addColumnsLabel) {
                foreach ($this->fields as $field_name => $field_value) {

                    if (is_array($field_value)) {
                        $label = !empty($field_value['label']) ? $field_value['label'] : $this->view->getCibleText("list_column_{$field_name}");
                    } else {
                        $label = trim($field_value);
                    }

                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $label);
                    $column++;
                }
                $key = 2;
            }
            foreach ($results as $value) {

                foreach (array_keys($this->fields) as $i => $field_value) {
                    if (isset($value[$field_value]))
                        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i, $key, trim($value[$field_value]));
                }
                $key++;
            }

            // load the appropriate IO Factory writer
            switch ($this->type) {
                case 'Excel5':
                    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                    // output the appropriate headers
                    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    header("Content-Disposition: attachment;filename={$this->filename}");
                    // output the file
                    $objWriter->save('php://output');
                    break;

                case 'CSV':
                    $objWriter = new PHPExcel_Writer_CSV($objPHPExcel);
                    $objWriter->setDelimiter(';');
                    $objWriter->setLineEnding("\r\n");
                    // Save file on the server
                    if ($this->_exportFilesFolder)
                        $objWriter->save($this->_exportFilesFolder . $this->filename);
                    else
                        $objWriter->save('php://output');

                    break;

                default:
                    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
                    // output the appropriate headers
                    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
                    header("Content-Disposition: attachment;filename={$this->filename}");
                    // output the file
                    $objWriter->save('php://output');
                    break;
            }
        }
    }

    public function ajaxCitiesAction() {
        if ($this->_isXmlHttpRequest) {
            $this->getHelper('viewRenderer')->setNoRender();

            $stateId = $this->_getParam('stateId');
            $cities = new CitiesObject();
            $citiesData = $cities->getCitiesDataByStates($stateId);

            foreach ($citiesData as $id => $data) {
                $citiesData[$id]['C_Name'] = $data['C_Name'];
            }

            echo json_encode($citiesData);
        }
    }

    public function ajaxStatesAction() {
        if ($this->_isXmlHttpRequest) {
            $this->getHelper('viewRenderer')->setNoRender();

            $countryId = $this->_getParam('countryId');
            $languageId = $this->_getParam('langId');
            $statesData = array();
            $states = Cible_FunctionsGeneral::getStateByCode(
                            $countryId, null, $languageId);

            if (is_array($states)) {
                foreach ($states as $id => $data) {
                    $statesData[$id]['id'] = $data['id'];
                    $statesData[$id]['name'] = $data['name'];
                }
            }

            echo json_encode($statesData);
        }
    }

    public function ajaxAction() {
        $action = $this->_getParam('actionAjax');
        $this->disableView();
        $value = $this->_getParam('term');
        $state = $this->_getParam('state');
        $limit = $this->_getParam('limit');
        $list = array();

        switch ($action) {
            case 'citiesList':
                $oCity = new CitiesObject();

                if (!empty($value))
                    $data = $oCity->autocompleteSearch(
                            $value, $this->getCurrentInterfaceLanguage(), $limit, $state
                    );

                foreach ($data as $value)
                    array_push($list, $value['C_Name']);

                echo json_encode($list);
                break;
            case 'lostPassword':
                $email = $this->_getParam('email');

                if (empty($email)) {
                    echo json_encode(array('result' => 'fail', 'message' => 'email missing'));
                    return;
                }

                $profile = new GenericProfilesObject();
                $tmp = $profile->findData(array('GP_Email' => $email));
                $user = !empty($tmp) ? $tmp[0] : array();
                if ($user) {
                    if (!Zend_Registry::isRegistered('languageSuffix')) {
                        $langs = Cible_FunctionsGeneral::getLanguageSuffix($user['GP_Language']);
                        Zend_Registry::set('languageSuffix', $langs);
                    }

                    $password = Cible_FunctionsGeneral::generatePassword();
                    $profile->save($user['GP_MemberID'], array('GP_Password' => $password, 'GP_Hash' => ''), $user['GP_Language']);

                    // send the mail
                    $data = array(
                        'email' => $email,
                        'PASSWORD' => $password,
                        'language' => $user['GP_Language'],
                    );
                    $options = array(
                        'send' => true,
                        'isHtml' => true,
                        'moduleId' => $this->_moduleID,
                        'event' => 'newPassword',
                        'type' => 'email',
                        'recipient' => 'client',
                        'data' => $data
                    );

                    new Cible_Notifications_Email($options);
                } else {
                    echo json_encode(array('result' => 'fail', 'message' => $this->view->getClientText('lost_password_email_not_found')));
                    return;
                }

                echo json_encode(array('result' => 'success', 'message' => $this->view->getClientText('lost_password_sent')));
                break;
            case 'locationName':
                $oObj = new StudiesObject();
                $field = 'SI_LocationName';
                if (!empty($value))
                    $data = $oObj->autocompleteSearch(
                            $value, $this->_currentEditLanguage, $limit, $field
                    );

                foreach ($data as $value)
                    array_push($list, $value[$field]);
                echo json_encode($list);
                break;
            case 'locationAddr':
                $oObj = new StudiesObject();
                $field = 'SI_LocationAddr';
                if (!empty($value))
                    $data = $oObj->autocompleteSearch(
                            $value, $this->_currentEditLanguage, $limit, $field
                    );

                foreach ($data as $value)
                    array_push($list, $value[$field]);

                echo json_encode($list);
                break;
            case 'setEnv':
                $session = new Zend_Session_Namespace(SESSIONNAME);
                $session->currentSite = $value;
                $this->setAcl();
                echo json_encode($session->currentSite);
                break;
            case 'pageSiteSrc':
                Zend_Registry::set('pageSiteSrc', $value);
                break;
            case 'clearCache':
                $session = new Zend_Session_Namespace(SESSIONNAME);
                $session->parameters = array();
                break;
            case 'findLink':
                $oBlocks = new BlocksObject();
                $hasLinks = (bool) $oBlocks->setProperties(array('pageId' => $value))->linksExist();
                echo json_encode($hasLinks);
                break;
            default:
                break;
        }
    }

    public function setAcl(Zend_Auth $auth = null, Zend_Auth_Adapter_DbTable $adapter = null, $login = false) {
        $session = new Zend_Session_Namespace(SESSIONNAME);
        $dbs = Zend_Registry::get('dbs');
        if (is_null($auth))
            $auth = Zend_Auth::getInstance();
        if ($login) {
            if (is_null($adapter))
                $adapter = new Zend_Auth_Adapter_DbTable($this->_db, 'Extranet_Users', 'EU_Username', 'EU_Password', 'MD5(?)');

            $user = array(
                'EU_ID',
                'EU_LName',
                'EU_FName',
                'EU_Email',
                'EU_DefaultSite',
                'EU_SiteAccess',
                'EU_ShowError'
            );
            $userData = $adapter->getResultRowObject($user);
            $userData->isAdminCible = $this->_cibleAdmin;
            $auth->getStorage()->write($userData);

            // build ACL rights
            $data = (array) $auth->getStorage()->read();
            if (!empty($data['EU_DefaultSite']))
                $session->currentSite = $data['EU_DefaultSite'];
            $userId = $data['EU_ID'];
        }
        elseif (!empty($session->currentSite) && !is_null($auth->getStorage()->read())) {
            $this->_cibleAdmin = $auth->getStorage()->read()->isAdminCible;
//            $dbAdapter = $dbs->getDb($session->currentSite);
//            Zend_Db_Table::setDefaultAdapter($dbAdapter);
//            Zend_Registry::set('db', $dbAdapter);
            $userId = $auth->getStorage()->read()->EU_ID;
        }
        if (!empty($userId)) {
            $session->acl = Cible_FunctionsAdministrators::getACLUser($userId);
        }
    }

    /**
     * Fecth modules using data indexation and load class to allow
     * Cible_FunctionsIndexation::indexationBuild() to load data.
     *
     * @return void
     */
    private function _autoloadModulesClasses() {
        $modules = Cible_FunctionsModules::getModules(true);

        foreach ($modules as $module) {
            $className = $module['M_Indexation'];
            Zend_Loader::autoload($className);
        }
    }

    public function setAlertMsg($alertMsg) {
        $this->_alertMsg = $this->view->AlertMessage($alertMsg);
        $this->view->placeholder('alert')->set($this->_alertMsg);
    }

    public function loginAction() {
        $this->setModuleId();
        $account = Cible_FunctionsGeneral::getAuthentication();

        if (is_null($account)) {
            $path = Zend_Registry::get('web_root');
            setcookie('returnUrl', $this->view->selectedPage, 0, $path);
            $message = $this->_getParam('message');
            if (!empty($message))
                $this->view->assign('message', $message);

            $form = new FormLogin();
            $noSubmitOther = true;
            if (isset($_POST['submit']))
                $noSubmitOther = false;
            elseif (isset($_POST['unsubscribe']))
                $noSubmitOther = false;
            elseif (isset($_POST['subscribe']))
                $noSubmitOther = false;
            elseif (isset($_POST['submitFind']))
                $noSubmitOther = false;

            if ($this->_request->isPost() && $noSubmitOther) {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData)) {
                    $result = Cible_FunctionsGeneral::authenticate($formData['emailLogin'],
                        $formData['password']);

                    if ($result['success'] == 'true' && empty($result['validatedEmail']) && $result['status'] == 2) {
                        $this->disableView();
                        $hash = md5(session_id());
                        $duration = $formData['stayOn'] ? time() + (60 * 60 * 24 * 30) : 0;
                        $cookie = array(
                            'lastName' => $result['lastName'],
                            'firstName' => $result['firstName'],
                            'email' => $result['email'],
                            'language' => $result['language'],
                            'hash' => $hash,
                            'status' => $result['status'],
                            'member_id' => $result['member_id']
                        );

                        setcookie("authentication", json_encode($cookie), $duration, $path);

                        $memberProfile = new GenericProfilesObject();
                        $memberProfile->save($result['member_id'], array('GP_Hash' => $hash), Zend_Registry::get('languageID'));

                        if ($this->_registry->isRegistered('pageID')) {
                            $pageId = $this->_registry->get('pageID');
                            $redirectUrl = $this->_request->getPathInfo();
                            if ($cookie['language'] != Zend_Registry::get('languageID')) {
                                $redirectUrl = Cible_FunctionsPages::getPageNameByID($pageId, $cookie['language']);
                                Zend_Registry::set('languageID', $cookie['language']);
                            }
                        }

//                        $redirectUrl = Cible_View_Helper_LastVisited::getLastVisited(1);
                        if (empty($redirectUrl)) {
                            $homePage = Cible_FunctionsPages::getHomePageDetails();
                            $redirectUrl = $homePage['PI_PageIndex'];
                        }

                        $this->_redirect($redirectUrl);
                    } else {
                        if ($result['success'] == 'true') {
                            if (!empty($result['validatedEmail']) || $result['status'] < 2) {
                                setcookie("authentication");
                                $url = Cible_FunctionsCategories::getPagePerCategoryView(0, 'confirm_email', $this->_moduleID);
                                $url .= '/email/' . $formData['emailLogin'];
                                $this->_redirect($url);
                            }
                        } else {
                            $error = Cible_Translation::getClientText('login_form_auth_fail_error');
                            $this->view->assign('error', $error);
                        }
                    }
                }
            }

            $this->view->assign('form', $form);
        } else {

            if (Zend_Registry::get('pageID') == $this->_config->authentication->pageId) {
                $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView(0, 'become_client', $this->_moduleID));
            }
            $this->disableView();
        }
    }

    public function captchaReloadAction() {
        $captcha_image = $this->view->formAddCaptcha(null, array('getCaptcha' => true));
        $image = $captcha_image->generate();
        $captcha['id'] = $captcha_image->getId();
        $captcha['word'] = $captcha_image->getWord();
        $captcha['url'] = $captcha_image->getImgUrl() . $image . $captcha_image->getSuffix();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        echo Zend_Json::encode($captcha);
    }

    private function _testDataForNotification(array $formData, $memberData) {
        $isModified = array();

        foreach ($formData as $key => $value) {
            if (!isset($memberData[$key]) && $key == 'duplicate')
                $isModified[$key] = $value;

            if (is_array($value))
                $isModified[$key] = $this->_testDataForNotification($value, $memberData[$key]);
            elseif (array_key_exists($key, $memberData) && !preg_match('/^password/', $key) && ($value != $memberData[$key])) {
                $isModified[$key] = $value;
            }
        }

        return $isModified;
    }

    public function modifyclientAction() {
        $this->becomeclientAction();
        $this->renderScript('index/becomeclient.phtml');
    }

    /**
     * Create a new account or edit an existing one.
     *
     * @return void
     */
    public function becomeclientAction()
    {
        $this->setModuleId();
        // Test if the user is already connected.
        $account = Zend_Registry::get('user');

        $_edit = false;
        // Instantiate the user profiles
        $profile = (null !== $this->_obj) ? $this->_obj : new MemberProfilesObject();
        $this->_dataIdField = $profile->getDataId();
        $profile->setOGeneric();
        $memberData = array();
        $accountValidate = true;
//        $addPage = Cible_FunctionsCategories::getPagePerCategoryView(0, 'becomeclient', $this->_moduleID, null, true);
//        $editPage = Cible_FunctionsCategories::getPagePerCategoryView(0, 'modifyclient', $this->_moduleID);
        $agreementError = false;
        // Get users data if he is already logged
        if ($account) {
            if ($account['status'] == 2 || $this->_request->isPost()) {
                $this->_editMode = $_edit = $this->_dataId > 0 ? true : false;
                $agreementError = false;
                $filters = array('GP_Email' => $account['email']);
                if ($this->_dataId){
                    $memberData = $profile->setId($this->_dataId)->findData($filters);
                }else{
                    $obj = $profile->getOGeneric();
                    $tmp = $obj->findData($filters);
                    $memberData = $tmp[0];
                }
//                $newsletterData = $newsletterProfile->findMember(array('email' => $account['email']));
                $this->view->modifTitle = $this->_obj->getModificationTitle();
                Zend_Registry::set('modifTitle', $this->view->modifTitle);
                if (preg_match('/' . $this->view->selectedPage . '/', $this->view->addPage)
                    && !empty($this->view->editPage)){
                    $this->_redirect($this->view->editPage);
                }
            }
        }

        $this->view->assign('accountValidate', $accountValidate);
        $imageSrc = '';
        $isNewImage = false;
        if (!empty($this->_imageSrc))
        {
            $imageSource = $this->_setImageSrc($memberData, $this->_imageSrc, $this->_dataId);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];
//            $imgBasePath = $imageSource['imgBasePath'];
//            $nameSize = $imageSource['nameSize'];
        }
        $options = $_edit ? array('mode' => 'edit') : array();
        $this->view->mode = $_edit ? 'edit' : 'add';
        if (null !== $this->_obj){
            $options['object'] = $this->_obj;
            $options['moduleName'] = $this->_moduleTitle;
            $options['dataId'] = $this->_dataId;
            $options['imageSrc'] = $imageSrc;
            $options['isNewImage'] = $isNewImage;
        }

        // Instantiate the form for account management
        $form = new FormBecomeClient($options);

        $this->view->assign('agreementError', $agreementError);
        // Actions when form is submitted
        if ($this->_request->isPost() && array_key_exists('submit', $_POST)) {
            $formData = $this->_request->getPost();
            $dataID = isset($formData['identification'])? $formData['identification'] : $formData;
            // Test if the users has ticked the aggreement checkbox
            $agreementError = isset($formData['termsAgreement']) && $formData['termsAgreement'] != 1 && !$_edit? true : false;
//            $nlData = $newsletterProfile->findMember(array('email' => $formData['identification']['email']));
            // Get the addresses data to insert
            if ($form->isValid($formData) && !$agreementError) {
                $oNotification = new Cible_Notifications_Email();
                if (!$_edit) {
                    // Do the processing here
                    $path = Zend_Registry::get('web_root');
                    $hash = md5(session_id());
                    $duration = 0;

//                    $validatedEmail = Cible_FunctionsGeneral::generatePassword();
//                    $formData['validatedEmail'] = $validatedEmail;
                    $formData['identification']['validatedEmail'] = '';
                    $formData['identification']['GP_Hash'] = $hash;
                    $formData['identification']['GP_Status'] = 2;
                    $formData['identification']['GP_Language'] = Zend_Registry::get('languageID');;
                    //Add addresses process and retrive id for memberProfiles
                    $idMember = $profile->addProfile($formData);
                    $this->_dataId = $profile->getId();
                    $this->_processImage($formData, $isNewImage);
                    $memberData = $formData['identification'];
                    $cookie = array(
                        'member_id' => $idMember,
                        'lastName' => $dataID['GP_LastName'],
                        'firstName' => $dataID['GP_FirstName'],
                        'email' => $dataID['GP_Email'],
                        'hash' => $hash,
                        'status' => 2
                    );
                    setcookie("authentication", json_encode($cookie), $duration, $path);

                    $dataAdm = array(
                        'firstname' => $dataID['GP_FirstName'],
                        'lastname' => $dataID['GP_LastName'],
                        'email' => $dataID['GP_Email'],
                        'language' => $dataID['GP_Language'],
                        'NEWID' => $idMember
                    );
                    $optionsAdm = array(
                        'send' => true,
                        'isHtml' => true,
                        'moduleId' => $this->_moduleID,
                        'event' => 'newAccount',
                        'type' => 'email',
                        'recipient' => 'admin',
                        'data' => $dataAdm
                    );
                    $password = $formData['identification']['GP_Password'];
                    $oNotification->process($optionsAdm);
                    $oNotification = new Cible_Notifications_Email();
                    $lgId = $this->_lang;
                    $link = $this->view->protocol . $this->_config->site->domainsName->$lgId;
                    $link .= '/' . $this->view->page;
                    $data = array(
                        'firstName' => $dataID['GP_FirstName'],
                        'lastName' => $dataID['GP_LastName'],
                        'email' => $dataID['GP_Email'],
                        'language' => $dataID['GP_Language'],
                        'link' => $link,
                        'password' => $password,
                    );
                    $options = array(
                        'send' => true,
                        'isHtml' => true,
                        'to' => $dataID['GP_Email'],
                        'moduleId' => $this->_moduleID,
                        'event' => 'newAccount',
                        'type' => 'email',
                        'recipient' => 'client',
                        'data' => $data
                    );

                    $oNotification->process($options);
                    Cible_FunctionsGeneral::authenticate($memberData['GP_Email'], $password);
                    $logData = array(
                        'L_ModuleID' => $this->_moduleID,
                        'L_UserID' => $idMember,
                        'L_Action' => 'newAccount',
                        'L_Data' => '',
                    );
//                    $oLog = new Cible_Log(array('userId' => $idMember, 'user' => $cookie));
//                    $oLog->log(array('data' => $logData));

                    if (!empty($this->view->redirectUrl)){
                        $this->_redirect($this->view->redirectUrl);
                    }else{
                        $this->renderScript('index/become-client-thank-you.phtml');
                    }
                }else {
                    $profile->save($this->_dataId, $formData, $memberData['GP_Language']);
                    $this->_processImage($formData, $isNewImage);
//                    if ($formData['email'] <> $memberData['email']) {
//                        $validatedEmail = Cible_FunctionsGeneral::generatePassword();
//                        $formData['validatedEmail'] = '';
//                        $profile->updateMember($memberData['member_id'], $formData);
//
//                        $confirm_page = Zend_Registry::get('absolute_web_root') . "/"
//                            . Cible_FunctionsCategories::getPagePerCategoryView(
//                                0, 'confirm_email', $this->_moduleID, $formData['language'])
//                            . "/email/{$formData['email']}/validateNumber/$validatedEmail";
//
//
//                        $this->view->assign('needConfirm', true);
//                        $this->renderScript('index/confirm-email.phtml');
//                        $data = array(
//                            'firstName' => $memberData['firstName'],
//                            'lastName' => $memberData['lastName'],
//                            'email' => $memberData['email'],
//                            'language' => $formData['language'],
//                            'validatedEmail' => '',
//                            'password' => $password,
//                        );
//                        $options = array(
//                            'send' => true,
//                            'isHtml' => true,
//                            'to' => $formData['email'],
//                            'moduleId' => $this->_moduleID,
//                            'event' => 'editResend',
//                            'type' => 'email',
//                            'recipient' => 'client',
//                            'data' => $data
//                        );
//
//                        $oNotification->process($options);
//                        $authentication = json_decode($_COOKIE['authentication'], true);
//                        $path = Zend_Registry::get('web_root');
//                        $duration = 0;
//                        $cookie = array(
//                            'member_id' => $memberData['GP_MemberID'],
//                            'firstname' => $memberData['GP_FirstName'],
//                            'lastname' => $memberData['GP_LastName'],
//                            'email' => $memberData['GP_Email'],
//                            'language' => $memberData['GP_Language'],
//                            'hash' => $authentication['hash'],
//                            'status' => 2
//                        );
//                        setcookie("authentication", json_encode($cookie), $duration, $path);
//                    } else {


                        $this->view->assign('messages', array($this->view->getCibleText('form_account_modified_message')));
                        $this->view->assign('updatedName', $memberData['GP_FirstName']);
                        $url = $this->view->url();
                        if (isset($memberData['TEI_Name'])
                            && $memberData['TEI_Name'] != $formData['TEI_Name']){
                            $name = $this->_dataId . '-'
                                . Cible_FunctionsGeneral::formatValueForUrl($formData['TEI_Name']);
                            $url = $this->view->url(array(
                                'action' => $name));
                        }
                        $this->_redirect($url);
                    }

//                    $data = array(
//                            'identification' => $memberData,
//                            'address'=> $addr);
//                    $notifyAdmin = $this->_testDataForNotification(
//                        $this->_request->getPost(),
//                        $data);
//                    $message = $this->view->getClientText('account_modified_admin_notification_message', $formData['language']);
//                    $titleAdmin = $this->view->getClientText('account_modified_admin_notification_title', $formData['language']);
                    $form->populate($formData);
                    $this->view->assign('form', $form);
                    // Notify admin
//                    if (count($notifyAdmin) > 0)
//                    {
//                        $states = Cible_FunctionsGeneral::getStatesByCountry($address['A_CountryId']);
//                        foreach ($states as $value)
//                            $tmpStates[$value['ID']] = $value['Name'];
//
//                        $view = $this->getHelper('ViewRenderer')->view;
//                        $view->assign('data', $notifyAdmin);
//                        $view->assign('states', $tmpStates);
//                        $changesList = $view->render('index/changesList.phtml');
//
//                        $this->_emailRenderData['emailHeader'] = $this->view->clientImage('logo.jpg', null, true);
//
//                        $message = str_replace('##firstname##', $memberData['firstName'], $message);
//                        $message = str_replace('##lastname##', $memberData['lastName'], $message);
//                        $message = str_replace('##email##', $memberData['email'], $message);
//                        $message = str_replace('##NEWID##', $memberData['member_id'], $message);
//                        $message = str_replace('##TABLE##', $changesList, $message);
//
//                        $this->_emailRenderData['message'] = $message;
//                        $this->_emailRenderData['footer'] = $this->view->getClientText("email_notification_footer", $formData['language']);
//                        $this->view->assign('emailRenderData', $this->_emailRenderData);
//                        $html = $view->render('index/emailNotification.phtml');
//
//                        $notify = new Cible_Notify(array(
//                                'isHtml' => '1',
//                                'to' => $this->_config->notification->accountCreation->sender,
//                                'from' => $this->_config->notification->accountCreation->sender,
//                                'title' => $titleAdmin,
//                                'message' => $html
//                            ));
//                        $notify->send();
//                    }
//                }
            } else{
                $form->populate($formData);
            }
        }elseif (($_edit && empty($this->view->step)) || !empty($memberData)){
            $form->populate($memberData);
        }
        $this->view->assign('form', $form);

    }

    private function _processImage($formData = array(), $isNewImage = true)
    {
        /* IMAGES */
        if (!empty($this->_imageSrc) && $this->_dataId > 0
            && !is_dir($this->_imagesFolder . $this->_dataId))
        {
            mkdir($this->_imagesFolder . $this->_dataId)
                or die("Could not make directory");
            mkdir($this->_imagesFolder . $this->_dataId . "/tmp")
                or die("Could not make directory");
        }
        if (isset($formData[$this->_imageSrc])
            && $formData[$this->_imageSrc] <> ''
            && $isNewImage){
            $this->_setImage($this->_imageSrc, $formData, $this->_dataId);
        }
    }


    public function thankYouAction() {
        $return = $this->_getParam('return');
        if ($return)
            $this->view->assign('return', $return);
    }

    public function switchProtocol() {
        $session = new Zend_Session_Namespace(SESSIONNAME);
        if ($this->_isSecured) {
            $url = 'https://' . $_SERVER['SERVER_NAME'] . $this->_request->getRequestUri();
            if (!isset($_SERVER['HTTPS'])) {
                $session->securedPage = $this->view->currentPageID;
                $this->_redirect($url);
            }
        } elseif (!$this->_isSecured) {
            $url = 'http://' . $_SERVER['SERVER_NAME'] . $this->_request->getRequestUri();
            if (isset($_SERVER['HTTPS'])) {
                if (!is_null($this->view->currentPageID) && $this->view->currentPageID != $session->securedPage) {
                    $session->securedPage = null;
                    $this->_redirect($url);
                }
            }
        }
    }

    protected function _testDuplicatedContent($testImgPath = false) {
        $this->_duplicateData = (array) Cible_FunctionsBlocks::getDuplicateData($this->_blockId, $this->view->languageId);

        if (!empty($this->_duplicateData)) {
            $dbs = Zend_Registry::get('dbs');
            $this->_defaultAdapter = $dbs->getDefaultDb();
            $this->_db = $dbs->getDb($this->_duplicateData['B_FromSite']);
            $this->_duplicateId = $this->_duplicateData['B_DuplicateId'];
            $imgPath = Zend_Registry::get('rootImgPath');
            if ($testImgPath) {
                $imgNewPath = preg_replace('~(.*)' . preg_quote($this->view->currentSite, '~') . '~', '$1' . $this->_duplicateData['B_FromSite'], $imgPath, 1);

                Zend_Registry::set('rootImgPath', $imgNewPath);
            }
        }
    }

    protected function _resetDefaultAdapter() {
        if (!is_null($this->_defaultAdapter))
            $this->_db = $this->_defaultAdapter;
    }

    public function setDevice() {
        $wurflConfigFile = RESOURCES_DIR . 'wurfl-config.xml';
        $wurflConfig = new WURFL_Configuration_XmlConfig($wurflConfigFile);
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
        $wurflManager = $wurflManagerFactory->create();
        $this->_device = $wurflManager->getDeviceForHttpRequest(filter_input_array(INPUT_SERVER));
    }

    public function setDeviceType() {
        $type = $this->_device->getCapability('device_os');
        $forceStandard = filter_input(INPUT_COOKIE, 'displayStandard');
        Zend_Registry::set('isMobile', false);
        //Zend_Registry::set('isMobile', true);
        if ($forceStandard != session_id()) {
            switch ($type) {
                case ''; //pour ie 11
                case 'Desktop':
                    $this->_deviceType = null;
                    break;
                default:
                    $this->_deviceType = self::IS_MOBILE;
                    if ($this->_device->getCapability('is_tablet') == 'true') {
                        $this->_deviceType = self::IS_TABLET;
                    } else {
                        //Zend_Registry::set('isMobile', true);
                        //pête le site présentement parce que les styles n'embarquent pas correctement dans le thème
                        Zend_Registry::set('isMobile', false);
                    }
                    break;
            }
        }else {
            setcookie('displayStandard', null, null);
        }
    }

    public function postDispatch() {
        if (Zend_Registry::isRegistered('isMobile'))
            $mobile = Zend_Registry::get('isMobile');
        else
            $mobile = false;

        if ($mobile) {
            $tplView = self::IS_MOBILE . '-' . $this->_request->getActionName();
            $paths = $this->view->getScriptPaths();
            $script = $paths[0] . $this->_request->getControllerName() . '/';
            if (file_exists($script . $tplView . '.phtml')) {
                $this->render($tplView);
            }
        }
    }

    /**
     * Test if the image is a new one and return path and status flag.
     *
     * @param array $record Data form database.
     * @param string $source The field to get filename.
     * @param int $recordID The id of the current record.
     * @param string $format The size format to fetch parameter from config file.
     *
     * @return array
     */
    protected function _setImageSrc($record, $source, $recordID, $format = 'thumb')
    {
        // image src.
        $config = $this->_config->toArray();
        $thumbMaxHeight = $config[$this->_moduleTitle][$this->_imgIndex][$format]['maxHeight'];
        $thumbMaxWidth = $config[$this->_moduleTitle][$this->_imgIndex][$format]['maxWidth'];
        $imgBasePath = $this->_rootImgPath;
        if ($recordID > 0){
            $imgBasePath .= $recordID . "/";
        }
        $nameSize = $thumbMaxWidth . 'x' . $thumbMaxHeight . '_';

        if (!empty($record[$source]))
        {
            $this->view->assign('imageUrl', $imgBasePath
                . str_replace(
                    $record[$source],
                    $nameSize . $record[$source],
                    $record[$source])
                );
            $this->_isNewImage = false;
        }

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
//            array_merge(
//                $data['productFormLeft'], $data['productFormRight'], $data['productFormBottom']);
            $postedImg = $this->_getPostedImg($formData, $source, $recordID);
            if ($postedImg['isset'] && (empty($record[$source]) || $postedImg['value'] <> $record[$source]))
            {
                if ($formData[$source] == ""){
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                }else{
                    $imageSrc = $imgBasePath
                        . "tmp/mcith/mcith_"
                        . $formData[$source];
                }
                $this->_isNewImage = true;
                $this->_previousImage = !empty($record[$source])?$record[$source] : '';
            }
            else
            {
                if ($record[$source] == ""){
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                }else{
                    $imageSrc = $imgBasePath
                        . str_replace(
                            $record[$source],
                            $nameSize . $record[$source], $record[$source]);
                }
                    $this->_isNewImage = false;
            }
        }
        else
        {
            if (!empty($recordID)){
                if (!is_dir($this->_imagesFolder . $recordID)){
                    mkdir($this->_imagesFolder . $recordID)
                        or die("Could not make directory");
                    mkdir($this->_imagesFolder . $recordID . "/tmp")
                        or die("Could not make directory");
                }
            }
            if (empty($record[$source])){
                $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
            }else{
                $imageSrc = $imgBasePath
                    . str_replace(
                        $record[$source],
                        $nameSize. $record[$source],
                        $record[$source]);
            }
        }
        $image = array('imageSrc' => $imageSrc, 'imgBasePath' => $imgBasePath, 'nameSize' => $nameSize, 'isNewImage' => $this->_isNewImage);
        if ($this->view->showCrop && isset($record[$source])){
            $sizes = $config[$this->_moduleTitle]['source']['crop'];
            $nameSize = $sizes['maxWidth'] . 'x' . $sizes['maxHeight'] . '_';
            $image['cropSrc'] = $imgBasePath . $nameSize . $record[$source];
    }
        return $image;
    }

    /**
     * Resizes and saves images file into folder according to modules parameters.
     *
     * @param string $source The key to get filename from data array.
     * @param array $newData The data sent by form.
     * @param int $recordID  The id of the current record.
     *
     * @return void
     */
    protected function _setImage($source, $newData, $recordID, $isCrop = false)
    {
        $config = $this->_config->toArray();
        $dimensions = $config[$this->_moduleTitle][$this->_imgIndex];

        if ($this->_editMode)
        {
            $srcImg = $this->_imagesFolder . $recordID . "/tmp/";
            if ($this->_cleanup){
                $this->_cleanupFolder($this->_imagesFolder . $recordID . '/');
            }
        }else{
            $srcImg = $this->_imagesFolder . "tmp/";
        }

        $destImg = $this->_imagesFolder . "tmp/";
        if (!empty($recordID)){
            $destImg = $this->_imagesFolder . $recordID . "/";
        }

        foreach ($dimensions as $size => $dims)
        {
            $tmpSrc = $srcImg . "{$size}_" . $newData[$source];
            copy($srcImg . $newData[$source], $tmpSrc);

            $maxWidth = $dims['maxWidth'];
            $maxHeight = $dims['maxHeight'];

            $name = str_replace(
                $newData[$source], $maxWidth . 'x' . $maxHeight . '_'
                . $newData[$source], $newData[$source]
            );
            $options = array(
                'src' => $tmpSrc,
                'maxWidth' => $maxWidth,
                'maxHeight' => $maxHeight);
            if (isset($dims['forceWidth'])){
                $options['forceWidth'] = $dims['forceWidth'];
            }
            if (isset($dims['forceHeight'])){
                $options['forceHeight'] = $dims['forceHeight'];
            }
            Cible_FunctionsImageResampler::resampled($options);

            if(isset($this->_grayScale) && $this->_grayScale){
                copy($tmpSrc, $destImg . $name);
                $options['grayScale']=true;
                Cible_FunctionsImageResampler::resampled($options);
                $name = str_replace(
                    $newData[$source], $maxWidth
                    . 'x'
                    . $maxHeight
                    . '_gray_'
                    . $newData[$source], $newData[$source]
                );
                rename($tmpSrc, $destImg . $name);
            }else{
                rename($tmpSrc, $destImg . $name);
            }
            if (file_exists($tmpSrc) && !$isCrop){
                unlink($tmpSrc);
            }
            if (file_exists($srcImg . $name) && !$isCrop){
                unlink($srcImg . $name);
            }
        }
        if ($this->view->showCrop && !$isCrop){
            $dimensions = $config[$this->_moduleTitle]['source'];
            foreach ($dimensions as $size => $dims)
        {
                $maxWidth = $dims['maxWidth'];
                $maxHeight = $dims['maxHeight'];
                $name = str_replace(
                    $newData[$source], $maxWidth . 'x' . $maxHeight . '_'
                    . $newData[$source], $newData[$source]);
                var_dump(file_exists($destImg . $name));
                var_dump(file_exists($srcImg . $name));
                exit;
                if (!file_exists($destImg . $name)
                    && file_exists($srcImg . $name)){
                    rename($srcImg . $name, $destImg . $name);
                    unlink($srcImg . $name);
                }elseif (!file_exists($destImg . $name)){
                    $options = array(
                        'src' => $srcImg . $newData[$source],
                        'maxWidth' => $maxWidth,
                        'maxHeight' => $maxHeight);
                    Cible_FunctionsImageResampler::resampled($options);
                    rename($srcImg . $newData[$source], $destImg . $name);
                }
            }
        }
        if (file_exists($srcImg . $newData[$source]) && !$isCrop){
            unlink($srcImg . $newData[$source]);
        }
        if (is_dir($srcImg . 'mcith/')){
            $this->_cleanupFolder($srcImg . 'mcith/');
        }
        if ($this->_isNewImage && !empty($this->_previousImage)){
            $files = glob($destImg . '*' . $this->_previousImage);
            foreach($files as $key => $file){
                unlink($file);
            }
        }
    }

    /**
     * Delete files from folder
     *
     * @param type $currentFolder
     */
    protected function _cleanupFolder($currentFolder)
    {
        $dirHandler = opendir($currentFolder);
        // for each file in the folder
        while (($file = readdir($dirHandler)) !== false)
        {
            $realPath = realpath($currentFolder . $file);
            if (filetype($realPath) == 'file')
                unlink($realPath);
        }
    }

    protected function _getPostedImg($formData, $source, $recordID)
    {
        $isset = false;
        $id = 0;
        foreach ($formData as $key => $value)
        {
            if (is_array($value))
            {
                $tmp = $this->_getPostedImg ($value, $source, $recordID);
                $isset = $tmp['isset'];
                $data = $tmp['value'];
                $id = isset($tmp['id']) ? $tmp['id'] : 0;
            }
            else
            {
                if (isset($formData[$source]))
                {
                    if (!empty($formData[$this->_dataIdField])){
                        $id = $formData[$this->_dataIdField];
                    }
                    $data = $formData[$source];
                    $isset = true;
                    break;
                }else{
                    $isset = false;
                }
            }
            if ($recordID > 0 && $id == $recordID){
                break;

            }
        }

        if ($isset){
            $return = array('isset' => true, 'value' => $data, 'id' => $id);
        }else{
            $return = array('isset' => false, 'value' => null);
        }
        return $return;
    }

}