<?php

/**
 * Module News
 * Controller for the backend administration.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_News
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
 * @package   Extranet_Module_News
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 */
class Imageslibrary_FrontController extends Cible_Controller_Action {

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 24;
    protected $_moduleTitle = 'imageslibrary';
    protected $_defaultAction = 'imageslibrary';
    protected $_defaultRender = 'imageslibrary';
    protected $_name = 'front';
    protected $_ID = 'id';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_actionsList = array();
    protected $_imageSrc = 'IL_Filename';
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'list' => 'ImageslibraryObject',
    );
    protected $_session;
    protected $_renderPartial = 'partials/generic.front.list.phtml';
    protected $_lang;
    protected $_obj;
    protected $_whereClause;
    protected $_itemPerPage = 0;
    protected $_tables = array();

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
        Zend_Registry::set('currentEditLanguage', $this->_lang);
        // Sets the called action name. This will be dispatched to the method
        $this->_obj = new ImageslibraryObject();
        $_blockID = $this->_request->getParam('BlockID');
        if (!empty($_blockID)) {
            $this->_obj->setBlockID($_blockID);
            $this->_obj->setBlockParams(array());
            $this->_currentAction = $this->_obj->getBlockParams('999');
            $this->_actionKey = $this->_currentAction;
        } else {
            // The action (process) to do for the selected object
            $this->_actionKey = $this->_getParam('actionKey');
            $this->_currentAction = $this->_getParam('currentAction');
            if ($this->_getParam('forceBlock')){
                $this->_obj->setBlockParams($this->_getParam('parameters'));
            }
        }

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('imageslibrary.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('imageslibrary.css'), 'all');

    }

    /**
     *
     * @return void
     */
    public function listAction()
    {
        $this->_renderPartial = 'partials/imgManageFrontList.phtml';

        $this->_colTitle = array(
        'idField' => 'IL_ID',
        'filenameField' => $this->_imageSrc,
        'format' => 'thumbList'
        );
        $this->_joinTables = array(
            array('obj' => 'ImageslibraryKeywordsObject', 'foreignKey' => 'IL_ID', 'dataOnly' => true),
            array('obj' => 'TeamsObject', 'foreignKey' => 'TED_ImgLibId = ILK_RefId', 'dataOnly' => true),
            );
        $tmp = $this->_obj->getIndexColumns();
        $this->_obj->setIndexSelectColumns(array_combine(array_keys(array_flip($tmp)), $tmp));
        $this->_formName = 'FormImageslibrary';
        $this->_redirectAction();
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
        $objectName = $this->_objectList[$this->_currentAction];
        if (get_class($this->_obj) != $objectName){
            $oData = new $objectName();
        }else{
            $oData = $this->_obj;
        }
        $cancelUrl = $this->view->url(array(
            'action' => $this->_currentAction,
            'actionKey' => null,
            'team' => null,
            'category' => null,
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

        if (!empty($this->_associationIds))
        {
            foreach($this->_associationIds as $association){
                $nameAssoc = $this->_objectList[$association];
                $assocObj = new $nameAssoc();
                $tmpData[$association] = $assocObj->getList(true,null,true);
                $this->view->data = $tmpData;
            }
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
            'imgBasePath' => $imgBasePath,
            'nameSize' => $nameSize,
            'dataId'     => '',
            'object'     => $oData,
            'id' => $this->_currentAction,
            'isNewImage' => $isNewImage,
            'validationRequired' => $this->_getParam('validationRequired')
        );

        $form = new $this->_formName($options);
        $this->view->form = $form;
        if (!empty($this->_imagesLst)){
            $default = $this->_imgIndex;
            foreach($this->_imagesLst as $key => $value)
            {
                $this->_imgIndex = $key;
                $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null,'thumb');
                $imagePaths[$value] = $imageSource;
            }
            $form->setImagesLst($this->_imagesLst)
                ->setImagePaths($imagePaths);
            $this->_imgIndex = $default;
        }
        if ($this->_request->isPost())
        {
            $moreImages = array();
            $formData = $this->_request->getPost();
            if (isset($formData['moreImg'])){
                $moreImages = $formData['moreImg'];
            }

            if ($form->isValid($formData))
            {
                $formData['ILK_RefId'] = $this->_obj->getBlockParams(1);
                $recordID = $oData->insert($formData, $this->_defaultEditLanguage);
                /* IMAGES */
                if (!empty($this->_imageSrc) && !is_dir($this->_imagesFolder . $recordID))
                {
                    mkdir($this->_imagesFolder . $recordID)
                        or die("Could not make directory");
                    mkdir($this->_imagesFolder . $recordID . "/tmp")
                        or die("Could not make directory");
                }
                if (!empty($moreImages))
                    $this->_saveImgData($moreImages, $recordID);

                $this->_setImage($this->_imageSrc, $formData, $recordID);

                if (!empty($this->_imagesLst)){
                    foreach ($this->_imagesLst as $key => $src){
                        $this->_imgIndex = $key;
                        if (!empty($formData[$src])){
                            $this->_setImage($src, $formData, $recordID);
                        }
                    }
                }
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

                $form->populate($formData);

            }
        }
    }

    /**
     *
     * @return void
     */
    public function editAction() {
        $imageSrc = "";
        $isNewImage = false;
        $id = (int) $this->_getParam($this->_ID);
        $catId = $this->_obj->getBlockParams(1);
        $baseDir = $this->view->baseUrl() . "/";
        $returnAction = $this->_getParam('return');
        $cancelUrl = $this->view->url(array('action' => null,
            'actionKey' => null,$this->_ID => null, 'category' => null,
            'team' => null));

        if ($returnAction){
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
        }else{
            $returnUrl = str_replace($this->view->baseUrl(), '',
                $this->view->url(array('action' => $this->_currentAction,
                'actionKey' => null,$this->_ID => null, 'category' => null,
                    'team' => null)));
        }
        $objectName = $this->_objectList[$this->_currentAction];
        if (get_class($this->_obj) != $objectName){
            $oData = new $objectName();
        }else{
            $oData = $this->_obj;
        }

        $this->_editMode = true;
        // Get data details
        $data = $oData->populate($id, $this->_lang);
        if (!in_array($catId, explode(',', $data['ILK_RefId']))){
            $this->view->errorInfo = true;
        }
        if (!empty($this->_associationIds))
        {
            foreach($this->_associationIds as $association){
                $nameAssoc = $this->_objectList[$association];
                $assocObj = new $nameAssoc();
                $tmpData[$association] = $assocObj->getList(false,null,true);
                $this->view->data = $tmpData;
            }
        }
        $nameSize = $imgBasePath = '';
        // image src.
        if (!empty($this->_imageSrc))
        {
            $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id,'thumb');
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
            'imgBasePath' => $imgBasePath,
            'nameSize' => $nameSize,
            'object'   => $oData,
            'dataId'   => $id,
            'id' => $this->_currentAction,
            'isNewImage' => $isNewImage,
            'validationRequired' => $this->_getParam('validationRequired')
        );

        $form = new $this->_formName($options);
        $this->view->form = $form;
        if (!empty($this->_imagesLst)){
            $default = $this->_imgIndex;
            foreach($this->_imagesLst as $key => $value)
            {
                $this->_imgIndex = $key;
                $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id,'thumb');
                $imagePaths[$value] = $imageSource;
            }
            $form->setImagesLst($this->_imagesLst)
                ->setImagePaths($imagePaths);
            $this->_imgIndex = $default;
        }
        // action
        if (!$this->_request->isPost())
        {
            if (!empty($this->_associationIds)){
                foreach($this->_associationIds as $association){
                    $tmpData[$association] = $oData->getAssociations($association, $id, $this->_defaultEditLanguage);
                }
                $this->view->related = $tmpData;
            }
            $form->populate($data);
        }
        else
        {
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {
                $formData['ILK_RefId'] = $this->_obj->getBlockParams(1);
                if (isset($formData['moreImg'])){
                    $moreImg = $formData['moreImg'];
                }
                if (!empty($moreImg)){
                    $this->_saveImgData($moreImg, $id);
                }

                if (isset($formData[$this->_imageSrc]) && $formData[$this->_imageSrc] <> '' && $isNewImage){
                    $this->_setImage($this->_imageSrc, $formData, $id);
                }

                if (!empty($this->_imagesLst))
                {
                    foreach ($this->_imagesLst as $key => $src){
                        $this->_imgIndex = $key;
                        if (!empty($formData[$src])){
                            $imageSource = $this->_setImageSrc($data, $src, $id);
                            $imageSrc = $imageSource['imageSrc'];
                            $isNewImage = $imageSource['isNewImage'];
                            if ($isNewImage)
                                $this->_setImage($src, $formData, $id);
                            }
                        }
                    }

                $oData->save($id, $formData, $this->_lang);
                // redirect
                if (!isset($formData['submitSaveClose'])){
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

    /**
     * Delete action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function deleteAction()
    {
        $id = (int) $this->_getParam($this->_ID);
        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_currentAction,
                        'actionKey' => null,
                        $this->_ID => null
                    )));
        $this->view->assign('return', $returnUrl);

        $this->view->action = $this->_currentAction;
        $returnAction = $this->_getParam('return');
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if ($this->_request->isPost())
        {
            $del = $this->_request->getPost('delete');
            if ($del && $id > 0)
            {
                $oData->delete($id);
                Cible_FunctionsGeneral::delFolder($this->_imagesFolder . $id);
                if (!empty($this->_deleteFolder))
                    Cible_FunctionsGeneral::delFolder($this->_deleteFolder . $id);

                $this->_redirect($returnUrl);
            }

            $this->_redirect($returnUrl);
        }
        elseif ($id > 0)
        {
            $this->view->data = $oData->populate($id, $this->getCurrentEditLanguage());
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
        $tabId = $oData->getDataId();
//        $this->_tables[] = $oData->getDataTableName();
//        $this->_tables[] = $oData->getIndexTableName();
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($this->_lang, false);
        $params = array('foreignKey' => $oData->getForeignKey());

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);

        // Set the options of the list = links for actions (add, edit, delete...)
        $options = $this->_setActionsList($tabId, $page);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;
        if ($this->_actionKey == 'list'){
        }
        if ($this->_obj->getBlockParams(1) > 0){
            $select->where('ILK_RefId = ?', $this->_obj->getBlockParams(1));
        }
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
            case 'add':
                $this->addAction($this->_objectList[$this->_currentAction]);
                $this->_helper->viewRenderer->setRender('add');
                break;
            case 'edit':
                $this->editAction($this->_objectList[$this->_currentAction]);
                $this->_helper->viewRenderer->setRender('edit');
                break;
            case 'delete':
                $this->deleteAction($this->_objectList[$this->_currentAction]);
                $this->_helper->viewRenderer->setRender('delete');
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
                    if (strstr($foreignKey, ' = ')){
                        list($tmpDataId, $foreignKey) = explode(' = ', $foreignKey);
                    }
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
//                $this->_tables[] = $tmpDataTable;
//                $this->_tables[] = $tmpIndexTable;
                //Get the primary key of the first data object to join table
                if (empty($tmpDataId)){
                    $tmpDataId = $tmpObject->getDataId();
                }
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

                array('commands' => 'add'),
                array('action_panel' => 'edit-list', 'edit', 'delete')
            );
            foreach ($this->_actionsList as $key => $controls)
            {
                foreach ($controls as $key => $action)
                {
                    //Redirect to the real action to process If no actionKey = list page.
                    switch ($action)
                    {
                        case 'add':
                            $urlOptions = array(
                                'action' => $this->_currentAction,
                                'actionKey' => 'add',
                                'category' => $this->_obj->getBlockParams(1),
                                'team' => $this->_obj->getBlockParams(8))
                                ;
                            if (!empty($filter)){
                                $urlOptions['group-filter'] = $filter;
                            }
                            $commands = array(
                                $this->view->link($this->view->url($urlOptions),
                                    $this->view->getCibleText('button_add'),
                                    array('class' => 'action_submit add btn-banner'))
                            );

                            break;

                        case 'edit':
                            $url = $this->view->url(array(
                                'action' => $this->_currentAction,
                                'actionKey' => 'edit',
                                $this->_ID => 'xIDx',
                                'team' => $this->_obj->getBlockParams(8)
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
                $options['disable-export-to-excel'] = 'true';
                $options['disable-export-to-pdf'] = 'true';
                $options['disable-export-to-csv'] = 'true';
                $options['enable-print'] = 'false';
            if (!empty($this->_filterData))
                $options['filters'] = $this->_filterData;
            $options['renderPartial'] = $this->_renderPartial;
            $options['actionKey'] = $this->_currentAction;
        }
        else
            $options = $this->_actionsList;

        return $options;
    }
}
