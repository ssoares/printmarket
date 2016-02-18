<?php
/**
* Data management for the references values
*
* PHP versions 5
*
* LICENSE:
*
* @category   Controller
* @package    Default
* @author     ssoares <sergio.soares@ciblesolutions.com>
* @copyright  2010 CIBLE Solutions d'Affaires
* @license    http://www.ciblesolutions.com
* @version    CVS: <?php $ ?> Id:$
*/
class Order_ParametersController extends Cible_Controller_Block_Abstract
{
    protected $_labelSuffix;
    protected $_colTitle      = array();
    protected $_moduleID      = 14;
    protected $_defaultAction = 'list';
    protected $_defaultRender = 'list';
    protected $_moduleTitle   = 'catalog';
    protected $_name          = 'parameters';
    protected $_ID            = 'id';
    protected $_currentAction = '';
    protected $_actionKey     = '';
    protected $_imageSrc      = '';

    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName   = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'parameters' => 'OrderParametersObject'
    );
    protected $_actionsList = array();

    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF   = false;
    protected $_disableExportToCSV   = false;
    protected $_enablePrint          = false;
    protected $_constraint;
    protected $_filterData = array();
    protected $_editMode = false;
    protected $_session;
    protected $_renderPartial = '';
    protected $_addSubFolder = true;
    protected $_whereClause;
    protected $_associationIds = array();
    protected $_getActionParams = false;
    protected $_addActions = array();

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init()
    {
        // Sets the called action name. This will be dispatched to the method
        $this->_currentAction = $this->_getParam('action');

        parent::init();
        // The action (process) to do for the selected object
        $this->_actionKey = $this->_getParam('actionKey');

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);

    }

    /**
     * Dispatches actions for the references.
     *
     * @access public
     *
     * @return void
     */
    public function parametersAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
                $this->_colTitle = array(
                'CP_ShippingFees'      => array('width' => '50px'),
                'CP_ShippingFeesLimit' => array('width' => '50px'),
//                'CP_MontantFraisCOD'  => array('width' => '50px'),
                'CP_AdminOrdersEmail'  => array('width' => '50px'),
//                'II_Name'  => array('width' => '50px'),
//                'CP_FreeMiniAmount'  => array('width' => '50px'),
//                'CP_BonusPointDollar'  => array('width' => '50px'),
//                'CP_TauxTaxeFed'  => array('width' => '50px')
                );

            $this->_joinTables = array();

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormParameters';
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
        $returnAction = $this->_getParam('return');
        $baseDir = $this->view->baseUrl() . "/";
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();
        $this->_registry->currentEditLanguage = $this->_registry->languageID;
        $cancelUrl = $this->view->url(array(
            'action' => $this->_currentAction,
            'actionKey' => null,
            $this->_ID => null
        ));
        $imageSrc = '';
        $isNewImage = false;
        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
        else
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_currentAction,
                'actionKey' => null,
                $this->_ID => null
            )));

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            if (!empty($this->_associationIds))
            {
                $nameAssoc = $this->_objectList[$this->_associationIds[0]];
                $assocObj = new $nameAssoc();
                $this->view->data = $assocObj->getList(true, null, true);
            }
            if ($this->_getMoreImages)
            {
                $this->view->assign('moreImg', json_encode(array()));
                $this->view->assign('imagesPath', '');
            }
            if (!empty($this->_imageSrc))
            {
                $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null);
                $imageSrc = $imageSource['imageSrc'];
                $isNewImage = $imageSource['isNewImage'];
                $imgBasePath = $imageSource['imgBasePath'];
                $nameSize = $imageSource['nameSize'];
            }
            // generate the form
            $options = array(
                'moduleName' => $this->_moduleTitle,
                'moduleID' => $this->_moduleID,
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'addAction' => true,
                'imageSrc' => $imageSrc,
                'imgField' => $this->_imageSrc,
                'imgBasePath' => $imgBasePath,
                'nameSize' => $nameSize,
                'dataId'     => '',
                'object'     => $oData,
                'id' => $this->_currentAction,
                'isNewImage' => $isNewImage
            );

            $form = new $this->_formName($options);
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $moreImages = array();
                $formData = $this->_request->getPost();
                if (isset($formData['moreImg']))
                {
                    $moreImages = $formData['moreImg'];
//                    unset($formData['moreImg']);
                }

                if ($form->isValid($formData))
                {
//                    $formData = $this->_mergeFormData($formData);
                    $recordID = $oData->insert($formData, $this->_defaultEditLanguage);
                    /* IMAGES */
                    if (!empty($this->_imageSrc) && !is_dir($this->_imagesFolder . $recordID))
                    {
                        mkdir($this->_imagesFolder . $recordID)
                            or die("Could not make directory");
                        mkdir($this->_imagesFolder . $recordID . "/tmp")
                            or die("Could not make directory");
                    }

                    // Process for additionnal images
                    if (!empty($moreImages))
                        $this->_saveImgData($moreImages, $recordID);
                    // Save image
                    $this->_setImage($this->_imageSrc, $formData, $recordID);
                    if (!empty($this->_imagesLst))
                    {
                        foreach ($this->_imagesLst as $src)
                        {
                            if (!empty($formData[$src]))
                                $this->_setImage($src, $formData, $recordID);
                        }
                    }
                    // redirect
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                'actionKey' => 'edit',
                                $this->_ID => $recordID
                            )))
                        );
                }
                else
                {
                    if (!empty($this->_associationIds))
                    {
                        $key = $this->_associationIds[0] . 'Set';
                        if (array_key_exists($key, $formData))
                            $this->view->assign('related', $formData[$key]);
                        else
                            $this->view->assign('related', array());
                    }
                    if ($this->_getMoreImages)
                        $this->view->assign('moreImg', json_encode($moreImages));
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
        $isNewImage = false;
        $id = (int) $this->_getParam($this->_ID);
        $page = (int) $this->_getParam('page');

        $baseDir = $this->view->baseUrl() . "/";
        $returnAction = $this->_getParam('return');
        $cancelUrl = $this->view->url(array(
                    'action' => $this->_currentAction,
                    'actionKey' => null,
                    $this->_ID => null
                ));

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
        else
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_currentAction,
                'actionKey' => null,
                $this->_ID => null
                )));

        $oDataName = $this->_objectList[$this->_currentAction];

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_editMode = true;
            $this->_cleanup = false;
            $oData = new $oDataName();
            // Get data details
            $data = $oData->populate($id, $this->_currentEditLanguage);
            if (!empty($this->_associationIds))
            {
                $nameAssoc = $this->_objectList[$this->_associationIds[0]];
                $assocObj = new $nameAssoc();
                $this->view->data = $assocObj->getList(false, null, true);
            }

            // image src.
            $config = Zend_Registry::get('config')->toArray();
            $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
            $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
            $this->view->assign('imagesPath', $this->_rootImgPath . '/' . $id . '/' . $thumbMaxWidth . 'x' . $thumbMaxHeight . '_');
            if (!empty($this->_imageSrc))
            {
                $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id);
                $imageSrc = $imageSource['imageSrc'];
                $isNewImage = $imageSource['isNewImage'];
                $imgBasePath = $imageSource['imgBasePath'];
                $nameSize = $imageSource['nameSize'];
            }

            // generate the form
            $options = array(
                'moduleName' => $this->_moduleTitle,
                'moduleID' => $this->_moduleID,
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'imageSrc' => $imageSrc,
                'imgField' => $this->_imageSrc,
                'imgBasePath' => $imgBasePath,
                'nameSize' => $nameSize,
                'object'   => $oData,
                'dataId'   => $this->_setId ? $data[$oData->getForeignKey()]:$id,
                'id' => $this->_currentAction,
                'isNewImage' => $isNewImage
            );

            $form = new $this->_formName($options);
            $this->view->form = $form;
            $moreImages = array();

            // action
            if (!$this->_request->isPost())
            {
                if ($this->_getMoreImages)
                {
                    $moreImages = $oData->getRelatedImages($id);
                    $this->view->assign('moreImg', json_encode($moreImages));
                }
                if (!empty($this->_associationIds))
                    $this->view->related = $oData->getAssociations($this->_associationIds[0], $id, $this->_defaultEditLanguage);
                $form->populate($data);
            }
            else
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    if (isset($formData['moreImg']))
                        $moreImg = $formData['moreImg'];
                    if (!empty($moreImg))
                        $this->_saveImgData($moreImg, $id);

                    if (isset($formData[$this->_imageSrc]) && $formData[$this->_imageSrc] <> '' && $isNewImage)
                        $this->_setImage($this->_imageSrc, $formData, $id);
//                    elseif(empty($formData[$this->_imageSrc]))
//                    {
//                        $this->_cleanup = true;
//                        $this->_setImage($this->_imageSrc, $formData, $id);
//                    }
                    if (!empty($this->_imagesLst))
                    {
                        foreach ($this->_imagesLst as $src)
                        {
                            if (!empty($formData[$src]))
                            {
                                $imageSource = $this->_setImageSrc($data, $src, $id);
                                $imageSrc = $imageSource['imageSrc'];
                                $isNewImage = $imageSource['isNewImage'];
                                if ($isNewImage)
                                    $this->_setImage($src, $formData, $id);
                            }
                        }
                    }
                    $oData->save($id, $formData, $this->getCurrentEditLanguage());
                    // redirect
                    if (!isset($formData['submitSaveClose']))
                    {
                        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                    'actionKey' => 'edit',
                                    $this->_ID => $id
                                )));
                    }

                    $this->_redirect($returnUrl);
                }
                else
                {
                    if (!empty($this->_associationIds))
                    {
                        $key = $this->_associationIds[0] . 'Set';
                        if (array_key_exists($key, $formData))
                            $this->view->assign('related', $formData[$key]);
                        else
                            $this->view->assign('related', array());
                    }

                    $form->populate($formData);
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

        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_currentAction,
                        'actionKey' => null,
                        $this->_ID => null
                    )));

        $this->view->assign(
                'return',
                $this->view->baseUrl() . "/" . $returnUrl
        );

        $this->view->action = $this->_currentAction;

        $returnAction = $this->_getParam('return');

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if (Cible_ACL::hasAccess($page))
        {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $id > 0)
                {
                    $tmp = $oData->delete($id);
                    Cible_FunctionsGeneral::delFolder($this->_imagesFolder . $id);
                    if (!empty($this->_deleteFolder))
                        Cible_FunctionsGeneral::delFolder($this->_deleteFolder . $id);

                    $this->_redirect($returnUrl);
                }

                $this->_redirect($returnUrl);
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
        $foreignKey = $oData->getForeignKey();
        if (empty($foreignKey))
            $foreignKey = $oData->getDataId();
        $params = array('foreignKey' => $foreignKey);

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
        $this->type = 'CSV';
        $this->filename = $this->_actionKey . '.csv';

        $dispatcher = $this->getFrontController()->getDispatcher();
        $actionName = $dispatcher->formatActionName($this->_actionKey);
        $params = $this->$actionName(true);
        $oDataName = $this->_objectList[$this->_actionKey];
        $lines = new $oDataName();
        $foreignKey = $lines->getForeignKey();

        $params['foreignKey'] = $foreignKey;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $this->fields = $this->_colTitle;
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
        {
            $this->_actionsList = array(

                array('commands' => ''),
                array('action_panel' => 'edit', 'delete')
            );
            if (!empty($this->_addActions)){
                $this->_actionsList[1] = array_merge($this->_actionsList[1], $this->_addActions);
            }

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
                                $this->view->link($this->view->url(
                                    array(
                                        'controller' => $this->_name,
                                        'action' => $this->_currentAction,
                                        'actionKey' => 'add')),
                                    $this->view->getCibleText('button_add'),
                                    array('class' => 'action_submit add'))
                            );

                            break;

                        case 'edit':
                            $url = $this->view->url(array(
                                'action' => $this->_currentAction,
                                'actionKey' => 'edit',
                                $this->_ID => 'xIDx'

                            ));

                            $edit = array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => $url,
                                'findReplace' => array(
                                    array(
                                        'search' => 'xIDx',
                                        'replace' => $tabId
                                    )
                                ),
                                'returnUrl' => $this->view->Url() . "/"
                            );
                            $actions['edit'] = $edit;
                            break;

                        case 'delete':
                            $url = $this->view->url(array(
                                'action' => $this->_currentAction,
                                'actionKey' => 'delete',
                                $this->_ID => 'xIDx'

                            ));
                            $delete = array(
                                'label' => $this->view->getCibleText('button_delete'),
                                'url' => $url,
                                'findReplace' => array(
                                    array(
                                        'search' => 'xIDx',
                                        'replace' => $tabId
                                    )
                                )
                                );

                            $actions['delete'] = $delete;
                            break;

                        case 'log':
                            $url = $this->view->url(array(
                                'action' => $this->_currentAction,
                                'actionKey' => 'log',
                                $this->_ID => 'xIDx'

                            ));

                            $log = array(
                                'label' => $this->view->getCibleText('button_log'),
                                'url' => $url,
                                'findReplace' => array(
                                    array(
                                        'search' => 'xIDx',
                                        'replace' => $tabId
                                    )
                                ),
                                'returnUrl' => $this->view->Url() . "/"
                            );
                            $actions['log'] = $log;
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
            if (!empty($this->_filterData))
                $options['filters'] = $this->_filterData;
            if ($this->_renderPartial)
                $options['renderPartial'] = $this->_renderPartial;

            $options['actionKey'] = $this->_currentAction;
        }
        else
            $options = $this->_actionsList;

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
            if(is_array($data))
            {
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
            // Loop on tables list(given by object class) to build the query
            foreach ($this->_joinTables as $key => $object)
            {
                if (is_array($object))
                {
                    $objName = $object['obj'];
                    // Get the constraint attribute = foreign key to link tables.
                    $foreignKey = $object['foreignKey'];
                    $dataOnly = isset($object['dataOnly']) ? $object['dataOnly'] : false;
                }
                else
                {
                    $foreignKey = $params['foreignKey'];
                    $objName = $object;
                    $dataOnly = isset($params['dataOnly']) ? $params['dataOnly'] : false;
                }
                //Create an object and fetch data from object.
                $tmpObject = new $objName();
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
                            $tmpIndexTable, $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                            $tmpIndexTable . '.' . $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    $select->joinLeft(
                        $tmpDataTable, $tmpDataId . ' = ' . $foreignKey);

                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                            $tmpIndexTable,
                            $tmpDataId . ' = ' . $tmpIndexId);

                        $select->where(
                            $tmpIndexTable . '.' . $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

}