<?php


class Banners_IndexController extends Cible_Controller_Categorie_Action
{
    protected $_labelSuffix;
    protected $_colTitle      = array();
    protected $_moduleID      = 18;
    protected $_defaultAction = '';
    protected $_defaultRender = 'list';
    protected $_moduleTitle   = 'banners';
    protected $_name          = 'index';
    protected $_ID            = 'id';
    protected $_currentAction = '';
    protected $_actionKey     = '';
    protected $_imageSrc      = 'BII_Filename';
    protected $_formName   = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'list-group'  => 'GroupObject',
        'list-images' => 'BannerImageObject',
        'copy-large-to-crop' => 'BannerImageObject'

    );
    protected $_actionsList = array();

    protected $_disableExportToExcel = true;
    protected $_disableExportToPDF   = true;
    protected $_disableExportToCSV   = true;
    protected $_enablePrint          = false;
    protected $_filterData = array();
    protected $_cleanup = false;

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

    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function listGroupAction()
    {
       // echo "listGroup";
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true)){
            $this->_colTitle = array(
                'BG_Name'    => array('width' => '350px')
                );

          //  $this->_joinTables = array('ProductsObject');
          $this->_defaultAction = 'list-group';
            $this->_formName = 'FormGroup';
            $this->_redirectAction();
        }
    }

     public function listImagesAction($getParams = false){
         if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true)){
            $this->_colTitle = array(
                'BII_Filename'    => array('width' => '150px'),
                'BII_Text'    => array('width' => '150px'),
                'BG_Name'    => array('width' => '150px')
                );

            $oGroups = new GroupObject();
            $groups = $oGroups->getAll();
            foreach ($groups as $key => $value)
            {
                $filterData[$value['BG_ID']] = $value['BG_Name'];
            }
            $this->_filterData = array(
                'group-filter' => array(
                    'label' => "Groupe d'images",
                    'default_value' => null,
                    'associatedTo' => 'BI_GroupID',
                    'choices' => $filterData
                )
            );
            $this->_joinTables = array('GroupObject');
            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }
            $this->_defaultAction = 'list-images';
            $this->_formName = 'FormBannerImage';

            $this->view->showCrop = isset($this->_config->banners->active->crop) ?
                (bool)$this->_config->banners->active->crop : false;

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
        $oData     = new $oDataName();

        $this->_registry->currentEditLanguage = $this->_registry->languageID;
        $returnAction = $this->_getParam('return');

        $baseDir = $this->view->baseUrl() . "/";

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];

            $cancelUrl = $this->view->url(array(
                    'action' => $this->_defaultAction,
                    'actionKey' => null,
                    $this->_ID => null
                ));
            if ($returnAction)
                $returnUrl = $this->_moduleTitle . "/"
                        . $this->_name . "/"
                        . $returnAction;
            else
                $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_defaultAction,
                        'actionKey' => null,
                        $this->_ID => null
                    )));

            $groupId = $this->_getParam('group-filter');
            // generate the form
            $options = array(
                        'baseDir'    => $baseDir,
                        'cancelUrl'  => $cancelUrl,
                        'moduleName' => $this->_moduleTitle,
                        'imageSrc'   => $imageSrc,
                        'imgField'   => $this->_imageSrc,
                        'dataId'     => '',
                        'addAction'  => '1',
                'isNewImage' => $isNewImage,
                'showCrop'   => $this->view->showCrop
            );
            if (!empty($groupId))
                $options['groupId'] = $groupId;

            $form = new $this->_formName($options);
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $formData = $this->_mergeFormData($formData);
                    $recordID = $oData->insert($formData, $this->getDefaultEditLanguage());

                    /* IMAGES */
                    if (!is_dir($this->_imagesFolder . $recordID))
                    {
                        mkdir($this->_imagesFolder . $recordID)
                            or die("Could not make directory");
                        mkdir($this->_imagesFolder . $recordID . "/tmp")
                            or die("Could not make directory");
                    }

                    $this->_setImage($this->_imageSrc, $formData, $recordID);

                    // redirect
                    if (!isset($formData['submitSaveClose']))
                    {
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                    'actionKey' => 'edit',
                                    $this->_ID => $recordID
                                )))
                        );
                    }

                    $this->_redirect($returnUrl);
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
        $this->_editMode = true;
        $imageSrc = "";
        $id       = (int) $this->_getParam($this->_ID);
        $page     = (int) $this->_getParam('page');
        $this->view->id = $id;
        $baseDir      = $this->view->baseUrl() . "/";
        $cancelUrl = $this->view->url(array(
                    'action' => $this->_defaultAction,
                    'actionKey' => null,
                    $this->_ID => null
                ));
        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                    'actionKey' => null,
                    $this->_ID => null
                )));

        $oDataName = $this->_objectList[$this->_currentAction];

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $oData = new $oDataName();
            // Get data details
            $data = $oData->populate($id, $this->_currentEditLanguage);

            // image src.
            $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id);
            $isNewImage = $imageSource['isNewImage'];

            $groupId = $this->_getParam('group-filter');
            // generate the form
            $options = array(
                'moduleName' => $this->_moduleTitle,
                'baseDir'    => $baseDir,
                'cancelUrl'  => $cancelUrl,
                'imageSrc'   => $imageSource['imageSrc'],
                'imgField'   => $this->_imageSrc,
                'dataId'     => $id,
                'data'       => $data,
                'isNewImage' => $imageSource['isNewImage'],
                'showCrop'   => $this->view->showCrop
            );
            if ($this->view->showCrop && isset($imageSource['cropSrc'])){
                $options['cropSrc'] = $imageSource['cropSrc'];
            }
            if (!empty($groupId))
                $options['groupId'] = $groupId;

            $form = new $this->_formName($options);
            $this->view->form = $form;
            // action
            if (!$this->_request->isPost())
                $form->populate($data);
            else
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $formData = $this->_mergeFormData($formData);
                    if ($formData[$this->_imageSrc] <> '' && $isNewImage){
                        $this->_setImage($this->_imageSrc, $formData, $id);
                    }

                    $oData->save($id, $formData, $this->_currentEditLanguage);
                    // redirect
                    if (!isset($formData['submitSaveClose']))
                    {
                        $this->_redirect(str_replace($this->view->baseUrl(), '',
                            $this->view->url(array(
                                    'actionKey' => 'edit',
                                    $this->_ID => $id,
                                    "lang" => $this->languageSuffix
                                )))
                        );
                    }

                    $this->_redirect($returnUrl);
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

        $this->view->return = $this->view->baseUrl() . "/"
                . $this->_moduleTitle . "/"
                . $this->_name . "/"
                . $this->_currentAction . "/"
                . "page/" . $page;

        $this->view->action = $this->_currentAction;

        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_defaultAction,
                        'actionKey' => null,
                        $this->_ID => null
                    )));

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if (Cible_ACL::hasAccess($page))
        {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $id > 0)
                {
                    $oData->delete($id);
                    Cible_FunctionsGeneral::delFolder($this->_imagesFolder . $id);
                    // update the page associate to this group of banner image
                    $db = $this->_db;
                    $db->update('Pages', array('P_BannerGroupID'=> ''), 'P_BannerGroupID = '. $id);
                    if(class_exists('Catalog_CategoriesData'))
                        $db->update('Catalog_CategoriesData', array('C_BannerGroupID'=> ''), 'C_BannerGroupID = '. $id);
                    if(class_exists('Catalog_SousCategoriesData'))
                        $db->update('Catalog_SousCategoriesData', array('SC_BannerGroupID'=> ''), 'SC_BannerGroupID = '. $id);

                }

                $this->_redirect($returnUrl);
            }
            elseif ($id > 0)
            {        $returnAction = $this->_getParam('return');

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/index/" . $returnAction;
        else
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "page/" . $page;
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

        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
        // Assign a the view for rendering
        $this->_helper->viewRenderer->setRender($this->_defaultRender);
        //Assign to the render the list created previously.
        $this->view->assign('mylist', $mylist);
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

        $dispatcher = $this->getFrontController()->getDispatcher();
        $actionName = $dispatcher->formatActionName($this->_actionKey);
        $params     = $this->$actionName(true);
        $oDataName  = $this->_objectList[$this->_actionKey];
        $lines      = new $oDataName();
        $foreignKey = $lines->getForeignKey();

        $params['foreignKey'] = $foreignKey;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $this->fields  = $this->_colTitle;
        $this->filters = array();

        $langId = $this->_defaultEditLanguage;

        $select = $lines->getAll($langId, false);
        $this->select = $this->_addJoinQuery($select, $params);

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
        $filter = '';
        $filter = $this->_getParam('group-filter');

        foreach ($this->_actionsList as $key => $controls)
        {
            foreach ($controls as $key => $action)
            {
                //Redirect to the real action to process If no actionKey = list page.
                switch ($action)
                {
                    case 'add':
                        $urlOptions = array(
                                                'controller' => $this->_name,
                                                'action' => $this->_currentAction,
                            'actionKey' => 'add');
                        if (!empty($filter))
                        {
                            $urlOptions['group-filter'] = $filter;
                        }
                        $commands = array(
                            $this->view->link($this->view->url($urlOptions),
                                    $this->view->getCibleText('button_add_' . $this->_labelSuffix),
                                    array('class' => 'action_submit add'))
                        );
                        break;

                    case 'edit':
                        $url = $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/edit/"
                            . $this->_ID . "/%ID%/page/" . $page;
                        if (!empty($filter))
                        {
                            $url .= '/group-filter/' . $filter;
                        }
                        $edit = array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $url,
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
}
