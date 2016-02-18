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
 * @version   $Id: IndexController.php 426 2013-02-08 21:39:28Z ssoares $
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
class Imageslibrary_IndexController extends Cible_Controller_Action {

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 24;
    protected $_moduleTitle = 'imageslibrary';
    protected $_defaultAction = 'gridlist';
    protected $_defaultRender = 'gridlist';
    protected $_name = 'index';
    protected $_ID = 'album';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_actionsList = array();
    protected $_imageSrc = 'IL_Filename';
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'gridlist' => 'ImageslibraryObject',
        'slidelist' => 'ImageslibraryObject',
        'details' => 'ImageslibraryObject',
        'carrousel' => 'ImageslibraryObject'
    );
    protected $_session;
    protected $_renderPartial = '';
    protected $_lang;
    protected $_obj;
    protected $_whereClause;
    protected $_itemPerPage = 0;
    protected $_keywordsIds;

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
        $this->_obj = new ImageslibraryObject();
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

//        $dataImagePath = "../../"
//            . $this->_config->document_root
//            . "/data/images/";
//        if (isset($this->_objectList[$this->_currentAction]))
//            $this->_imagesFolder = $dataImagePath
//                . $this->_moduleTitle . "/";
//        // . $this->_objectList[$this->_currentAction] . "/";
//
//        if (isset($this->_objectList[$this->_currentAction]))
//            $this->_rootImgPath = Zend_Registry::get("www_root")
//                . "/data/images/"
//                . $this->_moduleTitle . "/";
        // . $this->_objectList[$this->_currentAction] . "/";
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('imageslibrary.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('imageslibrary.css'), 'all');
        //$this->view->headLink()->appendStylesheet($this->view->locateFile('jquery.ui.selectmenu.css'), 'all');
        //$this->view->headLink()->appendStylesheet($this->view->locateFile('print.css'), 'print');
//        $this->view->headScript()->appendFile($this->view->locateFile('searchEngine.js'));

    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function gridlistAction() {
        $this->_itemPerPage = 15;
        $keywords = array();
        $data = array();
        $pline = false;
        $id = $this->_getParam($this->_ID);
        $imgId = $this->_getParam('img');
        if ($imgId > 0) {
            $this->_currentAction = 'details';
            $this->_itemPerPage = 100000;
            $pageRealisation = Cible_FunctionsCategories::getPagePerCategoryView(0, 'slidelist', $this->_moduleID, Zend_Registry::get('languageID'), true);

            $uriBack = $this->view->url(array(
                'controller' => $pageRealisation,
                'img' => null,
                'nav' => null,
                'action' => null,
                'album' => null)
            );
            $this->view->uriBack = $uriBack;
            $this->view->imgId = $imgId;
        }


        $keywords = $this->_getKeywordsList($id);
        if ($id != 'all') {
            $this->_whereClause = $id;
            $data['collection'] = $id;
        }
        //if ($id != 'all')
            //array_push($this->_joinTables, array('name' => 'ImageslibraryCollectionsObject', 'clause' => $this->_whereClause));
        $keywordsFilterIds = explode(',', $this->_getParam('spec'));
        if (!empty($keywordsFilterIds[0])) {
            array_push($this->_joinTables, array('name' => 'ImageslibraryKeywordsObject', 'clause' => $keywordsFilterIds));
            $data['keywords'] = $keywordsFilterIds;
        }
        $session = new Zend_Session_Namespace('images');
        $session->images = $data;
        $this->view->keywords = $session->keywords;
        $this->_colTitle = array(
            'idField' => 'IL_ID',
            'filenameField' => $this->_imageSrc,
            'format' => 'thumbList'
        );
        $this->_renderPartial = 'partials/imagesgridFrontend.list.phtml';
        $this->_filterData = array();
        $this->_formName = 'FormImageslibrary';
        $options = array(
            'moduleName' => $this->_moduleTitle,
            'moduleID' => $this->_moduleID,
            'keywords' => $keywords
        );
        $form = new $this->_formName($options);
        $form->populate($data);
        $this->view->form = $form;
        $this->view->url = $this->view->baseUrl() . '/' . (Cible_FunctionsCategories::getPagePerCategoryView(0, 'gridlist', $this->_moduleID));
        $this->view->urlDetails = $this->view->baseUrl() . '/' . (Cible_FunctionsCategories::getPagePerCategoryView(0, 'details', $this->_moduleID));

        $this->_redirectAction();
    }

    /**
     * Build the keywords list based on the associations arrays
     *
     * @param type $id Id of the current collection
     * @return array Keywords for the checkbox;
     */
    private function _getKeywordsList($id) {
        $keywords = array();
        // Liste des images associées à la collecitons
        //$oImgC = new ImageslibraryCollectionsObject();
        // $images = $oImgC->getListByCollection($id);
        // listes de mots unique par images de la collection
        $oImgK = new ImageslibraryKeywordsObject();
        // $keywordsAssoc = $oImgK->getListByCollectionImages($images);
        $keywordsAssoc = $oImgK->getData();

        $oRef = new ReferencesObject();
        foreach ($keywordsAssoc as $key => $value) {
            $tmp = $oRef->getValueById($value, $this->_lang);
            $keywords[$key] = $tmp['value'];
        }
        asort($keywords);

        return $keywords;
    }

    /**
     * Display the list for slider
     *
     * @return void
     */
    public function slidelistAction() {
        $this->_itemPerPage = 100000;

        $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.min.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.swipe.min.js', 'jquery'));
        $blockId = $this->_getParam('BlockID');

        $this->_currentAction = 'slidelist';

        $oData = new ImageslibraryObject();
        if (!empty($blockId)) {
            $blockData = Cible_FunctionsBlocks::getBlockDetails($blockId);
            $this->view->title = $blockData['BI_BlockTitle'];
            $oData->setBlockID($blockId);
            $oData->setBlockParams();
        } else{
            if(isset($this->view->params['forceBlock'])&& $this->view->params['forceBlock']){
                $block = $this->view->params;
                $oData->setBlock($block);
            }
        }
        switch($oData->getBlockParams('5')){
            case("carousel"):
                $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.carousel.min.js', 'jquery'));
//                $this->_helper->viewRenderer->setRender('carrousel');
                $this->view->vertical = $oData->getBlockParams('8');
                break;
            case("scrollVert"):
                $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.scrollVert.min.js', 'jquery'));
                break;
            case("shuffle"):
                $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.shuffle.min.js', 'jquery'));
                break;
            case("tileSlide"):
            case("tileBlind"):
                $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.tile.min.js', 'jquery'));
                break;
            case("flipHorz"):
            case("flipVert"):
                $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.flip.min.js', 'jquery'));
                break;
            default:
                break;
        }

        $detailsPage = Cible_FunctionsCategories::getPagePerCategoryView(0, 'details', $this->_moduleID, $this->_lang, true);
        $this->view->detailsPage = $detailsPage;
        // Slider parameters
        $this->view->autoPlay = $oData->getBlockParams('1');
        $this->view->delais = $oData->getBlockParams('2');
        $this->view->transition = $oData->getBlockParams('3');
        $this->view->navi = $oData->getBlockParams('4');
        $this->view->effect = $oData->getBlockParams('5');
        $this->view->prettyPhoto = $oData->getBlockParams('9');

        $keywordsIds = $oData->getBlockParams('7');

        if (!empty($keywordsIds)) {
            $this->view->keywordsIds = $keywordsIds;
            $keywordsIds = explode(',', $keywordsIds);
            array_push($this->_joinTables, array('name' => 'ImageslibraryKeywordsObject', 'clause' => $keywordsIds));
        }
        $this->_colTitle = array(
            'idField' => 'IL_ID',
            'filenameField' => $this->_imageSrc,
            'format' => 'thumbList'
        );

        $this->_redirectAction();
        if ($oData->getBlockParams(5) == "carousel"){
            $this->renderScript('index/carrousel.phtml');
        }
    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function detailsAction() {
        $this->gridlistAction();
        $this->view->data['ILI_Label1'];
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
        $oData->setOrderBy(array('IL_Seq ASC', 'IL_Filename ASC'));
        $select = $oData->getAll($this->_lang, false);
        $select->distinct();
        $params = array('foreignKey' => $oData->getForeignKey());

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;

        // Set the options of the list = links for actions (add, edit, delete...)
        $array = $oData->getAllImagelibrary($select);

        $options = $this->_setActionsList($tabId, $page);
        $options['adapter'] = 'array';
        $options['adapterData'] = $array;
        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $this->_tables, $field_list, $options);

        $mylist->setItemCountPerPage($this->_itemPerPage);

        if ($this->_currentAction == 'details') {
            $page = $this->_getParam('page');
            if (empty($page))
                $page = 1;
            $nav = $this->_getParam('nav');
            if (empty($nav)) {
                $images = $mylist->getItemsByPage($page);
                $page = ($page - 1) * $mylist->getItemCountPerPage();
                foreach ($images as $key => $img) {
                    $id = $this->_getParam('img');
                    if ($img[$tabId] == $id)
                        $page += ($key + 1);
                }
            }
            else {
                $page = $nav;
                $backToPage = ceil($page / $mylist->getItemCountPerPage());
                if ($backToPage == 0)
                    $backToPage = 1;
                $uriBack = $this->view->url(array('img' => null, 'nav' => null, 'page' => $backToPage));
                $this->view->uriBack = $uriBack;
            }

            $mylist->setItemCountPerPage(1);
            $mylist->setCurrentPageNumber($page);
            $item = $mylist->getItemsByPage($page);
            $id = $item[0][$tabId];
            $image = $oData->populate($id, $this->_lang);
            $this->view->data = $image;
            $this->view->image = $mylist->getCurrentItems();
        }

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
            case 'slidelist':
                $this->_listAction($this->_objectList[$this->_currentAction]);
                break;
            case 'details':
                $this->_listAction($this->_objectList[$this->_currentAction]);
                $this->_helper->viewRenderer->setRender('details');
                break;
            default:
                $this->_listAction($this->_objectList[$this->_currentAction]);
                break;
        }
    }

    private function _setActionsList($tabId, $page = 1) {
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

        foreach ($this->_actionsList as $key => $controls) {
            foreach ($controls as $key => $action) {
                //Redirect to the real action to process If no actionKey = list page.
                switch ($action) {
                    case 'add':
                        $urlOptions = array(
                            'controller' => $this->_name,
                            'action' => $this->_currentAction,
                            'actionKey' => 'add');
                        $session = new Zend_Session_Namespace(SESSIONNAME);
                        $_SESSION["moxiemanager.filesystem.rootpath"] = "../../../../../" . $session->currentSite . '/data';
                        $pathTmp = "/data/images/" . $this->_moduleTitle . "/tmp";

                        $js = "javascript:mcImageManager.upload({fields : '',
                        path : '" . $pathTmp . "',
                        insert_filter : function (data){},
                        onupload :  function(info) {
                                        window.location.href = '" . $this->view->url($urlOptions) . "';
                                    }
                                });";
                        if (!empty($filter)) {
                            $urlOptions['group-filter'] = $filter;
                        }
                        $commands = array(
                            $this->view->link($js, $this->view->getCibleText('button_add_' . $this->_labelSuffix), array('class' => 'action_submit add'))
                        );

                        break;

                    case 'edit-list':
                        $url = $this->view->baseUrl() . "/"
                                . $this->_moduleTitle . "/"
                                . $this->_name . "/"
                                . $this->_currentAction . "/"
                                . "actionKey/edit-list/"
                                . "page/" . $page;
                        if (!empty($filter)) {
                            $url .= '/group-filter/' . $filter;
                        }
                        $editList = array(
                            'label' => $this->view->getCibleText('button_edit_list'),
                            'url' => $url,
                            'findReplace' => array(),
                            'returnUrl' => $this->view->Url() . "/"
                        );
                        $actions['edit-list'] = $editList;
                        break;

                    case 'edit':
                        $url = $this->view->baseUrl() . "/"
                                . $this->_moduleTitle . "/"
                                . $this->_name . "/"
                                . $this->_currentAction . "/"
                                . "actionKey/edit/"
                                . $this->_ID . "/%ID%/page/" . $page;
                        if (!empty($filter)) {
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
                            'url' => $this->view->baseUrl() . "/"
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
            'commands' => $commands,
            'action_panel' => $actionPanel
        );

//        if ($this->_enablePrint)
//            $options['enable-print'] = 'true';
        if ($this->_renderPartial)
            $options['renderPartial'] = $this->_renderPartial;
        $options['list_options']['perPage'] = 15;
        $options['actionKey'] = $this->_currentAction;

        return $options;
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
    private function _addJoinQuery($select, array $params = array()) {
        if (isset($params['joinTables']) && count($params['joinTables']))
            $this->_joinTables = $params['joinTables'];

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        if (count($this->_joinTables) > 0) {
            // Get the constraint attribute = foreign key to link tables.
            $foreignKey = $params['foreignKey'];
            // Loop on tables list(given by object class) to build the query
            foreach ($this->_joinTables as $key => $object) {
                //Create an object and fetch data from object.
                $tmpObject = new $object['name']();
                $tmpDataTable = $tmpObject->getDataTableName();
                $tmpIndexTable = $tmpObject->getIndexTableName();
                $tmpColumnData = $tmpObject->getDataColumns();
                $tmpColumnIndex = $tmpObject->getIndexColumns();
                //Add data to tables list
                $this->_tables[$tmpDataTable] = $tmpColumnData;
                if (!empty($tmpColumnIndex))
                    $this->_tables[$tmpIndexTable] = $tmpColumnIndex;

                if (isset($object['clause']))
                    $tmpObject->setClause($object['clause']);
                //Get the primary key of the first data object to join table
                $tmpDataId = $tmpObject->getDataId();
                // If it's the first loop, join first table to the current table
                if ($key == 0) {
                    $select->joinRight($tmpDataTable, $tmpDataId . ' = ' . $foreignKey . $tmpObject->getClause(), array());
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable)) {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable, $tmpDataId . ' = ' . $tmpIndexId, array());
                        $select->where(
                                $tmpIndexTable . '.' . $tmpObject->getIndexLanguageId() . ' = ?', $this->_lang);
                    }
                } elseif ($key > 0) {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    $select->joinRight(
                            $tmpDataTable, $tmpDataId . ' = ' . $foreignKey . $tmpObject->getClause(), array());

                    if (!empty($tmpIndexTable)) {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable);

                        $select->where(
                                $tmpIndexTable . $tmpObject->getIndexLanguageId() . ' = ?', $this->_lang);
                    }
                }
            }
        }

        return $select;
    }

}
