<?php

class Page_ManageController extends Cible_Controller_Block_Abstract
{
    protected $_moduleID  = '999';
    protected $_moduleTitle   = 'page';
    protected $_imagesFolder;
    protected $_folderMenu = 'menu';
    protected $_rootImgPath;
    protected $_imgMenuFolder;
    protected $_rootMenuImgPath;
    protected $_imageSrc = 'ImageHeader';

    public function init()
    {
        $this->view->headScript()->appendFile($this->view->locateFile('tinymce.min.js', 'tinymce'));
        $this->view->headScript()->appendFile($this->view->locateFile('moxman.loader.min.js', 'tinymce/plugins/moxiemanager/js'));



        $this->view->jQuery()->addJavascriptFile($this->view->locateFile('jquery.maskedinput.min.js','jquery'));
        $this->view->jQuery()->addJavascriptFile($this->view->locateFile('jquery.prettyPhoto.js', 'jquery', 'front'));
        $this->view->headLink()->appendStylesheet($this->view->locateFile('prettyPhoto.css', '', 'front'), 'screen');

        parent::init();

        $this->_imgMenuFolder = $this->_imagesFolder
                              . '../' . $this->_folderMenu . "/";
        $this->_rootMenuImgPath = $this->_rootImgPath
                                . '../' . $this->_folderMenu . "/";
    }

    /**
     * Initiates data of importation files.
     * Scan the folder containing import files and format some data.
     * Save data into database to be used in the import actions.
     *
     * @return array
     */
    protected function _findImagesFiles()
    {
        $filesList = array();
        $imageFile = $this->_imagesFolder . "header/";

        if (is_dir($imageFile))
        {
            $dirHandler = opendir($imageFile);
            if ($dirHandler)
            {
                // for each file in the folder
                while (($file = readdir($dirHandler)) !== false)
                {
                    // get name and data (date of modif...)
                    $realPath = realpath($imageFile . $file);
                    $info     = pathinfo($imageFile . $file);
                    $fileName = $info['filename'];
                    $root = Zend_Registry::get('serverDocumentRoot')
                        . '/' . $this->_config->document_root;
                    $prefix = $this->_config->page->image->original->maxWidth
                        . 'x' . $this->_config->page->image->original->maxHeight
                        . '_';
                    // store it in an array
                    if (filetype($realPath) == 'file'
                        && !preg_match('/^([0-9]*x[0-9]*_)/',$fileName)){
                        $filesList[$fileName] = $info['basename'];
                    }elseif(preg_match('/^'.$prefix.'/',$fileName)){
                        $name = str_replace($prefix, '', $fileName);
                        rename($realPath, $imageFile.$info['basename']);
                        $filesList[$name] = $name . '.' . $info['extension'];
                    }
                }

                closedir($dirHandler);
            }
        }
        sort($filesList);
        // return this array
        return $filesList;
    }

    /**
     * Initiates data of importation files for the background image.
     * Scan the folder containing import files and format some data.
     * Save data into database to be used in the import actions.
     *
     * @return array
     */
    protected function _findBackImagesFiles()
    {
        $filesList = array();
        $imageBackFile = $this->_imagesFolder . "background/";


        if (is_dir($imageBackFile))
        {
            $dirHandler = opendir($imageBackFile);
            if ($dirHandler)
            {
                // for each file in the folder
                while (($file = readdir($dirHandler)) !== false)
                {
                    // get name and data (date of modif...)
                    $realPath = realpath($imageBackFile . $file);
                    $info     = pathinfo($imageBackFile . $file);
                    $fileName = $info['filename'];
                    // store it in an array
                    if (filetype($realPath) == 'file')
                    {
                        $filesList[$fileName] = $info['basename'];
                    }
                }
                closedir($dirHandler);
            }
        }
        sort($filesList);
        return $filesList;
    }

    function indexAction(){
        // retrieve the ID of the requested page
        $pageID = (int)$this->view->pageID = $this->_getParam( 'ID' );

        $authData = $this->view->user;
        $authID     = $authData['EU_ID'];
        $hasAccess = true;
        $hasAccessToContent = false;
        $hasCurrentPageAccess = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$pageID,"structure", $this->view->isAdministrator());
        if ($hasCurrentPageAccess)
            $hasAccessToContent = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$pageID,"data", $this->view->isAdministrator());
        if (!$hasCurrentPageAccess)
        {
            $isPageAdmin = $this->view->aclIsAllowed($this->_moduleTitle, 'edit', true);
            if ($isPageAdmin)
                $hasAccessToContent = true;
        }

        if (!$hasCurrentPageAccess && !$isPageAdmin)
            $hasAccess = false;

        if ($hasAccess)
        {
            $this->view->assign('hasAccessToContent', $hasAccessToContent);
            // Retrieve the page view layout
            $page = new PageObject();
            $pageData = $page->populate($pageID, $this->_defaultEditLanguage);
            $this->view->assign("pageTitle", $pageData["PI_PageTitle"]);
            if (isset($pageData["P_DuplicateId"])){
                $this->view->assign("relatedPage", $pageData["P_DuplicateId"]);
            }
            if (isset($pageData["P_FromSite"])){
                $this->view->assign("fromSite", $pageData["P_FromSite"]);
            }
            $page_info = Cible_FunctionsPages::getPageViewDetails($pageID);

            $template_file = 'manage/' . $page_info['V_Path'];
            $_zone_count = $page_info['V_ZoneCount'];

            // make a request to get all the blocks to be displayed
            $oBlock = new BlocksObject();
            $qry = $oBlock->setOrderBy('B_Position ASC')
                ->setQuery($oBlock->getAll($this->_defaultEditLanguage, false))
                ->setWhereClause(array($oBlock->getForeignKey() => $pageID))
                ->join('Modules', 'Modules.M_ID = Blocks.B_ModuleID');

            //Send the results to the view
            $rows = $page->setForeignKey($oBlock->getForeignKey())
                ->setQuery($qry)
                ->completeQuery($this->_defaultEditLanguage, true);

            $_blocks = array();

            foreach($rows as $row)
            {
                // create the placeholder object if not already defined
                if( !isset( $_blocks[$row['B_ZoneID']] ) )
                   $_blocks[$row['B_ZoneID']] = array();

                $_blocks[$row['B_ZoneID']][] = $row;
            }

            $this->view->assign('template_file', $template_file);
            $this->view->assign('zone_count', $_zone_count);
            $this->view->assign('blocks', $_blocks);

            // Load the modules in the view
            $Modules = new Modules();

            $select = $Modules->select()->setIntegrityCheck(false)
                ->from('Modules')
                ->where('M_HasFrontend = ?', 1)
                ->where('M_SiteType like ?', '%' . $this->_siteType . '%');

            $modules = $Modules->fetchAll($select);
            $this->view->assign('modules', $modules->toArray());
        }
        else{
            $this->view->assign('template_file', "");
            $this->view->assign('error_message_permission', $this->view->getCibleText('error_message_permission'));
        }

    }

    function pageStructureAction(){

    }

    function deleteheaderAction(){
        $this->disableView();
        $params = $this->_request->getParams();
        if (isset($params['image'])){
            $files = glob("../".$this->view->currentSite."/data/images/page/header/*" . $params['image']);
            foreach($files as $key => $value){
                unlink($value);
        }
        }
        $this->_redirect("/page/manage/header");
    }

    function cropimageAction(){
        $image = "";
        $params = $this->_request->getParams();
        $this->_rootImgPath .= "header/";
        $this->_imagesFolder .= "header/";
        if (!$this->_request->isPost()){
            $image = $params['image'];
            $this->_returnAfterCrop = "/page/manage/header";
            $this->_cancelPage = $this->view->baseUrl() . "/page/manage/header";
        }else{
            parent::cropimageAction();
            $formData = $this->_request->getPost();
            $data[$this->_imageSrc] = basename($formData['ImageSrc']);
            $this->_setImage($this->_imageSrc, $data, 'tmp/..');
            $this->_redirect($formData['returnPage']);
        }
        parent::cropimageAction();
    }

    function cropimagebackgroundAction(){
        $image = "";
        $params = $this->_request->getParams();
        if (!$this->_request->isPost()){
            $image = $params['image'];
            $imageS = $this->_imagesFolder . "background/" . $image;
            $imageSource = $this->_rootImgPath . "background/" . $image;
            $config = Zend_Registry::get('config')->toArray();
            $headerWidth = $config['image']['background']['size']['width'];
            $headerHeight = $config['image']['background']['size']['height'];

            $this->_headerWidth = $headerWidth;
            $this->_headerHeight = $headerHeight;
            $this->_imageSource = $imageSource;
            $this->_returnAfterCrop = "/page/manage/background";
            $this->_cancelPage = $this->view->baseUrl() . "/page/manage/background";
        }
        parent::cropimageAction();
    }

    function deletebackgroundAction(){
        $this->disableView();
        $params = $this->_request->getParams();
        if (isset($params['image'])){
            unlink("../".$this->view->currentSite."/data/images/page/background/" . $params['image']);
        }
        $this->_redirect("/page/manage/background");
    }

    function addheaderAction(){
        $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null, 'original');
        $imageSrc = $imageSource['imageSrc'];
        $isNewImage = $imageSource['isNewImage'];
        $this->view->showCrop = isset($this->_config->page->active->crop) ?
                (bool)$this->_config->page->active->crop : false;

        $form = new FormImages(array(
                'baseDir' => $this->view->baseUrl(),
                'cancelUrl' => Zend_Registry::get("www_root") . "extranet/page/manage/header",
                'imageSrc' => $imageSrc,
                'isNewImage' => $isNewImage,
                'pathTmp' => "/header/tmp/",
                'showCrop' => $this->view->showCrop
             ));
        $this->view->form = $form;
        if ($this->_request->isPost())
        {
            $this->_imagesFolder .= 'header/';
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)){
                $this->_setImage($this->_imageSrc, $formData, 'tmp/..');
                $this->_redirect("/page/manage/header");
            }
            else{
                $form->populate($formData);
            }
        }
    }

    function addbackgroundAction(){
        $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null);
        $imageSrc = $imageSource['imageSrc'];
        $isNewImage = $imageSource['isNewImage'];
        $form = new FormImages(array(
                'baseDir' => $this->view->baseUrl(),
                'cancelUrl' => Zend_Registry::get("www_root") . "extranet/page/manage/background",
                'imageSrc' => $imageSrc,
                'isNewImage' => $isNewImage,
                'pathTmp' => "/background"
             ));
        $this->view->form = $form;

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData))
            {
                $this->_redirect("/page/manage/background");
            }
            else{
                $form->populate($formData);
            }
        }
    }

    function headerAction(){
        $this->view->showCrop = isset($this->_config->page->active->crop) ?
            (bool)$this->_config->page->active->crop : false;
        $this->view->cropPrefix = $this->_config->page->source->crop->maxWidth
            . 'x' . $this->_config->page->source->crop->maxHeight . '_';
        $this->view->thumbW = $this->_config->page->image->thumb->maxWidth;
        $this->view->thumbH = $this->_config->page->image->thumb->maxHeight;
        $this->view->rootImgPath = rtrim(Zend_Registry::get('fullDocumentRoot'),'/');
        $this->view->assign('imageFiles',$this->_findImagesFiles());
        $this->view->assign('imageFolder', $this->_rootImgPath);
    }

    function backgroundAction(){
        $this->view->assign('imageFiles',$this->_findBackImagesFiles());
        $this->view->assign('imageFolder',$this->_rootImgPath);
    }

    public function ajaxAction()
    {
        $data = parent::ajaxAction();
        $action = $this->_getParam('actionAjax');

        switch ($action)
        {
            case 'pageSiteSrc':
                $form = new FormPage(array(
                    'siteType'  => $this->_siteType,
                    'imageHeaderArray'  => array(),
                    'imageBackHeaderArray' => array()
                ));
                echo $form->getElement('pagePicker')->render();
                break;

            default:
                break;
        }
    }
    function structureAction(){
        $this->view->headScript()->appendFile($this->view->locateFile('moxman.loader.min.js', 'tinymce/plugins/moxiemanager/js'));
        $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/cible.form.element.pagepicker.js");
        $params = $this->_request->getParams();
        if (isset($params['site']))
            $siteType = $params['site'];
        $this->view->assign('menu', $this->buildMenu($this->_siteType));
        $this->view->assign('params', $params);
        $config = Zend_Registry::get('config')->toArray();
        $this->view->assign('hasBackgroundImages', $config['image']['background']['show']);
        $this->view->assign('hasHeaderImages',$config['image']['entete']['show']);
    }

    function pageBreadcrumbAction(){
        $pageID = $this->_getParam( 'ID' );

        $this->disableView();

        if( empty($pageID) )
            return;

        echo Cible_FunctionsPages::buildBreadcrumb($pageID, $this->_defaultEditLanguage);
    }

    function moduleNameAction(){
        $module = $this->_getParam( 'name' );

        $this->disableView();

        if( empty($module) )
            return;

        echo Cible_FunctionsModules::getLocalizedModuleTitle($module);
    }

    public function menuAction(){
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/cible.form.element.pagepicker.js");

            $allMenuTitles = MenuObject::getAllMenuTitles($this->_siteType);
            $menus = array();

            foreach( $allMenuTitles as $menu){
                $_menu = new MenuObject();
                $_menu->initMenu($menu['Title']);
                array_push($menus, array(
                    'ID' => $menu['ID'],
                    'Title' => $menu['Title'],
                    'Type' => $menu['Type'],
                    'BgColor' => $menu['BgColor'],
                    'Menu' => $_menu->populate(0, true)
                ));
            }

            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/manage.categories.js");
            $this->view->assign( 'menus', $menus );
        }
    }

    function addMenuAction(){

        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $_parentId = (int)$this->_request->getParam('parentID');
        $_menuId   = (int)$this->_request->getParam('menuID');
        $_position = (int)$this->_request->getParam('position');

        $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($formData['menuImage'] <> "")
                $imageSrc = $this->_rootMenuImgPath . "tmp/mcith/mcith_" . $formData['menuImage'];
            else
                $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
        }
        else
            $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";

        // create a new formpage object
        $form = new FormMenu(array(
            'baseDir'   => $this->view->baseUrl(),
            'pageID'	=> 0,
            'addAction' => true,
            'imageSrc' => $imageSrc,
            'menuId' => '',
            'langId' => $this->_defaultEditLanguage,
            'siteType' => $this->_siteType,
            'isNewImage' => true,
        ));

        $this->view->assign('form', $form);

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            $isPlaceholder = 0;

            if( $formData['menuItemType'] == 'page' ){

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: block');

            } else if('external') {

                $form->disableElementValidation('pagePicker');
                $form->disableElementValidation('ControllerName');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: block');

            } else {

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->disableElementValidation('pagePicker');
                $form->disableElementValidation('ControllerName');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');

                $isPlaceholder = 1;

            }

            if ($form->isValid($formData))
            {
                $formData = $form->getValues();
                // saving the entry
                $menuItem = new MenuObject();
                $menuItem->initMenu($_menuId);
                $menuId   = $menuItem->addItem($_parentId, array(
                    'languageID' => $this->_config->defaultEditLanguage,
                    'Title' => $formData['MenuTitle'],
                    'Link' => $formData['MenuLink'],
                    'PageID' => $formData['pagePicker'],
                    'Position' => $_position,
                    'Style' => $formData['MenuTitleStyle'],
                    'menuItemSecured' => $formData['menuItemSecured'],
                    'loadImage' => $formData['loadImage'],
                    'menuImage' => $formData['menuImage'],
                    'menuImgAndTitle' => $formData['menuImgAndTitle'],
                    'MID_Show' => $formData['MID_Show'],
                    'MID_Show_Sitemap' => $formData['MID_Show_Sitemap'],
                    'MID_SiteType' => $formData['MID_SiteType'],
                    'Placeholder' => $isPlaceholder
                ));

                /* IMAGES */
                if (!is_dir($this->_imgMenuFolder . $menuId))
                {
                    mkdir($this->_imgMenuFolder . $menuId) or die("Could not make directory");
                    mkdir($this->_imgMenuFolder . $menuId . "/tmp") or die("Could not make directory");
                }
                if ($form->getValue('menuImage') <> '')
                {
                    $config = Zend_Registry::get('config')->toArray();
                    $srcOriginal = $this->_imgMenuFolder . "tmp/" . $form->getValue('menuImage');
                    $originalMaxHeight = $config[$this->_folderMenu]['image']['original']['maxHeight'];
                    $originalMaxWidth = $config[$this->_folderMenu]['image']['original']['maxWidth'];
                    $originalName = str_replace($form->getValue('menuImage'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));


                    $srcMedium = $this->_imgMenuFolder . "tmp/medium_{$form->getValue('menuImage')}";
                    $mediumMaxHeight = $config[$this->_folderMenu]['image']['medium']['maxHeight'];
                    $mediumMaxWidth = $config[$this->_folderMenu]['image']['medium']['maxWidth'];
                    $mediumName = str_replace($form->getValue('menuImage'), $mediumMaxWidth . 'x' . $mediumMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));

                    $srcThumb = $this->_imgMenuFolder . "tmp/thumb_{$form->getValue('menuImage')}";
                    $thumbMaxHeight = $config[$this->_folderMenu]['image']['thumb']['maxHeight'];
                    $thumbMaxWidth = $config[$this->_folderMenu]['image']['thumb']['maxWidth'];
                    $thumbName = str_replace($form->getValue('menuImage'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));

                    copy($srcOriginal, $srcMedium);
                    copy($srcOriginal, $srcThumb);

                    Cible_FunctionsImageResampler::resampled(array('src' => $srcOriginal, 'maxWidth' => $originalMaxWidth, 'maxHeight' => $originalMaxHeight));
                    Cible_FunctionsImageResampler::resampled(array('src' => $srcMedium, 'maxWidth' => $mediumMaxWidth, 'maxHeight' => $mediumMaxHeight));
                    Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                    rename($srcOriginal, $this->_imgMenuFolder . "$menuId/$originalName");
                    rename($srcMedium, $this->_imgMenuFolder . "$menuId/$mediumName");
                    rename($srcThumb, $this->_imgMenuFolder . "$menuId/$thumbName");
                }

                if( !$this->_isXmlHttpRequest )
                    $this->_redirect('/');
                else {
                    $this->view->assign('success', true);
                    $buttonAction = $formData['buttonAction'];
                    $this->view->assign('buttonAction', $buttonAction);
                    $this->view->assign('menuID', $menuId);
                }
            } else {
                $form->populate($formData);
            }
        }
        else
        {
            $form->getElement('menuImage_preview')->setAttrib('src', $imageSrc);
        }
    }

    function editMenuAction(){
        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $_parentId = (int)$this->_request->getParam('parentID');
        $_menuId = (int)$this->_request->getParam('menuID');
        // image src.
        $config = Zend_Registry::get('config')->toArray();
        $thumbMaxHeight = $config[$this->_folderMenu]['image']['thumb']['maxHeight'];
        $thumbMaxWidth = $config[$this->_folderMenu]['image']['thumb']['maxWidth'];

        $menuItem = new MenuObject();
        $menuItem->initMenu($_menuId);
        $menuData = $menuItem->loadItem($_parentId);
        if(isset($menuData['menuImage'])){
            $this->view->assign('imageUrl', $this->_rootMenuImgPath . "$_parentId/" . str_replace($menuData['menuImage'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $menuData['menuImage'], $menuData['menuImage']));
        }

        if (!is_dir($this->_imgMenuFolder . "$_parentId/"))
        {
            mkdir($this->_imgMenuFolder . $_parentId) or die("Could not make directory");
            mkdir($this->_imgMenuFolder . $_parentId . "/tmp") or die("Could not make directory");
        }

        $isNewImage = 'false';
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($formData['menuImage'] <> $menuData['menuImage'])
            {
                if ($formData['menuImage'] == "")
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                else
                    $imageSrc = $this->_rootMenuImgPath . "$_parentId/tmp/mcith/mcith_" . $formData['menuImage'];

                $isNewImage = 'true';
            }
            else
            {
                if ($menuData['menuImage'] == "")
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                else
                    $imageSrc = $this->_rootMenuImgPath . "$_parentId/" . str_replace($menuData['menuImage'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $menuData['menuImage'], $menuData['menuImage']);
            }
        }
        else
        {
            if (empty($menuData['menuImage']))
                $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
            else
                $imageSrc = $this->_rootMenuImgPath . "$_parentId/" . str_replace($menuData['menuImage'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $menuData['menuImage'], $menuData['menuImage']);
        }

        // create a new formpage object
        $form = new FormMenu(array(
            'baseDir' => $this->view->baseUrl(),
            'pageID'  => 0,
            'menuId'  => $_parentId,
            'langId'  => $this->_currentEditLanguage,
            'imageSrc' => $imageSrc,
            'siteType' => $this->_siteType,
            'isNewImage' => $isNewImage
        ));

        $this->view->assign('form', $form);

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            $isPlaceholder = 0;
            if( $formData['menuItemType'] == 'page' ){

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: block');
                $formData['MenuLink'] = '';
            } else if( $formData['menuItemType'] == 'external' ) {

                $form->disableElementValidation('pagePicker');
                $form->disableElementValidation('ControllerName');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: block');
                $formData['pagePicker'] = 0;

            } else {

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');

                $form->disableElementValidation('pagePicker');
                $form->disableElementValidation('ControllerName');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');

                $formData['MenuLink'] = '';
                $isPlaceholder = 1;

            }

            if ($form->isValid($formData))
            {
                $formData = $form->getValues();
                // saving the entry
                $menuItem = new MenuObject();
                $menuItem->initMenu($_menuId);
                $menuItem->updateItem($_parentId, array(
                    'menuItemSecured' => $formData['menuItemSecured'],
                    'loadImage' => $formData['loadImage'],
                    'menuImage' => $formData['menuImage'],
                    'menuImgAndTitle' => $formData['menuImgAndTitle'],
                    'languageID' => $this->_currentEditLanguage,
                    'Title' => $formData['MenuTitle'],
                    'MID_Show' => $formData['MID_Show'],
                    'MID_Show' => $formData['MID_Show'],
                    'MID_FontIcon' => isset($formData['MID_FontIcon']) ? $formData['MID_FontIcon'] : '',
                    'MID_Show_Sitemap' => $formData['MID_Show_Sitemap'],
                    'Link' => $formData['MenuLink'],
                    'PageID' => $formData['pagePicker'],
                    'Style'  => $formData['MenuTitleStyle'],
                    'Placeholder' => $isPlaceholder
                ));
                /* IMAGES */
                if ($form->getValue('menuImage') <> '' && $isNewImage == 'true')
                {
                    $config = Zend_Registry::get('config')->toArray();
                    $srcOriginal = $this->_imgMenuFolder . $_parentId . "/tmp/" . $form->getValue('menuImage');
                    $originalMaxHeight = $config[$this->_folderMenu]['image']['original']['maxHeight'];
                    $originalMaxWidth = $config[$this->_folderMenu]['image']['original']['maxWidth'];
                    $originalName = str_replace($form->getValue('menuImage'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));


                    $srcMedium = $this->_imgMenuFolder . $_parentId . "/tmp/medium_{$form->getValue('menuImage')}";
                    $mediumMaxHeight = $config[$this->_folderMenu]['image']['medium']['maxHeight'];
                    $mediumMaxWidth = $config[$this->_folderMenu]['image']['medium']['maxWidth'];
                    $mediumName = str_replace($form->getValue('menuImage'), $mediumMaxWidth . 'x' . $mediumMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));

                    $srcThumb = $this->_imgMenuFolder . $_parentId . "/tmp/thumb_{$form->getValue('menuImage')}";
                    $thumbMaxHeight = $config[$this->_folderMenu]['image']['thumb']['maxHeight'];
                    $thumbMaxWidth = $config[$this->_folderMenu]['image']['thumb']['maxWidth'];
                    $thumbName = str_replace($form->getValue('menuImage'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $form->getValue('menuImage'), $form->getValue('menuImage'));

                    copy($srcOriginal, $srcMedium);
                    copy($srcOriginal, $srcThumb);

                    Cible_FunctionsImageResampler::resampled(array('src' => $srcOriginal, 'maxWidth' => $originalMaxWidth, 'maxHeight' => $originalMaxHeight));
                    Cible_FunctionsImageResampler::resampled(array('src' => $srcMedium, 'maxWidth' => $mediumMaxWidth, 'maxHeight' => $mediumMaxHeight));
                    Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                    rename($srcOriginal, $this->_imgMenuFolder . "$_parentId/$originalName");
                    rename($srcMedium, $this->_imgMenuFolder . "$_parentId/$mediumName");
                    rename($srcThumb, $this->_imgMenuFolder . "$_parentId/$thumbName");
                }

                if( !$this->_isXmlHttpRequest )
                    $this->_redirect('/');
                else
                {
                    $buttonAction = $formData['buttonAction'];
                    $this->view->assign('buttonAction', $buttonAction);
                    $this->view->assign('success', true);
                    $this->view->assign('menuID', $_parentId);
                }
            } else {
                $form->populate($formData);
            }
        } else {

            $form->populate($menuData);

            $menuData['menuItemType'] = isset( $menuData['menuItemType'] ) ? $menuData['menuItemType'] : 'page';

            if( $menuData['menuItemType'] == 'page' ){

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: block');

            } else if( $menuData['menuItemType'] == 'external' ) {

                $form->disableElementValidation('pagePicker');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: block');

            } else {

                $form->disableElementValidation('MenuLink');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');

                $form->disableElementValidation('pagePicker');
                $form->getDisplayGroup('pageSelectionGroup')->setAttrib('style', 'display: none');
                $form->getDisplayGroup('externalLinkSelectionGroup')->setAttrib('style', 'display: none');
            }
        }
    }

    function deleteMenuAction(){
        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $_itemId = (int)$this->_request->getParam('itemID');
        $_menuId = (int)$this->_request->getParam('menuID');

        $this->disableView();

        if ($this->_request->isPost()) {
            $delete = isset($_POST['delete']);

            if( $delete ){
                $menuObject = new MenuObject($_menuId);
                $menuObject->initMenu($_menuId);
                $menuObject->deleteItem($_itemId);
                $dir = $this->_imgMenuFolder . $_itemId;
                Cible_FunctionsGeneral::delFolder($dir);
            }
        }
    }

    function updateMenuPositionAction(){
         $this->disableView();

        if ($this->_request->isPost()) {

            $order = isset($_POST['order']);

            if( $order ){

                $json_object = Zend_Json_Decoder::decode( $_POST['order'] );

                $menuObject = new MenuObject();
                foreach($json_object as $key => $object){

                    $menuName = str_replace('ul_','', $key);
                    $menuObject->initMenu($menuName);
                    $menuObject->updatePositions( $object );
                }
            }
        }
    }

    function autogenerateFromIdAction(){
        $this->disableView();

        $_itemId = $this->_getParam('itemID');
        $_menuId = $this->_getParam('menuID');

        if ($this->_request->isPost()) {


            if( $_itemId != null && $_itemId >= 0 ) {

                $menuObject = new MenuObject();
                $menuObject->initMenu($_menuId);
                $recursive = false;

                if( isset( $_POST['autogenerate_recursive'] ) && $_POST['autogenerate_recursive'] == 'true' )
                    $recursive = true;

                $result = $menuObject->autogenerateFromId( $_itemId, $recursive );
            }
        }
    }

    function addAction(){

        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);


        $this->view->title = "Ajouter une page";
        // retrieve the ID of the parent page
        $PageID = $this->_getParam( 'ID' );

        if(Cible_ACL::hasAccess($PageID)){
            // image src.
            if ($this->_request->isPost()){
                $formData = $this->_request->getPost();
            }

            $imageHeaderArray = $this->_findImagesFiles();
            $imageBackHeaderArray = $this->_findBackImagesFiles();

            // create a new formpage object
            $form = new FormPage(array(
                    'baseDir'   => $this->view->baseUrl(),
                    //'imageSrc'  => $imageSrc,
                    'pageID'    => 0,
                    'addAction' => true,
                    'siteType' => $this->_siteType,
                    'imageHeaderArray'  => $imageHeaderArray,
                    'imageBackHeaderArray' => $imageBackHeaderArray
                ));

            // get page informations
            $Page = $this->view->Page = Cible_FunctionsPages::getPageDetails($PageID);

            //get position of all pages on the same level of the current page
            //$Position = Cible_FunctionsPages::getAllPositions($PageID);

            $layouts = Cible_FunctionsPages::getAvailableLayouts($this->_siteType);
            $templates = Cible_FunctionsPages::getAvailableTemplates($this->_siteType);
            $themes = Cible_FunctionsPages::getAvailableThemes($this->_siteType);
            // fill select position
            //$form = Cible_FunctionsPages::fillSelectPosition($form, $Position,"add");

            $form = Cible_FunctionsPages::fillSelectThemes($form, $themes);
            $form = Cible_FunctionsPages::fillSelectLayouts($form, $layouts);
            $form = Cible_FunctionsPages::fillSelectTemplates($form, $templates);

            //$form->submit->setLabel('Ajouter');
            $this->view->form = $form;

            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData)) {
                    Zend_Registry::set('currentEditLanguage', $this->_config->defaultEditLanguage);
                    $this->_currentEditLanguage = $this->_config->defaultEditLanguage;

                    $_db = $this->_db;
                    $_lastPosition = $_db->fetchOne( $_db->quoteInto('SELECT P_Position FROM Pages WHERE P_ParentID = ? ORDER BY P_Position DESC', $PageID ) );

                    $page = new Pages();
                    $row = $page->createRow();
                    $row->P_Position = $_lastPosition + 1;
                    $row->P_ParentID = $PageID;
                    $row->P_ViewID = $formData['P_ViewID'];
                    $row->P_LayoutID = $formData['P_LayoutID'];
                    $row->P_ThemeID = $formData['P_ThemeID'];
                    $row->P_ShowTitle = $formData['P_ShowTitle'];
                    $row->P_SiteType = $formData['P_SiteType'];
                    $row->P_FromSite = isset($formData['P_FromSite']) ? $formData['P_FromSite'] : '';
                    $row->P_DuplicateId = isset($formData['pagePicker'])?$formData['pagePicker']:0;

                    $row->save();

                    // get the new page ID
                    $NewPageID = $row->P_ID;

                    $pageIndex = new PagesIndex();
                    $rowPageIndex = $pageIndex->createRow();

                    $rowPageIndex->PI_PageID = $NewPageID;
                    $rowPageIndex->PI_LanguageID = $this->_config->defaultEditLanguage;

                    //Insérer seulement dans la version de la langue en cours, les données saisies par l'usager

                    $rowPageIndex->PI_PageIndex         = strtolower($form->getValue('PI_PageIndex'));
                    $rowPageIndex->PI_PageTitle         = $form->getValue('PI_PageTitle');
                    $rowPageIndex->PI_MetaTitle         = $form->getValue('PI_MetaTitle');
                    $rowPageIndex->PI_MetaDescription   = $form->getValue('PI_MetaDescription');
                    $rowPageIndex->PI_MetaKeywords      = $form->getValue('PI_MetaKeywords');
                    $rowPageIndex->PI_MetaOther         = $form->getValue('PI_MetaOther');
                    $rowPageIndex->PI_Status            = $form->getValue('PI_Status');
                    $rowPageIndex->PI_TitleImageSrc     = $form->getValue('PI_TitleImageSrc');
                    $rowPageIndex->PI_ImageBackground   = $form->getValue('PI_ImageBackground');
                    $rowPageIndex->PI_AltPremiereImage  = $form->getValue('PI_AltPremiereImage');
//                    $rowPageIndex->PI_TitleImage2  = $form->getValue('PI_TitleImage2');
                    $rowPageIndex->PI_CanonicalLink     = $form->getValue('PI_CanonicalLink');

                    $rowPageIndex->save();


                    if($formData['PI_Status'] == 1){
                        $indexData['pageID']    = $NewPageID;
                        $indexData['moduleID']  = 0;
                        $indexData['contentID'] = $NewPageID;
                        $indexData['languageID'] = $this->_config->defaultEditLanguage;
                        $indexData['title']     = $formData['PI_PageTitle'];
                        $indexData['text']      = '';
                        $indexData['link']      = '';
                        $indexData['siteType']  = $this->_siteType;
                        $indexData['contents']  = $formData['PI_PageTitle'];
                        $indexData['object']  = 'PagesObject';
                        $indexData['action']    = 'add';

                        Cible_FunctionsIndexation::indexation($indexData);
                    }

                    // if not and ajax request, redirect else simply return as json success code and page details
                    if( !$this->_isXmlHttpRequest )
                        $this->_redirect('/');
                    else {
                        $buttonAction = $formData['buttonAction'];
                        $this->view->assign('buttonAction', $buttonAction);
                        $this->view->assign('success', true);
                        $this->view->assign('pageOnline', $formData['PI_Status']);
                        $this->view->assign('parentID', $PageID);
                        $this->view->assign('pageID', $NewPageID);
                        $this->view->assign('pageTitle', $formData['PI_PageTitle']);
                        $this->view->assign('currentEditLanguage', $this->_config->defaultEditLanguage);
                    }

                } else {
                    $form->getElement('PI_MetaOther')->clearFilters();
                    $form->populate($formData);
                }
            }
        }
    }

    function editAction(){

        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $this->view->title = "Modification de la page";
        // retrieve the ID of the requested page
        $PageID = (int)$this->_getParam( 'ID' );
        if(Cible_ACL::hasAccess($PageID)){

            // get page informations
            $Page = Cible_FunctionsPages::getPageDetails($PageID, $this->_currentEditLanguage);

            $page_info = $Page->toArray();

            $this->view->home = $page_info['P_Home'];
            $this->view->pageTitle = isset($page_info['PI_PageTitle']) ? $page_info['PI_PageTitle'] : '';

            $imageHeaderArray = $this->_findImagesFiles();
            $imageBackHeaderArray = $this->_findBackImagesFiles();

            $form = new FormPage(array(
                'baseDir'   => $this->view->baseUrl(),
                'pageID'    => $PageID,
                'siteType' => $this->_siteType,
                'imageHeaderArray'  => $imageHeaderArray,
                'imageBackHeaderArray' => $imageBackHeaderArray
            ));

            $layouts = Cible_FunctionsPages::getAvailableLayouts($this->_siteType);
            $templates = Cible_FunctionsPages::getAvailableTemplates($this->_siteType);
            $themes = Cible_FunctionsPages::getAvailableThemes($this->_siteType);

            $form = Cible_FunctionsPages::fillSelectLayouts($form, $layouts);
            $form = Cible_FunctionsPages::fillSelectTemplates($form, $templates);
            $form = Cible_FunctionsPages::fillSelectThemes($form, $themes);

            $this->view->form = $form;

            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();

                $PageIndex = strtolower($formData['PI_PageIndex']);
                // replace all double underscore by simple underscore
                while(substr_count($PageIndex,"__")>0){
                    $PageIndex = str_replace("__","_",$PageIndex);
                }

                if( isset( $page_info['PI_PageIndex'] ) && (strtolower($page_info['PI_PageIndex']) == $PageIndex ) )
                    $form->getElement('PI_PageIndex')->clearValidators();

                if (empty($formData['P_DuplicateId_lbl']))
                {
                    $formData['pagePicker'] = 0;
                    $formData['P_FromSite'] = '';
                }
                if ($form->isValid($formData)) {
                    $formData = $form->getValues();
                    $db = $this->_db;

                    // update the P_LayoutID for the current Page
                    $db->update('Pages', array(
                        'P_LayoutID' => $formData['P_LayoutID'],
                        'P_ThemeID' => $formData['P_ThemeID'],
                        'P_ShowTitle' => $formData['P_ShowTitle'],
                        'P_DuplicateId' => $formData['pagePicker'],
                        'P_FromSite' => $formData['P_FromSite']),
                        'P_ID = '. $PageID
                        );

                    // check to update P_ViewID
                    if( $formData['P_ViewID'] != $page_info['P_ViewID'] ){

                        //get current number of zones
                        $previous_zone_count = $page_info['V_ZoneCount'];

                        // update the P_ViewID for the current Page
                        $db->update('Pages', array('P_ViewID'=> $formData['P_ViewID']), 'P_ID = '. $PageID);

                        // Fetch the new selected view details
                        $new_view_details = Cible_FunctionsPages::getPageViewDetails($PageID);
                        $new_zone_count =  $new_view_details['V_ZoneCount'];

                        // if our new zone count is smaller then our previous zone count, we need to move our deleted zone blocks to zone 1
                        if($new_zone_count < $previous_zone_count){

                            //reset where just in case
                            $where = array();
                            $where[] = $db->quoteInto('B_PageID = ?', $PageID);
                            $where[] = $db->quoteInto('B_ZoneID > ?', $new_zone_count);
                            $db->update('Blocks', array('B_ZoneID'=> '-1'), $where);
                        }
                   }

                    //$Page['PI_LanguageID'] = $this->_currentEditLanguage;
                    if( isset( $Page['PI_PageTitle'] ) ){

                        $Page['PI_PageTitle'] = $formData['PI_PageTitle'];
                        $Page['PI_PageIndex'] = $PageIndex;
                        $Page['PI_Status']    = $formData['PI_Status'];

                        $Page['PI_MetaTitle'] = $formData['PI_MetaTitle'];
                        $Page['PI_MetaDescription'] = $formData['PI_MetaDescription'];
                        $Page['PI_MetaKeywords'] = $formData['PI_MetaKeywords'];
                        $Page['PI_MetaOther'] = $formData['PI_MetaOther'];

                        $Page['PI_TitleImageSrc'] = $formData['PI_TitleImageSrc'];
                        $Page['PI_AltPremiereImage'] = $formData['PI_AltPremiereImage'];
//                        $Page['PI_TitleImage2'] = $formData['PI_TitleImage2'];


                        $Page['PI_ImageBackground'] = $formData['PI_ImageBackground'];
                        $Page['PI_CanonicalLink'] = $formData['PI_CanonicalLink'];
                        $Page->save();

                    } else {
                        $data = array(
                            'PI_PageID' => $PageID,
                            'PI_LanguageID' => $this->_currentEditLanguage,
                            'PI_PageTitle' => $formData['PI_PageTitle'],
                            'PI_PageIndex' => $PageIndex,
                            'PI_Status' => $formData['PI_Status'],
                            'PI_MetaTitle' => $formData['PI_MetaTitle'],
                            'PI_MetaDescription' => $formData['PI_MetaDescription'],
                            'PI_MetaKeywords' => $formData['PI_MetaKeywords'],
                            'PI_MetaOther' => $formData['PI_MetaOther'],
                            'PI_TitleImageSrc' => $formData['PI_TitleImageSrc'],
                            'PI_ImageBackground' => $formData['PI_ImageBackground'],
                            'PI_AltPremiereImage' => $formData['PI_AltPremiereImage'],
//                            'PI_TitleImage2' => $formData['PI_TitleImage2'],


                            'PI_CanonicalLink' => $formData['PI_CanonicalLink']
                        );
                        $this->_db->insert('PagesIndex', $data);
                    }

                    $indexData['pageID']    = $PageID;
                    $indexData['moduleID']  = 0;
                    $indexData['contentID'] = $PageID;
                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                    $indexData['title']     = $formData['PI_PageTitle'];
                    $indexData['text']      = '';
                    $indexData['link']      = '';
                    $indexData['object']  = 'PagesObject';
                    $indexData['contents']  = $formData['PI_PageTitle'];

                    if($formData['PI_Status'] == 1)
                        $indexData['action']    = 'update';
                    else
                        $indexData['action']    = 'delete';

                        Cible_FunctionsIndexation::indexation($indexData);

                   // if not and ajax request, redirect else simply return as json success code and page details
                    if( !$this->_isXmlHttpRequest )
                        $this->_redirect('/');
                    else {
                        $buttonAction = $formData['buttonAction'];
                        $this->view->assign('buttonAction', $buttonAction);
                        $this->view->assign('success', true);
                        $this->view->assign('pageID', $PageID);
                        $this->view->assign('pageTitle', $formData['PI_PageTitle']);
                        $this->view->assign('pageOnline', $formData['PI_Status']);
                        $this->view->assign('currentEditLanguage', $this->_currentEditLanguage);
                    }
                }
                else {
                    $form->getElement('PI_MetaOther')->clearFilters();
                    $form->populate($formData);
                }
            }
            else {
                if ($PageID > 0) {
                    $form->getElement('PI_MetaOther')->clearFilters();
                    $form->populate($Page->toArray());
                }
            }
        }
    }

    function deleteAction(){
        $this->view->title = "Supprimer une page";
        // retrieve the ID of the parent page
        $PageID = $this->_getParam( 'ID' );
        if(Cible_ACL::hasAccess($PageID)){
            // generates tree by Language and send the result to the view
            Zend_Registry::set('baseUrl',$this->view->baseUrl());
            $this->view->TreeView = $this->view->getTreeView();

            if ($this->_request->isPost()) {
                //$PageID = (int)$this->_request->getPost('PageID');

                // if is set delete, then delete
                $delete = isset($_POST['delete']);

                if ($delete && $PageID > 0) {
                    Cible_FunctionsPages::deleteAllChildPage($PageID);
                    Cible_FunctionsPages::deleteAllBlock($PageID);

                    $pageSelect = new PagesIndex();
                    $select = $pageSelect->select()
                    ->where('PI_PageID = ?',$PageID);
                    $pageData = $pageSelect->fetchAll($select)->toArray();

                    foreach($pageData as $page){
                        $indexData['moduleID']  = 0;
                        $indexData['contentID'] = $PageID;
                        $indexData['languageID'] = $page['PI_LanguageID'];
                        $indexData['action'] = 'delete';
                        Cible_FunctionsIndexation::indexation($indexData);
                    }

                    $Page = new Pages();
                    $Where = 'P_ID = ' . $PageID;
                    $Page->delete($Where);

                    $PageIndex = new PagesIndex();
                    $Where = 'PI_PageID = ' . $PageID;
                    $PageIndex->delete($Where);

                    if( !$this->_request->isXmlHttpRequest() )
                        $this->_redirect('/');
                }
                else{
                    if( !$this->_isXmlHttpRequest )
                        $this->_redirect('/page/edit/ID/'.$PageID);
                }
            }
            else {
                if ($PageID > 0) {
                    $Page = new Pages();
                    $this->view->page = $Page->fetchRow('P_ID='.$PageID);

                    $PageIndex = new PagesIndex();
                    $Select = $PageIndex->select()
                                        ->where("PI_PageID = ?", $PageID)
                                        ->where("PI_LanguageID = ?", $this->_defaultEditLanguage);
                    $this->view->pageindex = $PageIndex->fetchRow($Select);
                }
            }
        }
    }

    function updatePositionAction(){
        $this->disableView();
        $_updateID = $this->_getParam('updateID');
        $_from_parentID = $this->_getParam('from_parentID');
        $_to_parentID = $this->_getParam('to_parentID');

        $_from_childs = Zend_Json::decode( $this->_getParam('from_childs') );
        $_to_childs = Zend_Json::decode( $this->_getParam('to_childs') );

        $_db = $this->_db;

        //We are simply updating the positions for parentID _to_parentID
        if( $_from_parentID == $_to_parentID ){
             foreach( $_to_childs as $key => $val ){
                $_db->update( 'Pages', array('P_Position' => $key + 1), 'P_ID = ' . $val );
             }
        }
        //We are moving _updateID to _to_parentID, then update order for all _from_childs and _to_childs
        else{

            $_db->update( 'Pages', array('P_ParentID' => $_to_parentID), 'P_ID = ' . $_updateID );

            foreach( $_from_childs as $key => $val ){
                $_db->update( 'Pages', array('P_Position' => $key + 1), 'P_ID = ' . $val );
            }

            foreach( $_to_childs as $key => $val ){
                $_db->update( 'Pages', array('P_Position' => $key + 1), 'P_ID = ' . $val );
            }
        }

    }

    function setAsHomePageAction(){
        $this->disableView();
        $_pageID = $this->_getParam('ID');

        $_db = $this->_db;
        switch ($this->_siteType)
        {
            case 'm':
                $_db->update('Pages', array('P_HomeMobile' => 0),'P_HomeMobile = 1');
                $_db->update('Pages', array('P_HomeMobile' => 1),'P_ID = '.$_pageID);
                break;

            default:
                $_db->update('Pages', array('P_Home' => 0),'P_Home = 1');
                $_db->update('Pages', array('P_Home' => 1),'P_ID = '.$_pageID);
                break;
        }
    }

    function buildMenu(){
        $Pages = $this->_db;
        $Select = $Pages->select()
                        ->from('PagesIndex')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->where('PagesIndex.PI_LanguageID = ?', $this->_defaultEditLanguage)
                        ->where('Pages.P_ParentID = ?', '0')
                        ->order('Pages.P_Position');
        if (!empty($this->_siteType))
            $Select->where('Pages.P_SiteType = ?', $this->_siteType);

        $Rows = $Pages->fetchAll($Select);
        $menu = '';
        foreach($Rows as $Row){
            $menu .= '<li id="page_'.$Row['PI_PageID'].'" class="sortable_items collapsed possible_homepage '.( $Row['P_Home'] || $Row['P_HomeMobile'] ? ' home ': '' ).'" pageid="'.$Row['PI_PageID'].'" parentid="'.$Row['P_ParentID'].'">
                <a class="state" onclick="toggleMenu(this)">&nbsp;</a>';

            if($Row['PI_Status']==1){
                $menu .= '<img class="drag_handle" src="'.$this->view->baseUrl().'/icons/icon-drag-22x22.png" align="left" />';
                $menu .= '<a class="element" onclick="toggleActionMenu(this)">'.( $Row['P_Home'] || $Row['P_HomeMobile'] ? $this->view->getCibleText('label_homepage'): $Row['PI_PageTitle'] ).'</a>';
             }
            else{
                $menu .= '<img class="drag_handle" src="'.$this->view->baseUrl().'/icons/icon-drag-offline-22x22.png" align="left" />';
                $menu .= '<a style="font-style:italic;" class="element" onclick="toggleActionMenu(this)">'.( $Row['P_Home'] || $Row['P_HomeMobile'] ? $this->view->getCibleText('label_homepage'): $Row['PI_PageTitle'] ).'</a>';
            }

            if($this->view->isAdministrator()==1){
            $menu .= '<div class="actions-dialog"><div class="hd"><div class="c"></div></div><div class="bd"><div class="c"><div class="s"><div class="actions"><a class="action_add" onclick="addPage(this);">'.$this->view->getCibleText('menu_treeview_add').'</a> <a class="action_edit" onclick="editPage(this);">'.$this->view->getCibleText('menu_treeview_edit').'</a> <a class="action_delete" onclick="deletePage(this);">'.$this->view->getCibleText('menu_treeview_delete').'</a> <a class="action_setAsHomePage" onclick="setAsHomePage(this);">'.$this->view->getCibleText('menu_treeview_homepage').'</a> <a class="action_viewStructure" onclick="viewStructure(this);">'.$this->view->getCibleText('menu_treeview_pagestructure').'</a> <a class="action_viewContents" onclick="viewContents(this)">'.$this->view->getCibleText('menu_treeview_content_management').'</a></div></div></div></div><div class="ft"><div class="c"></div></div></div>';
            }
            else{
                $menu .= '<div class="actions-dialog"><div class="hd"><div class="c"></div></div><div class="bd"><div class="c"><div class="s"><div class="actions"><a class="action_add" onclick="addPage(this);">'.$this->view->getCibleText('menu_treeview_add').'</a> <a class="action_edit" onclick="editPage(this);">'.$this->view->getCibleText('menu_treeview_edit').'</a> <a class="action_delete" onclick="deletePage(this);">'.$this->view->getCibleText('menu_treeview_delete').'</a> <a class="action_viewStructure" onclick="viewStructure(this);">'.$this->view->getCibleText('menu_treeview_pagestructure').'</a> <a class="action_viewContents" onclick="viewContents(this)">'.$this->view->getCibleText('menu_treeview_content_management').'</a></div></div></div></div><div class="ft"><div class="c"></div></div></div>';
            }
            // get all childrens of the page
            $menu .=  $this->findChildPages($Row['P_ID']);
            $menu .= '</li>';
        }

        return $menu;
    }

    function findChildPages($ParentID){
        // get all childrens associated with the parentID
        $Pages = $this->_db;
        $Select = $Pages->select()
                        ->from('PagesIndex')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->where('Pages.P_ParentID = ?', $ParentID)
                        ->where('PagesIndex.PI_LanguageID = ?', $this->_defaultEditLanguage)
                        ->where('Pages.P_ParentID <> ?', '0')
                        ->order('Pages.P_Position');

        $Rows = $Pages->fetchAll($Select);

        // continue to build the tree...
        $menu = '';
        if(count($Rows) > 0){
            $menu  = '<ul class="zone tree-view" style="display: none">';
            foreach($Rows as $Row){
               $menu .= '<li id="page_'.$Row['PI_PageID'].'" class="sortable_items collapsed" pageid="'.$Row['PI_PageID'].'" parentid="'.$Row['P_ParentID'].'"><a class="state" onclick="toggleMenu(this)">&nbsp;</a>';

               if($Row['PI_Status']==1){
                    $menu .= '<img class="drag_handle" src="'.$this->view->baseUrl().'/icons/icon-drag-22x22.png" align="left" />';
                    $menu .= '<a class="element" onclick="toggleActionMenu(this)">'.( $Row['P_Home'] || $Row['P_HomeMobile'] ? $this->view->getCibleText('label_homepage'): $Row['PI_PageTitle'] ).'</a>';
                }
                else{
                    $menu .= '<img class="drag_handle" src="'.$this->view->baseUrl().'/icons/icon-drag-offline-22x22.png" align="left" />';
                    $menu .= '<a class="element" onclick="toggleActionMenu(this)" style="font-style:italic;">'.( $Row['P_Home'] || $Row['P_HomeMobile'] ? $this->view->getCibleText('label_homepage'): $Row['PI_PageTitle'] ).'</a>';
                }

                if($this->view->isAdministrator()==1){
                $menu .= '<div class="actions-dialog"><div class="hd"><div class="c"></div></div><div class="bd"><div class="c"><div class="s"><div class="actions"><a class="action_add" onclick="addPage(this);">'.$this->view->getCibleText('menu_treeview_add').'</a> <a class="action_edit" onclick="editPage(this);">'.$this->view->getCibleText('menu_treeview_edit').'</a> <a class="action_delete" onclick="deletePage(this);">'.$this->view->getCibleText('menu_treeview_delete').'</a> <a class="action_setAsHomePage" onclick="setAsHomePage(this);">'.$this->view->getCibleText('menu_treeview_homepage').'</a> <a class="action_viewStructure" onclick="viewStructure(this);">'.$this->view->getCibleText('menu_treeview_pagestructure').'</a> <a class="action_viewContents" onclick="viewContents(this)">'.$this->view->getCibleText('menu_treeview_content_management').'</a></div></div></div></div><div class="ft"><div class="c"></div></div></div>';
                }
                else{
                    $menu .= '<div class="actions-dialog"><div class="hd"><div class="c"></div></div><div class="bd"><div class="c"><div class="s"><div class="actions"><a class="action_add" onclick="addPage(this);">'.$this->view->getCibleText('menu_treeview_add').'</a> <a class="action_edit" onclick="editPage(this);">'.$this->view->getCibleText('menu_treeview_edit').'</a> <a class="action_delete" onclick="deletePage(this);">'.$this->view->getCibleText('menu_treeview_delete').'</a> <a class="action_viewStructure" onclick="viewStructure(this);">'.$this->view->getCibleText('menu_treeview_pagestructure').'</a> <a class="action_viewContents" onclick="viewContents(this)">'.$this->view->getCibleText('menu_treeview_content_management').'</a></div></div></div></div><div class="ft"><div class="c"></div></div></div>';
                }
                // get all childrens of the page
                $menu .=  $this->findChildPages($Row['P_ID']);
                $menu .= '</li>';
            }
            $menu .= '</ul>';
        }
        return $menu;
    }

    public function formatNameAction()
    {
        $this->disableView();
        $oObj = new PagesObject();

        $select = $oObj->getAll(null, false);
//        $select->where('PI_ValUrl is NULL');
        $db = Zend_Registry::get('db');

        $data = $db->fetchAll($select);
        foreach ($data as $values)
        {
            $name = $values['PI_PageIndex'];
            $formatted = Cible_FunctionsGeneral::formatValueForUrl($name);

            $tmpdata = array(
                'PI_PageIndex' => $formatted
                );
            $oObj->save($values['P_ID'], $tmpdata, $values['PI_LanguageID']);

        }
    }
}
