<?php

/**
 * Module Imageslibrary
 * Controller for the backend administration.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: IndexController.php 425 2013-02-08 21:36:45Z ssoares $
 *
 */

/**
 * Manage actions for images associated with keywords and used as a gallery.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 */
class Imageslibrary_IndexController extends Cible_Controller_Categorie_Action
{

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 24;
    protected $_moduleTitle = 'imageslibrary';
    protected $_defaultAction = 'images';
    protected $_defaultRender = 'list';
    protected $_name = 'index';
    protected $_ID = 'id';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_imageSrc = 'IL_Filename';
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'images' => 'ImageslibraryObject',
        'list-teams' => 'ImageslibraryObject'
    );
    protected $_actionsList = array();
    protected $_disableExportToExcel = true;
    protected $_disableExportToPDF = true;
    protected $_disableExportToCSV = true;
    protected $_enablePrint = false;
    protected $_filterData = array();
    protected $_editMode = false;
    protected $_session;
    protected $_renderPartial = '';

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


        // . $this->_objectList[$this->_currentAction] . "/";
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('imageslibrary.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('imageslibrary.css'), 'all');
        $this->view->headScript()->appendFile($this->view->locateFile('manageRefValues.js', null, 'back'));
    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function imagesAction()
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $redirect = (bool)$this->_getParam('redirect');
            if ($redirect)
            {
                $url = str_replace('/redirect/1', '', $this->_request->getPathInfo());
                $this->_redirect ('/' . $url);
            }
            if ($this->_isXmlHttpRequest)
            {
                $this->disableView();
                $data = $this->_getParam('idsList');
                $_SESSION['idsList'] = empty($data) ? array() : $data;
                echo json_encode(true);
                exit;
            }

            $this->_colTitle = array(
                'idField' => 'IL_ID',
                'filenameField' => $this->_imageSrc,
                'format' => 'thumbList'
            );
            $this->_renderPartial = 'partials/imagesgrid.list.phtml';
            $obj = new ReferencesObject();
            $list = $obj->getListValues('album', $this->_defaultEditLanguage);
            $this->_filterData = array(
                'album' => array(
                    'label' => "",
                    'default_value' => null,
                    'associatedTo' => 'ILK_RefId',
                    'choices' => $list
                )
            );
//            $select->joinLeft('Imageslibrary_Keywords', 'IL_ID = ILK_ImageID')
//            ->joinLeft('References', 'R_ID = ILK_RefID');
            $this->_joinTables = array(
                array('obj' => 'ImageslibraryKeywordsObject', 'foreignKey' => 'IL_ID', 'noData' =>true),
                array('obj' => 'ReferencesObject', 'foreignKey' => 'ILK_RefID OR ILK_RefID is null', 'noData' =>true),
                );

            $this->_formName = 'FormImageslibrary';
            $this->_redirectAction();
        }
    }
    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function listTeamsAction()
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $redirect = (bool)$this->_getParam('redirect');
            if ($redirect)
            {
                $url = str_replace('/redirect/1', '', $this->_request->getPathInfo());
                $this->_redirect ('/' . $url);
            }
            if ($this->_isXmlHttpRequest)
            {
                $this->disableView();
                $data = $this->_getParam('idsList');
                $_SESSION['idsList'] = empty($data) ? array() : $data;
                echo json_encode(true);
                exit;
            }

            $this->_colTitle = array(
                'idField' => 'IL_ID',
                'filenameField' => $this->_imageSrc,
                'format' => 'thumbList'
            );
            $this->_renderPartial = 'partials/imagesgrid.list.phtml';
            $obj = new ReferencesObject();
            $list = $obj->getListValues('teams', $this->_defaultEditLanguage);
            $this->_filterData = array(
                'album' => array(
                    'label' => "",
                    'default_value' => null,
                    'associatedTo' => 'ILK_RefId',
                    'choices' => $list
                )
            );
            $this->_joinTables = array(
                array('obj' => 'ImageslibraryKeywordsObject', 'foreignKey' => 'IL_ID', 'noData' =>true),
                array('obj' => 'ReferencesObject', 'foreignKey' => 'ILK_RefID OR ILK_RefID is null', 'noData' =>true),
            );

            $this->_formName = 'FormImageslibrary';
            $this->_labelSuffix = 'images';
            $this->view->assign('cleaction', $this->_labelSuffix);
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
        $this->view->headLink()->appendStylesheet($this->view->locateFile('references.css'), 'all');
        $ids = array();
        $returnAction = $this->_getParam('return');
        $baseDir = $this->view->baseUrl() . "/";
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();
        $this->_registry->currentEditLanguage = $this->_registry->languageID;
        $this->_grayScale = false;
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $seq = 10;
            $last = $oData->getLastPosition();
            if ($last > 0)
                $seq += $last;

            if ($returnAction)
                $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
            else
                 $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                    'action' => $this->_defaultAction,
                    $this->_ID => null
                )));

            $currentFolder = $this->_imagesFolder . 'tmp/';


            $currentFolder = str_replace("../../www/", "../", $currentFolder);


            $filesList = $this->_findImagesFiles($currentFolder);

            if (!empty($filesList))
            {
                foreach ($filesList as $key => $image)
                {
                    $formData[$this->_imageSrc] = $key;
                    $formData['IL_Seq'] = $seq;

                    $recordID = $oData->insert($formData, $this->getDefaultEditLanguage());
                    /* IMAGES - Create dir */
                    if (!is_dir($this->_imagesFolder . $recordID))
                    {
                        mkdir($this->_imagesFolder . $recordID)
                            or die("Could not make directory");
                        mkdir($this->_imagesFolder . $recordID . "/tmp")
                            or die("Could not make directory");
                    }
                    // Save image
                    $this->_setImage($this->_imageSrc, $formData, $recordID);
                    $seq += 10;
                    array_push($ids, $recordID);
                    $tmp = array(
                        'ILI_Label1_2' => '',
                        'ILI_Label1_1' => '' ,
                        'ILI_Label2_2' => '',
                        'ILI_Label2_1' => '' ,
                        'ILI_Link_1' => '',
                        'ILI_Link_2' => '' ,
                        'ILI_Description_2' => '',
                        'ILI_Description_1' => '' ,
                        'ILK_RefId' => ''
                    );
                    $data = array_merge($formData, $tmp);
                    $oData->save($recordID, $data, $this->getDefaultEditLanguage());
                }
                $_SESSION['idsList'] = $ids;
                // redirect
                $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                    'actionKey' => 'edit-list',
                    $this->_ID => null,
                    "lang" => $this->languageSuffix
                )));

                $this->_redirect($returnUrl);
            }
            else{

                $this->_redirect($returnUrl);
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
    public function editListAction()
    {
        $this->view->headLink()->appendStylesheet($this->view->locateFile('references.css'), 'all');
        $imageSrc = "";
        $ids = $_SESSION['idsList'];
        $page = (int) $this->_getParam('page');

        $baseDir = $this->view->baseUrl() . "/";
        $cancelUrl = $this->view->url(array(
            'action' => $this->_currentAction,
            'actionKey' => null,
            $this->_ID => null
        ));
        $oDataName = $this->_objectList[$this->_currentAction];

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_editMode = true;
            $this->_grayScale = false;


            $oData = new $oDataName();
            $this->_dataIdField = $oData->getDataId();
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_currentAction,
                'actionKey' => null,
                $this->_ID => null
            )));

            $options = array(
                'moduleName' => $this->_moduleTitle,
                'moduleID' => $this->_moduleID,
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'source' => $this->_currentAction == 'list-teams' ? 'teams' : 'album',
                'empty' => true
            );
            $form = new $this->_formName($options);

            foreach ($ids as $id)
            {
                $subFormId = 'img' . $id;
                $subForm = new Zend_Form_SubForm();
                // Get data details
                $data = $oData->populate($id, $this->_currentEditLanguage);

                // image src.
                $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id, 'thumbList');
                $imageSrc = $imageSource['imageSrc'];
                $isNewImage = $imageSource['isNewImage'];

                // generate the form
                $options = array(
                    'moduleName' => $this->_moduleTitle,
                    'moduleID' => $this->_moduleID,
                    'baseDir' => $baseDir,
                    'cancelUrl' => $cancelUrl,
                    'imageSrc' => $imageSrc,
                    'imgField' => $this->_imageSrc,
                    'dataId' => $id,
                    'data' => $data,
                    'subFormId' => $subFormId,
//                    'object'     => $oData,
                    'source' => $this->_currentAction == 'list-teams' ? 'teams' : 'album',
                    'isNewImage' => $isNewImage
                );

                $tmpForm = new $this->_formName($options);
                $tmpForm->populate($data);
//                $pos = $tmpForm->getElement('IL_Seq')->getValue();
                $groups = $tmpForm->getDisplayGroups();
                foreach ($groups as $key => $group)
                {
                    $elements = array();
                    foreach ($group as $name => $element)
                    {
                        $subForm->addElement($element);
                        array_push($elements, $name);
                    }

                    $subForm->addDisplayGroup($elements, $key);
                    $subForm->getDisplayGroup($key)
                        ->removeDecorator('DtDdWrapper');
                }
                $form->addSubForm($subForm, $subFormId);
//                $form->getSubForm('img' . $id)->setOrder($pos);
            }

            $this->view->form = $form;

            // action
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    foreach ($formData as $key => $img)
                    {
                        if (is_array($img))
                        {
                            $imgId = $img[$oData->getDataId()];
                            $tmp = $oData->populate($imgId,1);
                            if ($img[$this->_imageSrc] != $tmp[$this->_imageSrc])
                                $img['isNewImage'] = 1;
                            if ($img[$this->_imageSrc] <> ''  && (bool)$img['isNewImage'])
                                $this->_setImage($this->_imageSrc, $img, $imgId);
                            $oData->save($imgId, $img, $this->getCurrentEditLanguage());
                        }
                    }
                    // redirect
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                'actionKey' => 'edit-list',
                                $this->_ID => null
                            )))
                        );
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
        $this->view->headLink()->appendStylesheet($this->view->locateFile('references.css'), 'all');
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

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->_editMode = true;
            $this->_cleanup = true;
            $this->_grayScale = false;
            $oData = new $oDataName();
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_currentAction,
                'actionKey' => null,
                $this->_ID => null
            )));

            // Get data details
            $data = $oData->populate($id, $this->_currentEditLanguage);

            // image src.
            $config = Zend_Registry::get('config')->toArray();

            $imageSource = $this->_setImageSrc($data, $this->_imageSrc, $id, 'thumbList');
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];

            // generate the form
            $options = array(
                'moduleName' => $this->_moduleTitle,
                'moduleID' => $this->_moduleID,
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'imageSrc' => $imageSrc,
                'imgField' => $this->_imageSrc,
                'dataId' => $id,
                'data' => $data,
                'empty' => false,
//                'object'     => $oData,
                'source' => $this->_currentAction == 'list-teams' ? 'teams' : 'album',
                'isNewImage' => $isNewImage
            );

            $form = new $this->_formName($options);
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
                    if ($formData[$this->_imageSrc] <> '' && $isNewImage)
                        $this->_setImage($this->_imageSrc, $formData, $id);

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
        $this->view->headScript()->appendFile($this->view->locateFile('moxman.loader.min.js', 'tinymce/plugins/moxiemanager/js'));
        $page = $this->_getParam('page');
        $this->view->moduleName = $this->_moduleTitle;
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
        $this->_tables = array(
            $dataTable => $columnData,
            $indexTable => $columnIndex
        );
        // Set the select query to create the paginator and the list.
        $oData->setOrderBy('IL_Seq ASC');
        $qry = $oData->getAll($this->_defaultEditLanguage, false);
        $qry->reset('columns')->columns(array('IL_ID', 'IL_Filename'))
            ->distinct();
        if ($this->_currentAction == 'list-teams'){
            $qry->where('R_TypeRef in (?)', array('teams'));
        }else{
            $qry->where('R_TypeRef not in  (?)', array('teams'));
        }
        $params = array('foreignKey' => $oData->getForeignKey());

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($qry, $params);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;

        // Set the options of the list = links for actions (add, edit, delete...)
        $options = $this->_setActionsList($tabId, $page);

        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $this->_tables, $field_list, $options);

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
            case 'edit-list':
                $this->editListAction();
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
                array('action_panel' => 'edit-list', 'edit', 'delete')
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
                            $urlOptions['group-filter'] = $filter;
                        $session = new Zend_Session_Namespace(SESSIONNAME);
                        $_SESSION["moxiemanager.filesystem.rootpath"] = "../../../../../" . $session->currentSite . '/data';
                        $pathTmp = "/data/images/" . $this->_moduleTitle . '/tmp';

                        $js = "javascript:moxman.upload({fields : '',
                        path : '" . $pathTmp . "',
                        insert_filter : function (data){},
                        onupload :  function(info) {
                                        window.location.href = '" . $this->view->url($urlOptions) . "';
                                    }
                                });";
                        $commands = array(
                            $this->view->link($js, $this->view->getCibleText('button_add_' . $this->_labelSuffix), array('class' => 'action_submit add'))
                        );

                        break;

                    case 'edit-list':
                        $urlOptions = array(
                            'controller' => $this->_name,
                            'action' => $this->_currentAction,
                            'actionKey' => 'edit-list'
                        );
                        if (!empty($filter))
                            $urlOptions['group-filter'] = $filter;

                        $editList = array(
                            'label' => $this->view->getCibleText('button_edit_list'),
                            'url' => $this->view->url($urlOptions),
                            'findReplace' => array(),
                            'returnUrl' => $this->view->Url() . "/"
                        );
                        $actions['edit-list'] = $editList;
                        break;

                    case 'edit':
                        $urlOptions = array(
                            'controller' => $this->_name,
                            'action' => $this->_currentAction,
                            'actionKey' => 'edit',
                            $this->_ID => 'xIDx'
                        );
                        if (!empty($filter))
                            $urlOptions['group-filter'] = $filter;

                        $edit = array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $this->view->url($urlOptions),
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
                        $urlOptions = array(
                            'controller' => $this->_name,
                            'action' => $this->_currentAction,
                            'actionKey' => 'delete',
                            $this->_ID => "xIDx"
                        );
                        $delete = array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => $this->view->url($urlOptions),
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
        if (!empty($this->_filterData))
            $options['filters'] = $this->_filterData;
        if ($this->_renderPartial)
            $options['renderPartial'] = $this->_renderPartial;

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
                    $noData = isset($object['noData']) ? $object['noData'] : false;
                }
                else
                {
                    $objName = $object;
                    $noData = isset($object['noData']) ? $object['noData'] : false;
                    $dataOnly = isset($params['dataOnly']) ? $params['dataOnly'] : false;
                }
                //Create an object and fetch data from object.
                $tmpObject = new $objName();
                $methods = get_class_methods($tmpObject);

                $tmpDataTable = $tmpObject->getDataTableName();
                $tmpIndexTable = $tmpObject->getIndexTableName();
                //Add data to tables list
                $search = $tmpObject->getSearchColumns();
                if (!empty($search)){
                    $dataCols = isset($search['data']) ? $search['data'] : $search;
                    $indexCols = isset($search['index']) ? $search['index'] : array();
                }else{
                    $dataCols = $tmpObject->getDataColumns();
                    $indexCols = $tmpObject->getIndexColumns();
                }

                $this->_tables[$tmpDataTable] = $dataCols;
                $this->_tables[$tmpIndexTable] = $indexCols;
                //Get the primary key of the first data object to join table
                $tmpDataId = $tmpObject->getDataId();

                // If it's the first loop, join first table to the current table
                if ($key == 0)
                {
                    if ($noData){
                        $select->joinLeft($tmpDataTable,
                            $tmpDataId . ' = ' . $foreignKey, array());
                    }else{
                        $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $foreignKey);
                    }
                    if (in_array('getDelField', $methods) && $this->_filterDeleted){
                        $select->where($tmpObject->getDelField() . ' = ?', 0);
                    }
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable) && !$dataOnly)
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        if ($noData){
                            $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId, array());
                        }else{
                            $select->joinLeft(
                                $tmpIndexTable, $tmpDataId . ' = ' . $tmpIndexId);
                        }
                        $select->where(
                            $tmpIndexTable . '.' . $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    if ($noData){
                        $select->joinLeft($tmpDataTable,
                            $tmpDataId . ' = ' . $foreignKey, array());
                    }else{
                        $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $foreignKey);
                    }
                    if (in_array('getDelField', $methods) && $this->_filterDeleted){
                        $select->where($tmpObject->getDelField() . ' = ?', 0);
                    }
                    if (!empty($tmpIndexTable) && !$dataOnly)
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        if ($noData){
                            $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId, array());
                        }else{
                            $select->joinLeft(
                                $tmpIndexTable, $tmpDataId . ' = ' . $tmpIndexId);
                        }
                        $select->where(
                            $tmpIndexTable . '.' . $tmpObject->getIndexLanguageId() . ' = ?', $this->_defaultEditLanguage);
                    }
                }
            }
        }


        return $select;
    }

    private function _findImagesFiles($currentFolder)
    {
        $filesList = array();
        $dirHandler = opendir($currentFolder);
               // for each file in the folder
        while (($file = readdir($dirHandler)) !== false)
        {
            $realPath = realpath($currentFolder . $file);
            $info = pathinfo($currentFolder . $file);
            $fileName = $file;
            // store it in an array
            if (filetype($realPath) == 'file')
            {
                $filesList[$fileName]['realPath'] = $realPath;
                $filesList[$fileName]['pathInfo'] = $info;
                $filesList[$fileName]['lastAccess'] = date('Y-m-d H:i:s');
                $filesList[$fileName]['lastModif'] = date('Y-m-d H:i:s', filemtime($realPath));
            }
        }
        return $filesList;
    }

}