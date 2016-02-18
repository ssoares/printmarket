<?php

/**
 * Module Template
 * Controller for the backend administration.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: IndexController.php 426 2013-02-08 21:39:28Z ssoares $
 *
 */

/**
 * Manage actions for images associated with keywords and used as a gallery.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 */
class Template_IndexController extends Cible_Controller_Action {

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = XX;
    protected $_moduleTitle = 'template';
    protected $_defaultAction = 'template';
    protected $_defaultRender = 'template';
    protected $_name = 'index';
    protected $_ID = '';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_actionsList = array();
    protected $_imageSrc = '';
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'index' => 'TemplateObject',
    );
    protected $_session;
    protected $_renderPartial = '';
    protected $_lang;
    protected $_obj;
    protected $_whereClause;
    protected $_itemPerPage = 0;

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init() {
        parent::init();
        $this->_lang = Zend_Registry::get('languageID');
        // Sets the called action name. This will be dispatched to the method
        $this->_obj = new TemplateObject();
        $_blockID = $this->_request->getParam('BlockID');
        if (!empty($_blockID)) {
            $this->_obj->setBlockID($_blockID);
            $this->_obj->setBlockParams(array());
            $this->_currentAction = $this->_obj->getBlockParams('999');
            $this->_actionKey = $this->_currentAction;

            // set a partir des block params
            $this->_obj->getBlockParams('7');
        } else {
            // The action (process) to do for the selected object
            $this->_actionKey = $this->_getParam('actionKey');
            $this->_currentAction = $this->_getParam('actionKey');
        }

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('template.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('template.css'), 'all');

    }

    /**
     *
     *
     *
     *
     * @return void
     */
    public function templateAction()
    {

        $this->_redirectAction();
    }

    /**
     *
     * @return void
     */
    public function detailsAction() {
        $this->templateAction();
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
    private function _listAction($objectName) {
        $this->view->moduleName = $this->_moduleTitle;
        $page = $this->_getParam('page');
        if ($page == '')
            $page = 1;
        // Create the object from parameter
        if (get_class($this->_obj) != $objectName){
            $oData = new $objectName();
        }else{
            $oData = $this->_obj;
        }
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($this->_lang, false);
        $select->distinct();
        $params = array('foreignKey' => $oData->getForeignKey());

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;

        $mylist = New Cible_Paginator($select, $this->_tables, $field_list, $options);

        $mylist->setItemCountPerPage($this->_itemPerPage);

        // Assign a the view for rendering
        $this->_helper->viewRenderer->setRender($this->_currentAction);
        //Assign to the render the list created previously.
        $this->view->assign('mylist', $mylist);
    }

    /**
     * Format the current action name to bu used for label texts translations.
     *
     * @access private
     *
     * @return void
     */
    private function _formatName() {
        $this->_labelSuffix = str_replace(array('/', '-'), '_', $this->_currentAction);
    }

    /**
     * Reditects the current action to the "real" action to process.
     *
     * @access public
     *
     * @return void
     */
    private function _redirectAction() {
        //Redirect to the real action to process If no actionKey = list page.

        switch ($this->_actionKey) {
            case 'details':
                $this->_listAction($this->_objectList[$this->_currentAction]);
                $this->_helper->viewRenderer->setRender('details');
                break;
            default:
                $this->_listAction($this->_objectList[$this->_currentAction]);
                break;
        }
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
                    if (!empty($tmpIndexTable) && !$dataOnly)
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

                    if (!empty($tmpIndexTable) && !$dataOnly)
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
