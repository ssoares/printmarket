<?php


class Video_IndexController extends Cible_Controller_Categorie_Action
{
    protected $_labelSuffix = 'video';
    protected $_colTitle      = array();
    protected $_moduleID      = 18;
    protected $_defaultAction = 'list';
    protected $_defaultRender = 'list';
    protected $_moduleTitle   = 'video';
    protected $_name          = 'index';
    protected $_ID            = 'id';
    protected $_currentAction = '';
    protected $_actionKey     = '';
    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName   = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'list-group'  => 'GroupObject',
        'list-images' => 'BannerImageObject'

    );
    protected $_actionsList = array();

    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF   = false;
    protected $_disableExportToCSV   = false;
    protected $_enablePrint          = false;

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        // Sets the called action name. This will be dispatched to the method
        $this->_currentAction = $this->_getParam('action');

        // The action (process) to do for the selected object
        $this->_actionKey = $this->_getParam('actionKey');

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);

        $dataImagePath = "../../"
                . $this->_config->document_root
                . "/data/images/";



        //if(isset($this->_objectList[$this->_currentAction]))
            $this->_imageFolder = $dataImagePath . $this->_moduleTitle . "/";
                   // . $this->_objectList[$this->_currentAction] . "/";

        if(isset($this->_objectList[$this->_currentAction]))
            $this->_rootImgPath = Zend_Registry::get("www_root")
                    . "/data/images/"
                    . $this->_moduleTitle . "/";
                   // . $this->_objectList[$this->_currentAction] . "/";


        //$this->_imageFolder = $this->_config->document_root . "/data/images/videos/";
    }

    /**
     * Add action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function addAction()
    {
        $pageID = $this->_getParam('pageID');
        $blockID = $this->_getParam('blockID');
        $baseDir = $this->view->baseUrl();

        $returnUrl = "/video/index/list/";


        $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
       // if ($this->view->aclIsAllowed('news', 'edit', true))
        {

            // generate the form
            $form = new FormVideo(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => "$baseDir$returnUrl",
                    'VI_ID' => '',
                    'VI_Poster' => '',
                    'VI_MP4' => '',
                    'VI_WEBM' => '',
                    'VI_OGG' => '',
                    'VI_Description' => '',
                    'VI_Name' => '',
                    'V_Alias' => '',
                    'V_Width' => '',
                    'V_Height' => '',
                    'V_Autoplay' => 0,
                    'isNewPoster' => true,
                    'isNewMP4' => true,
                    'isNewWEBM' => true,
                    'isNewOGG' => true,
                    'addAction' => true,
                    'imageSrc' => $imageSrc,
                    'lang' => Zend_Registry::get('languageSuffix')
                    /* ,
                      'toApprove' => 0,
                      'status'    => 2 */
                ));
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $videoObject = new VideoObject();
                    if($form->getValue('VI_MP4')<>''){
                        $srcVI_MP4O = $form->getValue('VI_MP4');
                        $mp4 = $this->getVideoName($srcVI_MP4O);
                        $formData['VI_MP4'] = $mp4;
                    }
                     if($form->getValue('VI_WEBM')<>''){
                        $srcVI_WEBM = $form->getValue('VI_WEBM');
                        $webm = $this->getVideoName($srcVI_WEBM);
                        $formData['VI_WEBM'] = $webm;
                    }
                     if($form->getValue('VI_OGG')<>''){
                        $srcVI_OGG = $form->getValue('VI_OGG');
                        $ogg = $this->getVideoName($srcVI_OGG);
                        $formData['VI_OGG'] = $ogg;
                    }
                    $videoID = $videoObject->insert($formData, $this->_currentEditLanguage);

                    $this->mediaListVideoMaker();

                    $config = Zend_Registry::get('config')->toArray();
                    if ($form->getValue('VI_Poster') <> ''){

                        $srcVI_PosterO = "../../{$this->_config->document_root}/data/images/videos/tmp/" . $form->getValue('VI_Poster');
                        $srcVI_PosterL = "../../{$this->_config->document_root}/data/images/videos/" . $form->getValue('VI_Poster');
                        copy($srcVI_PosterO, $srcVI_PosterL);
                        //echo $srcVI_PosterO . " | " . $srcVI_PosterL . "<br />";
                    }
                    if($form->getValue('VI_MP4')<>''){
                        $srcVI_MP4O = $form->getValue('VI_MP4');
                        /*$srcVI_MP4O = $this->getRelativePathVideo($srcVI_MP4O);
                        $mp4 = $this->getVideoName($srcVI_MP4O);
                        $srcVI_MP4L = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $mp4;
                        copy($srcVI_MP4O, $srcVI_MP4L);*/
                    }
                    if($form->getValue('VI_WEBM')){
                        $srcVI_WEBMO = $form->getValue('VI_WEBM');
                        /*$srcVI_WEBMO = $this->getRelativePathVideo($srcVI_WEBMO);
                        $webm = $this->getVideoName($srcVI_WEBMO);
                        $srcVI_WEBML = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $webm;
                        copy($srcVI_WEBMO, $srcVI_WEBML);*/
                    }
                    if($form->getValue('VI_OGG')){
                        $srcVI_OGGO =  $form->getValue('VI_OGG');
                       /* $srcVI_OGGO = $this->getRelativePathVideo($srcVI_OGGO);
                        $ogg = $this->getVideoName($srcVI_OGGO);
                        $srcVI_OGGL = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $ogg;
                        copy($srcVI_OGGO, $srcVI_OGGL); */

                    }
                    //exit;

                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect('video/index/edit/videoID/' . $videoID . "/");
                }
                else
                    $form->populate($formData);
            }
        }
    }

    /**
     * Edit action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function editAction()
    {
        $pageID = $this->_getParam('pageID');
        $videoID = $this->_getParam('videoID');
        $blockID = $this->_getParam('blockID');
        $baseDir = $this->view->baseUrl();
        $videoObject = new VideoObject();
        $returnUrl = "/video/index/list/";


       // if ($this->view->aclIsAllowed('news', 'edit', true))
        {
            $imageSrc = "";
            $ocjVideo = $videoObject->populate($videoID, $this->getCurrentEditLanguage());

            if ($this->_request->isPost())
            {
                //var_dump($this->_request->getPost());
                //exit;
                $formData = $this->_request->getPost();

                if($formData['VI_Poster']==""){
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                }
                else{
                    $imageSrc = $data[$this->_imageSrc];
                }
            }
            else{
                if($ocjVideo['VI_Poster']==""){
                    $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                }
                else{
                    $imageSrc = Zend_Registry::get("www_root") . "/data/files/videos/" . $ocjVideo['VI_Poster'];
                }
            }

            $form = new FormVideo(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => "$baseDir$returnUrl",
                    'VI_ID' => $videoID,
                    'VI_Poster' => $ocjVideo['VI_Poster'],
                    'VI_MP4' => $ocjVideo['VI_MP4'],
                    'VI_WEBM' => $ocjVideo['VI_WEBM'],
                    'VI_OGG' => $ocjVideo['VI_OGG'],
                    'VI_Description' => $ocjVideo['VI_Description'],
                    'VI_Name' => $ocjVideo['VI_Name'],
                    'V_Alias' => $ocjVideo['V_Alias'],
                    'V_Width' => $ocjVideo['V_Width'],
                    'V_Height' => $ocjVideo['V_Height'],
                    'V_Autoplay' => $ocjVideo['V_Autoplay'],
                    'isNewPoster' => 0,
                    'isNewMP4' => 0,
                    'isNewWEBM' => 0,
                    'isNewOGG' => 0,
                    //'addAction' => 0,
                    'imageSrc' => $imageSrc,
                    'lang' => Zend_Registry::get('languageSuffix')
                    /* ,
                      'toApprove' => 0,
                      'status'    => 2 */
                ));
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData))
                {
                    if($form->getValue('VI_MP4')<>''){
                        $srcVI_MP4O = $form->getValue('VI_MP4');
                        $mp4 = $this->getVideoName($srcVI_MP4O);
                        $formData['VI_MP4'] = $mp4;
                    }
                     if($form->getValue('VI_WEBM')<>''){
                        $srcVI_WEBM = $form->getValue('VI_WEBM');
                        $webm = $this->getVideoName($srcVI_WEBM);
                        $formData['VI_WEBM'] = $webm;
                    }
                     if($form->getValue('VI_OGG')<>''){
                        $srcVI_OGG = $form->getValue('VI_OGG');
                        $ogg = $this->getVideoName($srcVI_OGG);
                        $formData['VI_OGG'] = $ogg;
                    }
                    $videoObject->save($videoID, $formData, $this->_currentEditLanguage);
                    $this->mediaListVideoMaker();

                    $config = Zend_Registry::get('config')->toArray();
                     //var_dump($form);
                    $config = Zend_Registry::get('config')->toArray();
                    if ($form->getValue('VI_Poster') <> ''){

                        $srcVI_PosterO = "../../{$this->_config->document_root}/data/images/videos/tmp/" . $form->getValue('VI_Poster');
                        $srcVI_PosterL = "../../{$this->_config->document_root}/data/images/videos/" . $form->getValue('VI_Poster');
                        copy($srcVI_PosterO, $srcVI_PosterL);
                        //echo $srcVI_PosterO . " | " . $srcVI_PosterL . "<br />";
                    }
                    if($form->getValue('VI_MP4')<>''){
                        $srcVI_MP4O = $form->getValue('VI_MP4');
                       /* $srcVI_MP4O = $this->getRelativePathVideo($srcVI_MP4O);
                        $mp4 = $this->getVideoName($srcVI_MP4O);
                        $srcVI_MP4L = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $mp4;
                        copy($srcVI_MP4O, $srcVI_MP4L);*/
                        //echo $srcVI_MP4O . " | " . $srcVI_MP4L . "<br />";
                    }
                    if($form->getValue('VI_WEBM')){
                        $srcVI_WEBMO = $form->getValue('VI_WEBM');
                       /* $srcVI_WEBMO = $this->getRelativePathVideo($srcVI_WEBMO);
                        $webm = $this->getVideoName($srcVI_WEBMO);
                        $srcVI_WEBML = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $webm;
                        copy($srcVI_WEBMO, $srcVI_WEBML);*/
                        //echo $srcVI_WEBMO . " | " . $srcVI_WEBML . "<br />";
                    }
                    if($form->getValue('VI_OGG')){
                        $srcVI_OGGO =  $form->getValue('VI_OGG');
                        /*$srcVI_OGGO = $this->getRelativePathVideo($srcVI_OGGO);
                        $ogg = $this->getVideoName($srcVI_OGGO);
                        $srcVI_OGGL = "../../{$this->_config->document_root}/data/images/videos/" . $videoID . "/" . Zend_Registry::get('languageSuffix') . "/" . $ogg;
                        copy($srcVI_OGGO, $srcVI_OGGL); */
                        //echo $srcVI_OGGO . " | " . $srcVI_OGGL . "<br />";
                    }
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect('video/index/edit/videoID/' . $videoID . "/lang/" . Zend_Registry::get('languageSuffix') . "/return/list/");
                }
                else
                    $form->populate($formData);
            }
            else
            {
               // var_dump($ocjVideo);
                $form->populate($ocjVideo);
            }
        }
    }

    /**
     * Delete action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function deleteAction()
    {
        // variables
        $page = (int) $this->_getParam('page');
        $blockId = (int) $this->_getParam('blockID');
        $id = (int) $this->_getParam('videoID');

       // var_dump($id);// $id;
       // exit;
        $this->view->return = $this->view->baseUrl() . "/"
                . $this->_moduleTitle . "/"
                . $this->_name . "/"
                . "list/"
                . "page/" . $page;

        $this->view->action = $this->_currentAction;

        $returnAction = $this->_getParam('return');

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/index/" . $returnAction;
        else
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "page/" . $page;

        $oData = new VideoObject();


        //if (Cible_ACL::hasAccess($page))
       // {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $id > 0)
                {
                    //Cible_FunctionsGeneral::delFolder($this->_imageFolder . $id);
                    $oData->delete($id);
                }
                $this->_redirect($returnUrl);
            }
            elseif ($id > 0)
            {
                // get data details
                $this->view->data = $oData->populate($id, $this->getCurrentEditLanguage());
            }
       // }
    }

    /**
     * Creates the list of data for this action for the current object.
     *
     * @access public
     *
     * @param string $objectName String tot create the good object.
     *
     * @return void
     */
    public function listAction()
    {

        // if ($this->view->aclIsAllowed('news', 'edit', true))
        {

            $lang = $this->_getParam('lang');
            if (!$lang)
            {
                $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
                $langId = $this->_defaultEditLanguage;
            }
            else
            {
                $langId = Cible_FunctionsGeneral::getLanguageID($lang);
                $this->_registry->currentEditLanguage = $langId;
            }

            // NEW LIST GENERATOR CODE //
            $tables = array(
                'Videos' => array('V_ID', 'V_Alias'),
                'VideosIndex' => array('VI_ID', 'VI_Description')
            );

            $field_list = array(
                'V_Alias' => array(
                    'width' => '300px'
                ),
                'VI_Description' => array(
                //'width' => '300px'
                )
            );

            $video = new VideoData();
            $select = $video->select()
                    ->from('Videos')
                    ->setIntegrityCheck(false)
                    ->join('VideosIndex', 'Videos.V_ID = VideosIndex.VI_ID')
                    ->joinRight('Languages', 'Languages.L_ID = VideosIndex.VI_LanguageID')
                    ->where('VI_LanguageID = ?', $langId);


            $options = array(
                'commands' => array(
                    $this->view->link($this->view->url(array('controller' => 'index', 'action' => 'add')), $this->view->getCibleText('button_add_video'), array('class' => 'action_submit add'))
                ),
                //'disable-export-to-excel' => 'true',
                'to-excel-action' => 'all-video-to-excel',

                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => "{$this->view->baseUrl()}/video/index/edit/videoID/%ID%/lang/%LANG%/return/list/",
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => 'V_ID'
                                ),
                                array(
                                    'search' => '%LANG%',
                                    'replace' => 'L_Suffix'
                                )
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => "{$this->view->baseUrl()}/video/index/delete/videoID/%ID%/return/list/",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'V_ID'
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

            $this->view->assign('mylist', $mylist);
        }
    }

    private function mediaListVideoMaker(){

        $videoObject = new VideoObject();
        $objVideo = $videoObject->getVideosList();
        $baseUrlVideo = $this->view->baseUrl();
        $pos = strripos($baseUrlVideo, "/");

        $baseUrlVideo = substr($baseUrlVideo,0,$pos);
        $baseUrlVideo .= "/data/files/videos/";

        $stringData = 'var tinyMCEMediaList = new Array(';
        foreach ($objVideo as $key => $video)
        {
            if($key>0)
                $stringData .= ",";

            $stringData .= "[";
            $stringData .= '"' . $video['V_Alias'] . '",';
            $stringData .= "[";
            $stringData .= '["VI_WEBM", "' . $baseUrlVideo . $video['VI_WEBM'] . '"],';
             $stringData .= '["VI_MP4", "' . $baseUrlVideo . $video['VI_MP4'] . '"],';
            $stringData .= '["VI_OGG", "' . $baseUrlVideo . $video['VI_OGG'] . '"],';
            $stringData .= '["V_Autoplay",' . $video['V_Autoplay'] . '],';
            $stringData .= '["V_Width",' . $video['V_Width'] . '],';
            $stringData .= '["V_Height",' . $video['V_Height'] . '],';
            $stringData .= '["VI_Name","' .  $video['VI_Name'] . '"],';
            $stringData .= '["VI_Poster", "' . $baseUrlVideo . $video['VI_Poster'] . '"]';
            $stringData .= ']';
            $stringData .= ']';
        }

        $stringData .= ');';

        $dataFilePath = "../../" . $this->_config->document_root
                . "/data/files/videos/media_list.js";

        $fh = fopen($dataFilePath, 'w');
        fwrite($fh, $stringData);
        fclose($fh);

        //echo $stringData;
      // exit;
    }


    /**
     * Export data according to given parameters.
     *
     * @return void
     */
    public function toExcelAction()
    {
        $this->type     = 'CSV';
        $this->filename = $this->_actionKey . '.csv';
        $params         = array();

        $actionName = $this->_actionKey . 'Action';
        $params     = $this->$actionName(true);
        $oDataName  = $this->_objectList[$this->_actionKey];
        $lines      = new $oDataName();
        $foreignKey = $lines->getForeignKey();

        $params['foreignKey'] = $foreignKey;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $columns       = array_keys($params['columns']);
        $this->fields  = array_combine($columns, $columns);
        $this->filters = array();

        $pageID = $this->_getParam('pageID');
        $langId = $this->_defaultEditLanguage;

        $select = $lines->getAll($langId, false);
        $select = $this->_addJoinQuery($select, $params);

        $this->select = $select;

        parent::toExcelAction();
    }

    /**
     * Format the current action name to bu used for label texts translations.
     *
     * @access private
     *
     * @return void
     */
    private function _formatName()
    {
        $this->_labelSuffix = str_replace(array('/', '-'), '_', $this->_currentAction);
    }

    /**
     * Reditects the current action to the "real" action to process.
     *
     * @access public
     *
     * @return void
     */
    private function redirectAction()
    {
        //Redirect to the real action to process If no actionKey = list page.
        switch ($this->_actionKey)
        {
            case 'add':
                $this->addAction();
                $this->_helper->viewRenderer->setRender('add');
                break;
            case 'edit':
                $this->editAction();
                $this->_helper->viewRenderer->setRender('edit');
                break;
            case 'delete':
                $this->deleteAction();
                $this->_helper->viewRenderer->setRender('delete');
                break;

            default:
                $this->listAction($this->_objectList[$this->_currentAction]);
                break;
        }
    }

    /**
     * Set options array or the list view. Options are the actions in the page.
     *
     * @access public
     *
     * @param int $tabId Id of the row to be processed.
     * @param int $page  Id of the page if selected with the paginator.
     *
     * @return void
     */
    private function _setActionsList($tabId, $page = 1)
    {
        $commands = array();
        $actions = array();
        $actionPanel = array(
            'width' => '50px'
        );

        $options = array();

        if (count($this->_actionsList) == 0)
            $this->_actionsList = array(
                array('commands' => 'add'),
                array('action_panel' => 'edit', 'delete')
            );

        foreach ($this->_actionsList as $key => $controls)
        {
            foreach ($controls as $key => $action)
            {
                //Redirect to the real action to process If no actionKey = list page.
                switch ($action)
                {
                    case 'add':
                        $commands = array(
                            $this->view->link($this->view->url(
                                            array(
                                                'controller' => $this->_name,
                                                'action' => $this->_currentAction,
                                                'actionKey' => 'add')),
                                    $this->view->getCibleText('button_add_' . $this->_labelSuffix),
                                    array('class' => 'action_submit add'))
                        );
                        break;

                    case 'edit':
                        $edit = array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/edit/"
                            . $this->_ID . "/%ID%/page/" . $page,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            ),
                            'returnUrl' => $this->view->Url() . "/"
                        );
                        $actions['edit'] = $edit;
                        break;

                    case 'delete':
                        $delete = array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url'   => $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/delete/"
                            . $this->_ID . "/%ID%/page/" . $page,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            )
                        );

                        $actions['delete'] = $delete;
                        break;

                    default:

                        break;
                }
            }
        }
        $actionPanel['actions'] = $actions;

        $options = array(
            'commands'     => $commands,
            'action_panel' => $actionPanel
        );
        if ($this->_disableExportToExcel)
            $options['disable-export-to-excel']= 'true';
        if ($this->_disableExportToPDF)
            $options['disable-export-to-pdf']= 'true';
        if ($this->_disableExportToCSV)
            $options['disable-export-to-csv']= 'true';
        if ($this->_enablePrint)
            $options['enable-print']= 'true';

        $options['actionKey'] = $this->_currentAction;

        return $options;
    }

    /**
     * Transforms data of the posted form in one array
     *
     * @param array $formData Data to save.
     *
     * @return array
     */
    protected function _mergeFormData(array $formData)
    {
        (array)$tmpArray = array();

        foreach($formData as $key => $data)
        {
            if(is_array($data)){
                $tmpArray = array_merge($tmpArray,$data);
            }
            else
                $tmpArray[$key] = $data;
        }

        return $tmpArray;
    }

    /**
     * Add some data from other table, tests the joinTables
     * property. If not empty add tables and join clauses.
     *
     * @param Zend_Db_Table_Select $select
     * @param array $params
     *
     * @return Zend_Db_Table_Select
     */
    private function _addJoinQuery($select, array $params = array())
    {

        if (isset($params['joinTables']) && count($params['joinTables']))
            $this->_joinTables = $params['joinTables'];

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        if (count($this->_joinTables) > 0)
        {

            $foreignKey = $params['foreignKey'];
            // Loop on tables list(given by object class) to build the query
            foreach ($this->_joinTables as $key => $object)
            {
                //Create an object and fetch data from object.
                $tmpObject = new $object();
                $tmpDataTable = $tmpObject->getDataTableName();
                $tmpIndexTable = $tmpObject->getIndexTableName();
                $tmpColumnData = $tmpObject->getDataColumns();
                $tmpColumnIndex = $tmpObject->getIndexColumns();
                //Add data to tables list
                $tables[$tmpDataTable] = $tmpColumnData;
                $tables[$tmpIndexTable] = $tmpColumnIndex;
                //Get the primary key of the first data object to join table
                $tmpDataId = $tmpObject->getDataId();
                // If it's the first loop, join first table to the current table
                if ($key == 0)
                {
                    $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $foreignKey);
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }


                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    $select->joinLeft(
                            $tmpDataTable);

                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable);

                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

     private function getVideoName($videoPath){
        $videoName = "";
        $videoName = strrev($videoPath);
        $pos = strpos($videoName, "/");
        if ($pos === false) {
            return $videoPath;
        }
        $videoName = substr($videoName,0,$pos);
        $videoName = strrev($videoName);
        return $videoName;
    }

    private function getRelativePathVideo($videoPath){
        $videoName = "";
        $videoName = strrev($videoPath);
        $pos = strpos($videoName, "/");
        if ($pos === false) {
            return $videoPath;
        }
        $videoName = substr($videoName,0,$pos);
        $videoName = strrev($videoName);
        $videoName = "../../www/data/files/" . $videoName;
        return $videoName;
    }
}
