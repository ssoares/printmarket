<?php

abstract class Cible_Extranet_Controller_Action extends Cible_Controller_Action
{
    protected $_mobileManagement = false;
    protected $_hasProfile = false;
    protected $_hasVideos = false;
    protected $_hasReindexation = false;

    protected $_headerWidth = "";
    protected $_headerHeight = "";
    protected $_imageSource = "";
    protected $_returnAfterCrop = "";
    protected $_cancelPage = "";
    protected $_moduleTitle = '';
    protected $_showActionButton = true;
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_filesFolder;
    protected $_rootFilesPath;
    protected $_editMode = false;
    protected $_cleanup = false;
    protected $_grayScale = false;
    protected $_tables = array();

    protected static $currentInterfaceLanguage = 1;

    public function init()
    {
        parent::init();

        $arrays = $this->_request->getParams();
        $this->view->assign('user', $this->view->auth());
//        if ($this->_isXmlHttpRequest && empty($this->view->user))
//            $this->_redirect($this->view->url());

        $session = new Cible_Sessions(SESSIONNAME);
        $this->view->imageSrc = $this->_imageSrc;
        if (isset($this->_config->embedVideos))
            $this->_hasVideos = (bool) $this->_config->embedVideos;

        if (isset($this->_config->hasReindexation))
        {
            $this->_hasReindexation = (bool) $this->_config->hasReindexation;
            $this->view->hasReindexation = $this->_hasReindexation;
        }

        if(isset($this->_config->mobileManagement))
            $this->_mobileManagement = (bool) $this->_config->mobileManagement;

        $okToList = false;
        if (isset($arrays['action']))
        {
            if (strlen($arrays['action']) > 3)
            {
                //echo substr($arrays['action'], 0, 4);
                if (substr($arrays['action'], 0, 4) == 'list')
                    $okToList = true;
            }
        }

        if ((isset($arrays['controller'])) && (isset($arrays['action'])))
        {
            if (($arrays['controller'] == 'static-texts') && ($arrays['action'] == 'index'))
                $okToList = true;

            $perPageSession = "perPage_" . $arrays['action'] . "_" . $arrays['controller'];
            $pageSession = "page_" . $arrays['action'] . "_" . $arrays['controller'];
        }
        if (isset($arrays['action']))
        {
            if(($arrays['action'] == 'list-all-images')||($arrays['action'] =='list-images'))
                $okToList = false;
        }

        if ($okToList == true && !$this->_isXmlHttpRequest)
        {
            $reload = false;

            $urlInfo = $this->_request->getPathInfo();
            $urlInfoT = strrev($urlInfo);
            if ($urlInfoT[0] != "/")
                $urlInfo .= "/";

            if (!empty($session->$perPageSession))
            {
                if ($this->_getParam('perPage'))
                {
                    $perPageVar = $this->_getParam('perPage');
                    if ($session->$perPageSession != $perPageVar)
                    {
                        $session->$perPageSession = $perPageVar;
                        $replaceP = "/perPage/" . $session->$perPageSession;
                        $oldPageP = "/\/perPage\/[0-9]*/";
                        $urlInfo = preg_replace($oldPageP, $urlInfo, $replaceP);
                        $reload = true;
                    }
                }
                else
                {
                    $urlInfoTmp = strrev($urlInfo);
                    if ($urlInfoTmp[0] == "/")
                        $urlInfo = $urlInfo . "perPage/" . $session->$perPageSession . "/";
                    else
                        $urlInfo = $urlInfo . "/perPage/" . $session->$perPageSession . "/";

                    $reload = true;
                }
            }
            else
                $session->$perPageSession = 10;

            if (!empty($session->$pageSession))
            {
                if ($this->_getParam('page'))
                {
                    $pageVar = $this->_getParam('page');
                    if ($session->$pageSession != $pageVar)
                    {
                        $session->$pageSession = $pageVar;
                        $replaceP = "/page/" . $session->$pageSession;
                        $oldPageP = "/\/page\/[0-9]*/";
                        $urlInfo = preg_replace($oldPageP, $urlInfo, $replaceP);
                        $reload = true;
                    }
                }
                else
                {
                    $urlInfoTmp = strrev($urlInfo);
                    if ($urlInfoTmp[0] == "/")
                        $urlInfo = $urlInfo . "page/" . $session->$pageSession . "/";
                    else
                        $urlInfo = $urlInfo . "/page/" . $session->$pageSession . "/";

                    $reload = true;
                }
            }
            else
                $session->$pageSession = 1;

            if ($reload == true)
                $this->_redirect($urlInfo);
        }

        // Defines the default interface language
        if ($this->_config->defaultInterfaceLanguage)
            $this->_defaultInterfaceLanguage = $this->_config->defaultInterfaceLanguage;

        // Check if the current interface language should be different than the default one
        $this->_currentInterfaceLanguage = !empty($session->languageID) ? $session->languageID : $this->_defaultInterfaceLanguage;

        if ($this->_getParam('setLang')){
            $this->_currentInterfaceLanguage = Cible_FunctionsGeneral::getLanguageID($this->_getParam('setLang'));
        }

        // Registers the current interface language for future uses
        $this->_registry->set('languageID', $this->_currentInterfaceLanguage);
        $session->languageID = $this->_currentInterfaceLanguage;
        $suffix = Cible_FunctionsGeneral::getLanguageSuffix($this->_currentInterfaceLanguage);
        $this->_registry->set('languageSuffix', $suffix);
        self::$currentInterfaceLanguage = $this->_currentInterfaceLanguage;
        Zend_Registry::set('Zend_Locale', new Zend_Locale($suffix));
        // Defines the default edit language
        if ($this->_config->defaultEditLanguage)
            $this->_currentEditLanguage = $this->_config->defaultEditLanguage;
        else
            $this->_currentEditLanguage = $this->_defaultEditLanguage;

        $this->_currentEditLanguage = !empty($session->currentEditLanguage) ? $session->currentEditLanguage : $this->_currentEditLanguage;

        // Check if the current edit language should be different than the default one
        if ($this->_getParam('lang'))
        {
            $this->_currentEditLanguage = Cible_FunctionsGeneral::getLanguageID($this->_getParam('lang'));
        }

        // Registers the current edit language for future uses
        $this->_registry->set('currentEditLanguage', $this->_currentEditLanguage);
        $session->currentEditLanguage = $this->_currentEditLanguage;

        if (Cible_FunctionsGeneral::extranetLanguageIsAvailable($this->getCurrentInterfaceLanguage()) == 0)
        {

            $session = new Cible_Sessions(SESSIONNAME);

            $this->_currentInterfaceLanguage = $this->_config->defaultInterfaceLanguage;

            // Registers the current interface language for future uses
            $this->_registry->set('languageID', $this->_currentInterfaceLanguage);
            $session->languageID = $this->_currentInterfaceLanguage;

            $suffix = Cible_FunctionsGeneral::getLanguageSuffix($this->_currentInterfaceLanguage);
            $this->_registry->set('languageSuffix', $suffix);
        }

        $modProfile = Cible_FunctionsModules::modulesProfile();

        if (count($modProfile) > 0)
            $this->_hasProfile = true;

        $this->setAcl();
        $this->view->assign('hasProfile', $this->_hasProfile);
        $this->view->assign('hasVideos', $this->_hasVideos);
        $this->view->assign('mobileManagement', $this->_mobileManagement);
    }

    public function cropimageAction(){
        $this->disableView();
        $this->_ID = empty($this->_ID)?'id':$this->_ID;
        $params = $this->_request->getParams();
        $image = $params['image'];
        $this->_showActionButton = $this->_isXmlHttpRequest ? false : true;
        $default = $this->_imgIndex;
        $new = false;
        $id = isset($params[$this->_ID])?$params[$this->_ID]:null;
        switch($params['mode']){
            case 'edit':
                $this->_editMode = $this->_moduleTitle != 'page' ? true : false;
                $this->_imgIndex = 'source';
                $info = $this->_setImageSrc(array($this->_imageSrc => $image),
                    $this->_imageSrc, $id, 'crop');
                $imageSource = $info['imgBasePath'] . "tmp/" . $image;
                if ($params['new']=='N'){
                    $base = rtrim(Zend_Registry::get('fullDocumentRoot'),'/');
                    if (file_exists($base . $info['imageSrc'])){
                        copy($base . $info['imageSrc'], $base . $imageSource);
                    }else{
                        copy($base . $info['imgBasePath'] .$image, $base . $imageSource);
                    }
                }else{
                    $new = true;
                }
                break;
            default:
                $new = true;
                $imageSource = $this->_rootImgPath . "tmp/" . $image;
                break;
        }
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            $data[$this->_imageSrc] = basename($formData['ImageSrc']);
            if ($new){
                $this->_imgIndex = 'source';
                $this->_setImage($this->_imageSrc, $data, $id, true);
            }
            $oCrop = New Cible_FunctionsCrop(null,$formData);
            $oCrop->cropImage();
            $this->_imgIndex = 'crop';
            $this->_setImage($this->_imageSrc, $data, $id, true);
            $this->_imgIndex = $default;
            if (!$new && !empty($id)){
                $this->_setImage($this->_imageSrc, $data, $id);
            }
            if($this->_isXmlHttpRequest){
                $imgInfo = $this->_setImageSrc($data, $this->_imageSrc, $id);
                if ($params['mode'] == 'edit'){
                    $thumbnailPath = $imgInfo['imageSrc'];
                }else{
                    $thumbnailPath = $this->_rootImgPath . "tmp/"
                        . $imgInfo['nameSize']
                        . $image;
                }
                echo $thumbnailPath;
                exit;
            }
        }
        else
        {
            $config = $this->_config->toArray();
            $headerWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
            $headerHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];

            $options = array('fileSource'=> $imageSource,
                'fileDestination'=> $imageSource,
                'image'=> $image,
                'mode'=> $params['mode'],
                'returnPage'=> $this->_returnAfterCrop,
                'cancelPage'=> $this->_cancelPage,
                'submitPage'=> $this->view->url(array('image' => $image,
                    'mode' => $params['mode'], 'new' => $params['new'])),
                'sizeXWanted'=> $headerWidth,
                'sizeYWanted'=> $headerHeight,
                'showActionButton'=> $this->_showActionButton);
            $oCrop = New Cible_FunctionsCrop($options);
            $oCrop->cropRenderImage();
        }
    }

    public static function currentInterfaceLanguage()
    {
        return self::$currentInterfaceLanguage;
    }

    public function copyLargeToCropAction()
    {
        $this->disableLayout();
        $module = $this->_moduleTitle;
        if ((bool)$this->_config->$module->active->crop){
            $prefixCrop = $this->_config->$module->source->crop->maxWidth . 'x'
                .$this->_config->$module->source->crop->maxHeight . '_';

            if (empty($this->_tmpData)
                && $this->_objectList[$this->_currentAction]){
                $oDataName = $this->_objectList[$this->_currentAction];
                $obj = new $oDataName();
                $this->_tmpData = $obj->getAll($this->_defaultEditLanguage);
            }else{
                throw new Exception('No data and this module does not contain the property $_objectList');
            }
            foreach($this->_tmpData as $data){
                $infos = $this->_setImageSrc($data, $this->_imageSrc, $data[$obj->getDataId()], 'original');
                $fileDest = $_SERVER['DOCUMENT_ROOT'] . dirname($infos['imageSrc'])
                    .'/' . $prefixCrop . $data[$this->_imageSrc];
                copy($_SERVER['DOCUMENT_ROOT'] . $infos['imageSrc'], $fileDest);
                var_dump($fileDest);
            }
        }else{
            echo "Crop function not enabled for this module";
        }
        exit;
    }

}
