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
class Utilities_ReferencesController extends Cible_Controller_Block_Abstract
{

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 0;
    protected $_defaultAction = 'list';
    protected $_moduleTitle = 'utilities';
    protected $_name = 'references';
    protected $_ID = 'id';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_imageSrc = '';
    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'references' => 'ReferencesObject',
        'list-values' => 'ReferencesObject'
    );
    protected $_actionsList = array();
    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF = false;
    protected $_disableExportToCSV = false;
    protected $_enablePrint = false;
    protected $_setFilters = false;
    protected $_filterParams = array();
    protected $_langId = null;

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

        if (isset($this->_objectList[$this->_currentAction]))
            $this->_imageFolder = $dataImagePath
                . $this->_moduleTitle . "/"
                . $this->_objectList[$this->_currentAction] . "/";

        if (isset($this->_objectList[$this->_currentAction]))
            $this->_rootImgPath = Zend_Registry::get("www_root")
                . "/data/images/"
                . $this->_moduleTitle . "/"
                . $this->_objectList[$this->_currentAction] . "/";
    }

    /**
     * Dispatches actions to list values of the reference.
     *
     * @access public
     *
     * @return void
     */
    public function listValuesAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_colTitle = array(
                'R_ID' => array('width' => '150px'),
                'R_Seq' => array('width' => '150px'),
                'R_TypeRef' => array('width' => '150px'),
                'RI_Value' => array('width' => '150px')
            );
            $lang = $this->_getParam('lang');

            if (!$lang)
            {
                $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
                $this->_langId = $this->_defaultEditLanguage;
            }
            else
            {
                $this->_langId = Cible_FunctionsGeneral::getLanguageID($lang);
                $this->_registry->currentEditLanguage = $this->_langId;
            }
            if (!$this->_request->isPost())
            {
                $typeRef = $this->_getParam('typeRef');
                $oRef = new ReferencesObject();
                $langs = Cible_FunctionsGeneral::getAllLanguage(true);

                foreach ($langs as $language)
                    $data[$language['L_ID']] = $oRef->getRefByType($typeRef, $language['L_ID']);
                $tmpDataCurrentLang = $data[$this->_defaultEditLanguage];
                unset($data[$this->_defaultEditLanguage]);
                $dataCurrentLang = array();
                foreach ($tmpDataCurrentLang as $key => $tmpData)
                {
                    $lg = Cible_FunctionsGeneral::getLanguageSuffix($tmpData['RI_LanguageID']);
                    $tmpData['RI_Value_' . $lg] = $tmpData['RI_Value'];
                    $dataCurrentLang[$tmpData['R_ID']] = $tmpData;
                }
                $orderBySeq = $oRef->getOrderBySeq();
                $chkboxList = $oRef->getChkboxList();
                if (in_array($typeRef, $chkboxList))
                    $this->view->assign('addChkbox', true);
                $this->view->assign('orderBySeq', $orderBySeq);
                $this->view->assign('currentLang', $this->_defaultEditLanguage);
                $id = 0;
                $prev = 0;
                foreach ($data as $dataLang)
                {
//                    if ($value['R_ID'] == $id)
//                    {
                    foreach ($dataLang as $key => $value)
                    {
                        $lg = Cible_FunctionsGeneral::getLanguageSuffix($value['RI_LanguageID']);
                        if (array_key_exists($value['R_ID'], $dataCurrentLang))
                            $dataCurrentLang[$value['R_ID']]['RI_Value_' . $lg] = $value['RI_Value'];
                    }
                }

                $this->view->assign('list', $dataCurrentLang);
            }
            else
                $this->disableView();

            if ($getParams)
            {
                $params = array(
                    'columns' => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }
            $this->_formName = 'FormReferences';

            $this->_defaultAction = $this->_currentAction;
            $this->_redirectAction();
            $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
        }
    }

    /**
     * Dispatches actions for the references.
     *
     * @access public
     *
     * @return void
     */
    public function referencesAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_colTitle = array(
                'R_ID' => array('width' => '150px'),
                'R_Seq' => array('width' => '150px'),
                'R_TypeRef' => array(
                    'width' => '150px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'form_enum_')),
                'RI_Value' => array('width' => '150px')
            );

            $this->_setFilters = true;
            $oRef = new ReferencesObject();
            $data = $oRef->getAll($this->_defaultEditLanguage);
            $choices = array();
            $choices[''] = $this->view->getCibleText('filter_empty_label');
            foreach ($data as $ref)
            {
                if (!empty($ref['R_TypeRef']))
                    $choices[$ref['R_TypeRef']] = $this->view->getCibleText('form_enum_' . $ref['R_TypeRef']);
            }
            $this->_filterParams = array(
                'references-status-filter' => array(
                    'label' => 'Filtre 1',
                    'default_value' => null,
                    'associatedTo' => 'R_TypeRef',
                    'choices' => array_unique($choices)
                )
            );
            if ($getParams)
            {
                $params = array(
                    'columns' => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }
            $this->_formName = 'FormReferences';
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

        $this->_registry->currentEditLanguage = $this->_registry->languageID;

        $returnAction = $this->_getParam('return');

        $baseDir = $this->view->baseUrl() . "/";

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
                    . $returnAction;
            else
                $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                    'action' => $this->_currentAction,
                    'actionKey' => null,
                    $this->_ID => null
                )));

            // generate the form
            $form = new $this->_formName(array(
                'baseDir' => $baseDir,
                'cancelUrl' => "$baseDir$returnUrl",
                'moduleName' => $this->_moduleTitle . "/"
                . $this->_objectList[$this->_currentAction],
                'imageSrc' => $imageSrc,
                'imgField' => $this->_imageSrc,
                'dataId' => '',
                'isNewImage' => true
                ));

            $this->view->form = $form;

            // action
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

//                $formData['LANGUAGE'] = $this->getCurrentEditLanguage();

                if ($form->isValid($formData))
                {
                    $formData = $this->_mergeFormData($formData);
                    $recordID = $oData->insert($formData, $this->_defaultEditLanguage);

                    // redirect
                    if (!$this->_isXmlHttpRequest)
                    {
                        if (!isset($formData['submitSaveClose']))
                        {
                            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                        'actionKey' => 'edit',
                                        $this->_ID => $recordID
                                    )));
                        }
                        $this->_redirect($returnUrl);
                    }
                    else
                        echo $recordID;
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

        $baseDir = $this->view->baseUrl() . "/";
        $returnAction = $this->_getParam('return');
        $cancelUrl = $this->view->url(array(
                    'action' => $this->_currentAction,
                    'actionKey' => null,
                    $this->_ID => null
                ));

        $oDataName = $this->_objectList[$this->_currentAction];

        $oData = new $oDataName();


        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_currentAction,
                'actionKey' => null,
                $this->_ID => null
                )));


            // Get data details
            $data = $oData->populate($id, $this->_currentEditLanguage);

            // generate the form
            $form = new $this->_formName(
                array(
                'moduleName' => $this->_moduleTitle . "/"
                . $this->_objectList[$this->_currentAction],
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'imageSrc' => $imageSrc,
                'imgField' => $this->_imageSrc,
                'dataId' => $id,
                'data' => $data,
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
                    $formData = $this->_mergeFormData($formData);
                    $langId = !empty($this->_langId) ? $this->_langId : $this->getCurrentEditLanguage();
                    $oData->save($id, $formData, $langId);
                    // redirect
                    if (!$this->_isXmlHttpRequest)
                    {
                        if (!isset($formData['submitSaveClose']))
                        {
                            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                        'actionKey' => 'edit',
                                        $this->_ID => $id
                                    )));
                        }

                        $this->_redirect($returnUrl);
                    }
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
        $deleted = true;
        $page = (int) $this->_getParam('page');
        $blockId = (int) $this->_getParam('blockID');
        $id = (int) $this->_getParam($this->_ID);
        $force = (bool) $this->_getParam('force');

        $returnUrl = str_replace(
            $this->view->baseUrl(),
            '',
            $this->view->url(
                array(
                    'action' => $this->_currentAction,
                    'actionKey' => null,
                    $this->_ID => null
                )));

        $this->view->assign('return',$this->view->baseUrl() . "/" . $returnUrl);

        $this->view->action = $this->_currentAction;

        $returnAction = $this->_getParam('return');

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if (Cible_ACL::hasAccess($page))
        {
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                $isFree = $oData->isInUse($id, $this->_defaultEditLanguage);
                if ($del && $id > 0 && ($isFree || $force))
                {
                    $oData->delete($id);
                }
                else
                    $deleted = false;

                if ($this->_isXmlHttpRequest)
                {
                    echo json_encode($deleted);
                }
                else
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
     * Return the values of the list to update the dropdown
     * @access public
     * @return void
     */
    public function rebuildAction()
    {
        $this->disableView();
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            foreach ($this->view->list as $key => $value)
            {
                $data[$value['R_ID']] = $value['RI_Value'];
            }
            echo json_encode($data);
        }
    }

    /**
     * Update the sequences.
     *
     * @access public
     *
     * @return void
     */
    public function editPosAction()
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->disableView();
            $seq = $this->_getParam('data');
            $oDataName = $this->_objectList[$this->_currentAction];
            $oData = new $oDataName();
            foreach ($seq as $key => $value)
                $oData->save($value['R_ID'], $value, $this->getCurrentEditLanguage());
        }
    }

    /**
     * Retrieve the label of a value list according to the key parameter.
     *
     * @access public
     *
     * @return string
     */
    public function getLabelAction()
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->disableView();
            $key = $this->_getParam('typeRef');
            $label = $this->view->getCibleText('form_enum_' . $key, $this->_defaultEditLanguage);

            if ($this->_isXmlHttpRequest)
                echo json_encode($label);
            else
                return $label;
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
        $columnData = $oData->getDataColumns();
        $dataTable = $oData->getDataTableName();
        $indexTable = $oData->getIndexTableName();
        $columnIndex = $oData->getIndexColumns();
        $tabId = $oData->getDataId();
        //Set the tables from previous collected data
        $tables = array(
            $dataTable => $columnData,
            $indexTable => $columnIndex
        );
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($this->_defaultEditLanguage, false);
        $params = array('constraint' => $oData->getConstraint());
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
        $this->_helper->viewRenderer->setRender($this->_defaultAction);
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
            case 'rebuild':
                $this->rebuildAction();
                break;
            case 'edit-pos':
                $this->editPosAction();
                break;
            case 'get-label':
                $this->getLabelAction();
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
                                $this->view->getCibleText('button_add'),
                                array('class' => 'action_submit add'))
                            );
                        break;
                    case 'filter':
                        $filters = array();
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
        if ($this->_setFilters)
            $options['filters'] = $this->_filterParams;

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
            $constraint = $params['constraint'];
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
                    $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $constraint);
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                            $tmpIndexTable, $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                            $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                    /* If there's more than one table to link, store the current
                     * table name for the next loop
                     */
                    if (count($this->_joinTables) > 1)
                        $prevConstraint = $tmpObject->getConstraint();;
                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    $select->joinLeft(
                        $tmpDataTable, $prevConstraint . ' = ' . $tmpDataId);
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                            $tmpIndexTable, $constraint . ' = ' . $tmpIndexId);
                        $select->where(
                            $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

}