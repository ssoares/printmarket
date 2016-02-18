<?php


class Repertoire_IndexController extends Cible_Controller_Categorie_Action
{

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 20;
    protected $_defaultAction = 'repertoire';
    protected $_defaultRender = 'list';
    protected $_moduleTitle = 'repertoire';
    protected $_name = 'index';
    protected $_ID = 'id';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_imageSrc = '';
    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'repertoire' => 'RepertoireObject',
        'groupe' => 'GroupeObject',
        'region' => 'RegionObject'
    );
    protected $_actionsList = array();
    protected $_renderPartial = '';
    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF = false;
    protected $_disableExportToCSV = false;
    protected $_enablePrint = false;
    protected $_forceView = false;
    protected $_constraint;
    protected $_filterData = array();

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
        $this->view->assign('actionKey', $this->_labelSuffix);

        $dataImagePath = "../../"
            . $this->_config->document_root
            . "/data/images/";

        if (isset($this->_objectList[$this->_currentAction]))
            $this->_imageFolder = $dataImagePath
                . $this->_moduleTitle . "/";

        if (isset($this->_objectList[$this->_currentAction]))
            $this->_rootImgPath = Zend_Registry::get("www_root")
                . "/data/images/"
                . $this->_moduleTitle . "/";

    }

    /**
     * Allocates action for Boroughs (arrondissements) management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function repertoireAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_constraint = '';
            $this->_colTitle = array(
                'RD_ID' => array('width' => '150px'),
                'RI_Name' => array('width' => '150px'),
                'RI_Surname' => array('width' => '150px'),
            );
            if ($getParams)
            {
                $params = array(
                    'columns' => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormRepertoire';
            $this->_redirectAction();
        }
    }
    /**
     * Allocates action for Boroughs (arrondissements) management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function groupeAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'G_ID' => array('width' => '150px'),
                'GI_Name' => array('width' => '150px')
            );

//            $this->_joinTables = array('');

            if ($getParams)
            {
                $params = array(
                    'columns' => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormGroupe';
            $this->_redirectAction();
        }
    }
    /**
     * Allocates action for Boroughs (arrondissements) management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function regionAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'RG_ID' => array('width' => '150px'),
                'GI_Name' => array('width' => '150px'),
                'RGI_Name' => array('width' => '150px')
            );

            $this->_joinTables = array('GroupeObject');

            if ($getParams)
            {
                $params = array(
                    'columns' => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormRegion';
            $this->_redirectAction();
        }
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
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();
        $tmpVal = $this->_getParam('perPage');
        $perpage = '';
        if (!empty($tmpVal))
            $perpage = "perPage/" . $this->_getParam('perPage');
        $this->_registry->currentEditLanguage = $this->_registry->languageID;

        $returnAction = $this->_getParam('return');

        $baseDir = $this->view->baseUrl() . "/";
        $this->view->locale = Cible_FunctionsGeneral::getLanguageSuffix($this->_defaultEditLanguage);

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if (isset($formData[$this->_imageSrc]) && $formData[$this->_imageSrc] <> "")
                    if ($formData[$this->_imageSrc] <> "")
                        $imageSrc = $this->_rootImgPath
                            . "tmp/mcith/mcith_"
                            . $formData[$this->_imageSrc];
            }

            if ($returnAction)
                $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction
                    . $perpage;
            else
                $returnUrl = $this->_moduleTitle
                    . "/" . $this->_name . "/" . $this->_currentAction
                    . "/actionKey/list/" . $perpage;

            // generate the form
            $form = new $this->_formName(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => "$baseDir$returnUrl",
                    'moduleName' => $this->_moduleTitle,
                    'imageSrc' => $imageSrc,
                    'object' => $oData,
                    'imgField' => $this->_imageSrc,
                    'mode' => $this->_actionKey,
                    'dataId' => '',
                    'isNewImage' => true
                ));

            $this->view->form = $form;

            // action
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData))
                {
//                    $formData = $this->_mergeFormData($formData);
                    $recordID = $oData->insert($formData, $this->_defaultEditLanguage);
                    /* IMAGES */
                    if (!empty($this->_imageSrc) && !is_dir($this->_imageFolder . $recordID))
                    {
                        mkdir($this->_imageFolder . $recordID)
                            or die("Could not make directory");
                        mkdir($this->_imageFolder . $recordID . "/tmp")
                            or die("Could not make directory");
                    }

                    if ($form->getValue($this->_imageSrc) <> '')
                    {
                        //Get config data
                        $config = Zend_Registry::get('config')->toArray();
                        //Set sizes for the image
                        $srcOriginal = $this->_imageFolder . "tmp/" . $form->getValue($this->_imageSrc);
                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
                        $originalMaxWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];

                        $originalName = str_replace(
                            $form->getValue($this->_imageSrc), $originalMaxWidth
                            . 'x'
                            . $originalMaxHeight
                            . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc)
                        );


                        $srcMedium = $this->_imageFolder
                            . "tmp/medium_"
                            . $form->getValue($this->_imageSrc);
                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
                        $mediumName = str_replace(
                            $form->getValue($this->_imageSrc), $mediumMaxWidth
                            . 'x'
                            . $mediumMaxHeight
                            . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc)
                        );

                        $srcThumb = $this->_imageFolder
                            . "tmp/thumb_"
                            . $form->getValue($this->_imageSrc);
                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
                        $thumbName = str_replace(
                            $form->getValue($this->_imageSrc), $thumbMaxWidth
                            . 'x'
                            . $thumbMaxHeight
                            . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc)
                        );

                        copy($srcOriginal, $srcMedium);
                        copy($srcOriginal, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcOriginal,
                                'maxWidth' => $originalMaxWidth,
                                'maxHeight' => $originalMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcMedium,
                                'maxWidth' => $mediumMaxWidth,
                                'maxHeight' => $mediumMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcThumb,
                                'maxWidth' => $thumbMaxWidth,
                                'maxHeight' => $thumbMaxHeight)
                        );

                        rename($srcOriginal, $this->_imageFolder . $recordID . "/" . $originalName);
                        rename($srcMedium, $this->_imageFolder . $recordID . "/" . $mediumName);
                        rename($srcThumb, $this->_imageFolder . $recordID . "/" . $thumbName);
                    }

                    // redirect
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect(
                            $this->_moduleTitle . "/"
                            . $this->_name . "/" . $this->_currentAction
                            . "/actionKey/edit/" . $this->_ID . "/" . $recordID
                            . '/' . $perpage);
                }
                else
                {
                    $form->populate($formData);
                }
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
        $imageSrc = "";
        $id = (int) $this->_getParam($this->_ID);
        $page = (int) $this->_getParam('page');
        $tmpVal = $this->_getParam('perPage');
        $perpage = '';
        if (!empty($tmpVal))
            $perpage = "perPage/" . $this->_getParam('perPage');

        $session = new Zend_Session_Namespace();
        $session->parameters['lastSelected'] = $id;

        $baseDir = $this->view->baseUrl() . "/";
        $returnAction = $this->_getParam('return');
        $cancelUrl = $baseDir
            . $this->_moduleTitle . "/"
            . $this->_name . "/"
            . $this->_currentAction . "/"
            . "actionKey/list/"
            . "page/" . $page . '/'
            . $perpage;

        $oDataName = $this->_objectList[$this->_currentAction];

        $oData = new $oDataName();

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $returnUrl = $this->_moduleTitle . "/"
                . $this->_name . "/"
                . $this->_currentAction . "/"
                . "actionKey/list/"
                . "page/" . $page . '/'
                . $perpage;

            // Get data details
            $data = $oData->populate($id, $this->_currentEditLanguage);

            $this->view->locale = Cible_FunctionsGeneral::getLanguageSuffix($this->_currentEditLanguage);
            // image src.
            if (!empty($this->_imageSrc))
            {
                $config = Zend_Registry::get('config')->toArray();
                $thumbMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                $thumbMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];

                $this->view->assign(
                    'imageUrl', $this->_rootImgPath
                    . $id . "/"
                    . str_replace(
                        $data[$this->_imageSrc], $thumbMaxWidth
                        . 'x'
                        . $thumbMaxHeight
                        . '_'
                        . $data[$this->_imageSrc], $data[$this->_imageSrc])
                );
                $isNewImage = 'false';

                if ($this->_request->isPost())
                {
                    $formData = $this->_request->getPost();
                    if ($formData[$this->_imageSrc] <> $data[$this->_imageSrc])
                    {
                        if ($formData[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                . $id
                                . "/tmp/mcith/mcith_"
                                . $formData[$this->_imageSrc];

                        $isNewImage = 'true';
                    }
                    else
                    {
                        if ($data[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                . $id . "/"
                                . str_replace(
                                    $data[$this->_imageSrc], $thumbMaxWidth
                                    . 'x'
                                    . $thumbMaxHeight . '_'
                                    . $data[$this->_imageSrc], $data[$this->_imageSrc]);
                    }
                }
                else
                {
                    if (empty($data[$this->_imageSrc]))
                        $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                    else
                        $imageSrc = $this->_rootImgPath
                            . $id . "/"
                            . str_replace(
                                $data[$this->_imageSrc], $thumbMaxWidth
                                . 'x'
                                . $thumbMaxHeight . '_'
                                . $data[$this->_imageSrc], $data[$this->_imageSrc]);
                }
            }
            // generate the form
            $form = new $this->_formName(
                    array(
                        'moduleName' => $this->_moduleTitle,
                        'baseDir' => $baseDir,
                        'cancelUrl' => $cancelUrl,
                        'imageSrc' => $imageSrc,
                        'imgField' => $this->_imageSrc,
                        'mode' => $this->_actionKey,
                        'object' => $oData,
                        'dataId' => $id,
                        'isNewImage' => 'true'
                    )
            );
            $this->view->form = $form;

            // action
            if (!$this->_request->isPost())
            {
                $form->populate($data);
            }
            else
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
//                    $formData = $this->_mergeFormData($formData);
                    if ($formData['isNewImage'] == 'true' && $form->getValue($this->_imageSrc) <> '')
                    {
                        $config = Zend_Registry::get('config')->toArray();
                        $srcOriginal = $this->_imageFolder
                            . $id
                            . "/tmp/"
                            . $form->getValue($this->_imageSrc);
                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
                        $originalMaxWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
                        $originalName = str_replace(
                            $form->getValue($this->_imageSrc), $originalMaxWidth
                            . 'x'
                            . $originalMaxHeight . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc));


                        $srcMedium = $this->_imageFolder
                            . $id . "/tmp/medium_"
                            . $form->getValue($this->_imageSrc);

                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
                        $mediumName = str_replace(
                            $form->getValue($this->_imageSrc), $mediumMaxWidth
                            . 'x'
                            . $mediumMaxHeight . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc));

                        $srcThumb = $this->_imageFolder
                            . $id
                            . "/tmp/thumb_"
                            . $form->getValue($this->_imageSrc);
                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
                        $thumbName = str_replace(
                            $form->getValue($this->_imageSrc), $thumbMaxWidth
                            . 'x'
                            . $thumbMaxHeight . '_'
                            . $form->getValue($this->_imageSrc), $form->getValue($this->_imageSrc));

                        copy($srcOriginal, $srcMedium);
                        copy($srcOriginal, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcOriginal,
                                'maxWidth' => $originalMaxWidth,
                                'maxHeight' => $originalMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcMedium,
                                'maxWidth' => $mediumMaxWidth,
                                'maxHeight' => $mediumMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                            array(
                                'src' => $srcThumb,
                                'maxWidth' => $thumbMaxWidth,
                                'maxHeight' => $thumbMaxHeight)
                        );

                        rename($srcOriginal, $this->_imageFolder
                            . $id . "/" . $originalName);
                        rename($srcMedium, $this->_imageFolder
                            . $id . "/" . $mediumName);
                        rename($srcThumb, $this->_imageFolder
                            . $id . "/" . $thumbName);
                    }
                    $oData->save($id, $formData, $this->getCurrentEditLanguage());
                    // redirect
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect(
                            $this->_moduleTitle . "/"
                            . $this->_name . "/" . $this->_currentAction
                            . "/actionKey/edit/" . $this->_ID . "/" . $id
                            . '/' . $perpage);
                }
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
        $id = (int) $this->_getParam($this->_ID);
        $tmpVal = $this->_getParam('perPage');
        $perpage = '';
        if (!empty($tmpVal))
            $perpage = "/perPage/" . $this->_getParam('perPage');

        $this->view->return = $this->view->baseUrl() . "/"
            . $this->_moduleTitle . "/"
            . $this->_name . "/"
            . $this->_currentAction . "/"
            . "page/" . $page
            . $perpage;

        $this->view->action = $this->_currentAction;

        $returnAction = $this->_getParam('return');

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/index/" . $returnAction;
        else
            $returnUrl = $this->_moduleTitle . "/"
                . $this->_name . "/"
                . $this->_currentAction . "/"
                . "page/" . $page
                . $perpage;

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if (Cible_ACL::hasAccess($page))
        {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete') || $this->_getParam('del');

                if ($del && $id > 0)
                {
                    $oData->delete($id);
                }
                if (!$this->_isXmlHttpRequest)
                    $this->_redirect($returnUrl);
                else
                {
                    $this->disableView();
                    echo json_encode(true);
                }
            }
            elseif ($id > 0)
            {
                // get date details
                $this->view->data = $oData->populate($id, $this->getCurrentEditLanguage());
            }
        }
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
    private function _listAction($objectName)
    {
        $page = $this->_getParam('page');

        if ($page == '')
            $page = 1;
        // Create the object from parameter
        $oData = new $objectName();

        // get needed data to create the list
        $columnData  = $oData->getDataColumns();
        $dataTable   = $oData->getDataTableName();
        $indexTable  = $oData->getIndexTableName();
        $columnIndex = $oData->getIndexColumns();
        $tabId = $oData->getDataId();
        //Set the tables from previous collected data
        $tables = array(
            $dataTable => $columnData,
            $indexTable => $columnIndex
        );
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($this->_defaultEditLanguage, false);

        $params = array('foreignKey' => $oData->getForeignKey());

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);

        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;

        // Set the options of the list = links for actions (add, edit, delete...)
        $options = $this->_setActionsList($tabId, $page);
//        echo "<pre>";
//        print_r($select->assemble());
//        echo "</pre>";
//        exit;
        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
        // Assign a the view for rendering
        $this->_helper->viewRenderer->setRender($this->_defaultRender);
        //Assign to the render the list created previously.
        $this->view->assign('mylist', $mylist);
    }

    private function _sortParameters($data)
    {
        $session = new Zend_Session_Namespace();
        $params = array();

        if (isset($data['displaySearch']))
            $data = $this->_rebuildFilters($data);

        foreach ($data as $key => $value)
        {
            if (preg_match('/^BFF_/', $key))
                $params[$key] = $value;
            if ($key == 'clear')
                $session->parameters = array();
        }

        if (!empty($params))
            $session->parameters = $params;
    }

    private function _rebuildFilters(array $data = array())
    {
        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case 'BFF_RentSell':
                case 'BFF_Type':
                    if (!empty($value))
                        $data[$key] = explode ('-', $value);

                    break;
                case 'order_direction':
                        $data['order_direction'] = $value;
                        unset($data[$key]);
                    break;

                default:
                    break;
            }
        }

        return $data;
    }

    /**
     * Export data according to given parameters.
     *
     * @return void
     */
    public function toExcelAction()
    {
        $this->type = 'CSV';
        $this->filename = $this->_actionKey . '.csv';
        $params = array();

        $actionName = $this->_actionKey . 'Action';
        $params = $this->$actionName(true);
        $oDataName = $this->_objectList[$this->_actionKey];
        $lines = new $oDataName();
        $constraint = $lines->getForeignKey();

        $params['constraint'] = $constraint;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $columns = array_keys($params['columns']);
        $this->fields = array_combine($columns, $columns);
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
    private function _redirectAction()
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
                $this->_listAction($this->_objectList[$this->_currentAction]);
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

        $tmpVal = $this->_getParam('perPage');
        $perpage = '';
        if (!empty($tmpVal))
            $perpage = "/perPage/" . $this->_getParam('perPage');

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
                                        'actionKey' => 'add')), $this->view->getCibleText('button_add'), array('class' => 'action_submit add'))
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
                            . $this->_ID . "/%ID%/page/" . $page
                            . $perpage,
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
                    case 'print':
                        $edit = array(
                            'label' => $this->view->getClientText('share_print_text'),
                            'url' => $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/print/"
                            . $this->_ID . "/%ID%/page/" . $page
                            . $perpage,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            ),
                            'returnUrl' => $this->view->Url() . "/"
                        );

                        $actions['print'] = $edit;
                        break;

                    case 'delete':
                        $delete = array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/delete/"
                            . $this->_ID . "/%ID%/page/" . $page
                            . $perpage,
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
            'commands' => $commands,
            'action_panel' => $actionPanel
        );
        if ($this->_disableExportToExcel)
            $options['disable-export-to-excel'] = 'true';
        if ($this->_disableExportToPDF)
            $options['disable-export-to-pdf'] = 'true';
        if ($this->_disableExportToCSV)
            $options['disable-export-to-csv'] = 'true';
        if ($this->_enablePrint)
            $options['enable-print'] = 'true';
        if ($this->_renderPartial)
            $options['renderPartial'] = $this->_renderPartial;
        if (!empty($this->_filterData))
            $options['filters'] =  $this->_filterData;

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
        (array) $tmpArray = array();

        foreach ($formData as $key => $data)
        {
            if (is_array($data))
            {
                $tmpArray = array_merge($tmpArray, $data);
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
            // Get the constraint attribute = foreign key to link tables.
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
                                $tmpIndexTable.'.'.$tmpObject->getIndexLanguageId() . ' = ?',
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
                                $tmpIndexTable . $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

    public function sendBuildingsListAction(array $data, $oData, $formEmail)
    {
        $session = new Zend_Session_Namespace();
        $alertMsg = array();

        if (!empty($data['buildingsId']) && !empty($data['emailList']))
        {
            $buildingsList = explode(',', $data['buildingsId']);
            $session->buildingList = $buildingsList;

            foreach ($buildingsList as $building)
            {
                $tmp = $oData->getAllData($data['language'], false, $building);
                if (!empty($tmp))
                {
                    $token = md5($building);
                    $linkUrl = Zend_Registry::get('absolute_web_root') . "/"
                    . Cible_FunctionsCategories::getPagePerCategoryView(
                            0,
                            'details',
                            $this->_moduleID,
                            $data['language'])
                    . "/building/{$tmp[0]['B_ID']}/token/{$token}";
                    $link = $this->view->link($linkUrl, $this->view->getCibleText('see_building_details', $data['language']));
                    $data['buildingList'][$tmp[0]['B_ID']] = array($tmp[0]['B_Number'], $tmp[0]['AI_FirstAddress'], $link);
                }
                else
                {
                    $alertMsg['dataLang'] = array('valid' => true, 'level' => 2);
                    $alertMsg['listSend'] = array('valid' => true, 'level' => 2);
                }
            }

            $columns = array(
                array('title' => $this->view->getCibleText('form_label_B_Number', $data['language'])),
                array('title' => $this->view->getCibleText('form_label_firstAddress', $data['language'])),
                array('title' => $this->view->getCibleText('see_building_details', $data['language'])),
            );
            $table = Cible_FunctionsGeneral::generateHtmlTable($columns, $data['buildingList']);
            if (isset($data['buildingList']) && count($data['buildingList']) && empty($alertMsg['listSend']))
            {
                $emailData = array(
                    'preMessage' => $data['preMessage'],
                    'list' => $table,
                    'postMessage' => $data['postMessage'],
                    'language' => $data['language'],
                );
                $mailsTo = explode(';', $data['emailList']);
                $options = array(
                    'send' => true,
                    'isHtml' => true,
                    'to' => $mailsTo,
                    'moduleId' => $this->_moduleID,
                    'event' => 'sendList',
                    'type' => 'email',
                    'recipient' => 'client',
                    'data' => $emailData
                );

                if (!empty($data['attachedFile']))
                    $options['attachment'] = $data['attachedFile'];

                try
                {
                    $oNotification = new Cible_Notifications_Email($options);
                    if (empty($alertMsg['listSend']))
                        $alertMsg['listSend'] = array('valid' => true, 'level' => 3);

                }
                catch (Exception $exc)
                {
                    $alertMsg['listSend'] = array('valid' => false, 'level' => 1);
                }
            }
            else
            {
                if (!empty($alertMsg['listSend']))
                    $alertMsg['dataLang'] = array('valid' => true, 'level' => 2);
                else
                    $alertMsg['dataLang'] = array('valid' => false, 'level' => 1);
                $alertMsg['listSend'] = array('valid' => false, 'level' => 1);
            }

        }
        elseif (!empty($data))
        {
            if (empty($data['buildingsId']))
                $alertMsg['building'] = array('valid' => false, 'level' => 1);
            if (empty($data['emailList']))
                $alertMsg['recipient'] = array('valid' => false, 'level' => 1);
        }

        $this->setAlertMsg($alertMsg);
    }

    protected function _setDetails($id, $imageSrc = '', $page = 1)
    {
//        $this->view->headLink()->offsetUnset(0);
        $this->view->headLink()->appendStylesheet($this->view->locateFile('site.css', null, 'front'), 'all');
        $this->view->headLink()->offsetSetStylesheet(25, $this->view->locateFile('buildings.css', null, 'front'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('buildings.css', null, 'front'), 'all');
        $this->view->headLink()->offsetSetStylesheet(26, $this->view->locateFile('buildingDetails.css', null, 'front'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('buildingDetails.css', null, 'front'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('printPreview.css', null, 'front'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('print.css', null, 'front'), 'print');

        $session = new Zend_Session_Namespace();
        $session->parameters['lastSelected'] = $id;
        $this->view->id = $id;
        $this->view->emptyData = false;
        $this->view->backToSearch = false;
        $langId = Zend_Registry::get('languageID');
        Zend_Registry::set('currentEditLanguage', $langId);
        $this->_currentEditLanguage = $langId;
        $baseDir = $this->view->baseUrl() . "/";
        $cancelUrl = $baseDir
            . $this->_moduleTitle . "/"
            . $this->_name . "/"
            . $this->_currentAction . "/"
            . "actionKey/list/"
            . "page/" . $page . '/';

        $oDataName = $this->_objectList[$this->_actionKey];
        $oData = new $oDataName();

        // Get data details
        $data = $oData->populate($id, $langId);
        $oPremise = new PremisesObject();
        $premises = $oPremise->populate($id, $langId);

        if (!empty($data['B_ID']))
        {
            if (!empty($session->parameters['BFF_Number']))
                $this->view->backToSearch = true;

            $formPremises = new FormPremises(
                array(
                    'moduleName' => $this->_moduleTitle,
                    'baseDir' => $baseDir,
                    'cancelUrl' => $cancelUrl,
                    'imageSrc' => $this->_imageSrc,
                    'imgField' => '',
                    'mode' => $this->_actionKey,
                    'object' => $oPremise,
                    'dataId' => $id,
                    'isNewImage' => 'true'
                )
            );

            $this->view->locale = Cible_FunctionsGeneral::getLanguageSuffix($langId);
            // generate the form
            $form = new $this->_formName(
                    array(
                        'moduleName' => $this->_moduleTitle,
                        'baseDir' => $baseDir,
                        'cancelUrl' => $cancelUrl,
                        'imageSrc' => $imageSrc,
                        'imgField' => $this->_imageSrc,
                        'mode' => $this->_actionKey,
                        'object' => $oData,
                        'dataId' => $id,
                        'isNewImage' => 'true'
                    )
            );
            $form->populate($data);
            $elements = $form->getDisplayGroups();
            foreach ($elements as $key => $element)
            {
                $elems = $element->getElements();
                foreach ($elems as $elem)
                    $elem->setAttrib('disabled', 'disabled');

                $this->view->assign($key, $element);
            }

            $this->view->assign('form', $form);
            $this->_renderPremises($formPremises, $premises);
            // action
            if (isset($data['P_ID']))
                $this->view->firstId = $data['P_ID'];
        }
        else
            $this->view->emptyData = true;

    }
    private function _renderPremises(Zend_Form $form, array $data)
    {
        $formsToRender = array();
        foreach ($data as $pId => $premise )
        {
            if (!empty($premise['PI_Name']))
                $this->view->label = $premise['PI_Name'];
            else
                $this->view->label = $this->view->getCibleText('default_local_name');

            $form->populate($premise);
            $elements = $form->getDisplayGroups();
            foreach ($elements as $key => $element)
            {
                $elems = $element->getElements();
                foreach ($elems as $elem)
                    $elem->setAttrib('disabled', 'disabled');

                $this->view->assign($key, $element);
            }
            $render = $this->view->render('index/premisesPrint.phtml');
            $formsToRender[$pId] = $render;
        }

        $this->view->assign('formsToRender', $formsToRender);

    }

    public function traverseHierarchyAction()
    {
        $this->disableView();
//        $result = $this->traverseHierarchy();
        $this->importImages();
    }

    public function importImages($path = "")
    {
        $oData = new BuildingsObject();
        $data = $oData->getAll($this->_defaultEditLanguage);
        $config = Zend_Registry::get('config')->toArray();

        foreach ($data as $building)
        {
            $id = $building['B_ID'];
            $img = $building['B_Image'];

            if(empty($path))
                $path = $_SERVER['DOCUMENT_ROOT']
                    .Zend_Registry::get('www_root').'/data/images/'. $this->_moduleTitle .'/';
            if (!is_dir($path . $id))
            {
                mkdir ($path . $id);
                mkdir ($path . $id . '/tmp');
            }

            $srcOriginal = $path . $id . "/tmp/" . $img;
            $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
            $originalMaxWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
            $originalName = $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $img;

            $srcMedium = $path . $id . "/tmp/medium_" . $img;
            $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
            $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
            $mediumName = $mediumMaxWidth . 'x' . $mediumMaxHeight . '_' . $img;

            $srcThumb = $path . $id . "/tmp/thumb_" . $img;
            $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
            $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
            $thumbName = $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $img;
            copy($path . 'photos/' . $img, $path . $id . '/tmp/' . $img);
            copy($srcOriginal, $srcMedium);
            copy($srcOriginal, $srcThumb);
//
            Cible_FunctionsImageResampler::resampled(
                array(
                    'src' => $srcOriginal,
                    'maxWidth' => $originalMaxWidth,
                    'maxHeight' => $originalMaxHeight)
            );
            Cible_FunctionsImageResampler::resampled(
                array(
                    'src' => $srcMedium,
                    'maxWidth' => $mediumMaxWidth,
                    'maxHeight' => $mediumMaxHeight)
            );
            Cible_FunctionsImageResampler::resampled(
                array(
                    'src' => $srcThumb,
                    'maxWidth' => $thumbMaxWidth,
                    'maxHeight' => $thumbMaxHeight)
            );

            rename($srcOriginal, $path
                . $id . "/" . $originalName);
            rename($srcMedium, $path
                . $id . "/" . $mediumName);
            rename($srcThumb, $path
                . $id . "/" . $thumbName);
        }

    }

    public function traverseHierarchy($path = "", $maxWidth = 170, $maxHeight = 170)
    {
        if(empty($path))
        $path = $_SERVER['DOCUMENT_ROOT']
            .Zend_Registry::get('www_root').'/data/images/'. $this->_moduleTitle .'/';

        $returnArray = array();
        $tmp = array();
        $dir = opendir($path);

        while (($file = readdir($dir)) !== false)
        {
            if ($file[0] == '.' || $file == 'tmp')
                continue;

            $fullPath = $path . '/'. $file;
            //Trouver la plus grande image.
            if (!is_dir($fullPath))
            {
                $folder = dirname($fullPath);
                $tmp = explode('_', $file);
                $dim = explode('x', $tmp[0]);
                unset($tmp[0]);
                $prodId = substr($folder,(strrpos($folder, '/') + 1), 3 );
                if ($pid != $prodId)
                {
                    $pid = $prodId;
                    $dt1 = filemtime($fullPath);
                    $data = array(
                        'P_Photo' => implode('_',$tmp)
                    );

                }
                else
                {
                    $dt2 = filemtime($fullPath);
                    if($dt1 < $dt2)
                        $data = array(
                            'P_Photo' => implode('_',$tmp)
                        );
                }

                 $oProduct = new ProductsObject();
                $oProduct->save($prodId, $data, 1);
                //construire le nom le chemin de l'image
//                if ($dim[0] > $maxWidth && $dim[1] > $maxHeight)
//                {
//                    $srcThumb = $path . '/' . $maxWidth.'x'.$maxHeight.'_'.$tmp[1];
//                    //remplir le tableau image
//                    $image = array(
//                        'src'       => $srcThumb,
//                        'maxWidth'  => $maxWidth,
//                        'maxHeight' => $maxHeight
//                    );
//                    copy($fullPath, $srcThumb);
//                    resampled($image);
//                }
            }
            else // your if goes here: if(substr($file, -3) == "jpg") or something like that
                $this->traverseHierarchy($fullPath);
        }

    }

}