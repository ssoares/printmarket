<?php

class Events_IndexController extends Cible_Controller_Categorie_Action
{
    protected $_moduleID = 7;
    protected $_moduleTitle   = 'events';
    protected $_name = 'index';
    protected $_defaultAction = 'list-all';
    protected $_ID = 'id';
    protected $_imageSrc = 'ImageSrc';


    public function setOnlineBlockAction(){
        parent::setOnlineBlockAction();
    }

    public function getManageDescription($blockID = null){
        $baseDescription = parent::getManageDescription($blockID);

        $listParams = $baseDescription;

        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        if($blockParameters)
        {
            $blockParams = $blockParameters->toArray();

            // Catégorie
            $categoryID = $blockParameters[0]['P_Value'];
            $categoryDetails = Cible_FunctionsCategories::getCategoryDetails($categoryID);
            $categoryName = $categoryDetails['CI_Title'];

            $listParams .= "<div class='block_params_list'><strong>";
            $listParams .= $this->view->getCibleText('label_category');
            $listParams .= "</strong>" . $categoryName . "</div>";

            // Nombre d'events afficher
            $nbNewsShow = $blockParameters[1]['P_Value'];
            $listParams .= "<div class='block_params_list'><strong>";
            $listParams .= $this->view->getCibleText('label_number_to_show');
            $listParams .= "</strong>" . $nbNewsShow . "</div>";
        }

        return $listParams;
    }

    public function getIndexDescription($blockID = null){

        $listParams = '';
        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        if($blockParameters)
        {
            $blockParams = $blockParameters->toArray();

            // Catégorie
            $categoryID = $blockParameters[0]['P_Value'];
            $categoryDetails = Cible_FunctionsCategories::getCategoryDetails($categoryID);
            $categoryName = $categoryDetails['CI_Title'];
            $listParams .= "<div class='block_params_list'><strong>Catégorie : </strong>" . $categoryName . "</div>";
        }

        // Nombre d'events Online
        $listParams .= "<div class='block_params_list'><strong>Événements en ligne : </strong>" . $this->getEventsOnlineCount($categoryID) . "</div>";

        return $listParams;
    }

    public function listAction(){
        $tables = array(
                'EventsData' => array('ED_ID','ED_CategoryID'),
                'EventsIndex' => array('EI_EventsDataID','EI_LanguageID','EI_Title','EI_Status'),
                'Status' => array('S_Code')
        );

        $field_list = array(
            'EI_Title' => array(
                //'width' => '300px'
            ),
            'S_Code' => array(
                'width' => '80px',
                'postProcess' => array(
                    'type' => 'dictionnary',
                    'prefix' => 'status_'
                )
            )
        );

        $this->view->params = $this->_getAllParams();
        $blockID = $this->_getParam( 'blockID' );
        $pageID  = $this->_getParam( 'pageID' );

        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);

        $categoryID = $blockParameters[0]['P_Value'];

        $category = new CategoriesIndex();
        $select = $category->select()
        ->where('CI_CategoryID = ?', $categoryID)
        ->where('CI_LanguageID = ?', $this->_defaultEditLanguage);

        $categoryArray = $category->fetchRow($select);
        $this->view->assign('categoryName', $categoryArray['CI_Title']);

        $events = new EventsData();
        $select = $events->select()
            ->from('EventsData')
            ->setIntegrityCheck(false)
            ->join('EventsIndex', 'EventsData.ED_ID = EventsIndex.EI_EventsDataID')
            ->join('Status', 'EventsIndex.EI_Status = Status.S_ID')
            ->where('ED_CategoryID = ?', $categoryID)
            ->where('EI_LanguageID = ?', $this->_defaultEditLanguage);
            //->order('EI_Title');


        $options = array(
            'commands' => array(
                $this->view->link($this->view->url(array('controller'=>'index','action'=>'add')),$this->view->getCibleText('button_add_events'), array('class'=>'action_submit add') )
            ),
            //'disable-export-to-excel' => 'true',
            'filters' => array(
                'events-status-filter' => array(
                    'label' => 'Filtre 1',
                    'default_value' => null,
                    'associatedTo' => 'S_Code',
                    'choices' => array(
                        '' => $this->view->getCibleText('filter_empty_status'),
                        'online' => $this->view->getCibleText('status_online'),
                        'offline' => $this->view->getCibleText('status_offline')
                    )
                )
            ),
            'action_panel' => array(
                'width' => '50',
                'actions' => array(
                    'edit' => array(
                        'label' => $this->view->getCibleText('button_edit'),
                        'url' => $this->view->url(array(
                            'action' => 'edit',
                            $this->_ID => "xIDx"
                            )),
                        'findReplace' => array(
                             array(
                            'search' => 'xIDx',
                            'replace' => 'ED_ID'
                            )
                        )
                    ),
                    'delete' => array(
                        'label' => $this->view->getCibleText('button_delete'),
                        'url' => $this->view->url(array(
                            'action' => 'delete',
                            $this->_ID => "xIDx"
                            )),
                        'findReplace' => array(
                             array(
                            'search' => 'xIDx',
                            'replace' => 'ED_ID'
                            )
                        )
                    )
                )
            )
        );

        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

        $this->view->assign('mylist', $mylist);
    }

    public function listAllAction(){

        if ($this->view->aclIsAllowed('events','edit',true)){

            // NEW LIST GENERATOR CODE //
            $tables = array(
                    'EventsData' => array('ED_ID','ED_CategoryID'),
                    'EventsIndex' => array('EI_EventsDataID','EI_LanguageID','EI_Title','EI_Status'),
                    'Status' => array('S_Code'),
                    'CategoriesIndex' => array('CI_Title')
            );

            $field_list = array(
                'EI_Title' => array(
                    'width' => '300px'
                ),
                'CI_Title' => array(
                    /*'width' => '80px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'status_'
                    )*/
                ),
                'S_Code' => array(
                    'width' => '80px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'status_'
                    )
                )
            );

            $events = new EventsData();
            $select = $events->select()
                ->from('EventsData')
                ->setIntegrityCheck(false)
                ->join('EventsIndex', 'EventsData.ED_ID = EventsIndex.EI_EventsDataID')
                ->join('Status', 'EventsIndex.EI_Status = Status.S_ID')
                ->joinRight('CategoriesIndex', 'EventsData.ED_CategoryID = CategoriesIndex.CI_CategoryID')
                ->joinRight('Categories', 'EventsData.ED_CategoryID = Categories.C_ID')
                ->joinRight('Languages', 'Languages.L_ID = EventsIndex.EI_LanguageID')
                ->where('EI_LanguageID = ?', $this->_defaultEditLanguage)
                ->where('EventsIndex.EI_LanguageID = CategoriesIndex.CI_LanguageID')
                ->where('C_ModuleID = ?', $this->_moduleID);
                //->order('EI_Title');


            $options = array(
                'commands' => array(
                    $this->view->link($this->view->url(array('controller'=>'index','action'=>'add')),$this->view->getCibleText('button_add_events'), array('class'=>'action_submit add') )
                ),
                //'disable-export-to-excel' => 'true',
                'filters' => array(
                    'events-category-filter' => array(
                        'label' => 'Filtre 1',
                        'default_value' => null,
                        'associatedTo' => 'ED_CategoryID',
                        'choices' => Cible_FunctionsCategories::getFilterCategories($this->_moduleID)
                    ),
                    'events-status-filter' => array(
                        'label' => 'Filtre 2',
                        'default_value' => null,
                        'associatedTo' => 'S_Code',
                        'choices' => array(
                            '' => $this->view->getCibleText('filter_empty_status'),
                            'online' => $this->view->getCibleText('status_online'),
                            'offline' => $this->view->getCibleText('status_offline'),
                        )
                    )
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $this->view->url(array(
                            'action' => 'edit',
                            $this->_ID => "xIDx",
                            'lang' => "xLANGx"
                            )),
                        'findReplace' => array(
                             array(
                            'search' => 'xIDx',
                            'replace' => 'ED_ID'
                            ),
                                array(
                                    'search' => 'xLANGx',
                                    'replace' => 'L_Suffix'
                                )
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => $this->view->url(array(
                                'action' => 'delete',
                                $this->_ID => "xIDx"
                                )),
                            'findReplace' => array(
                                array(
                                    'search' => 'xIDx',
                                    'replace' => 'ED_ID'
                                )
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

            $this->view->assign('mylist', $mylist);
        }
    }

    public function addAction(){

        // variables
        $pageID = $this->_getParam('pageID');
        $blockID = $this->_getParam('blockID');
        $returnAction = $this->_getParam('return');
        $baseDir = $this->view->baseUrl();

        if(empty($pageID))
            $categoriesList = 'true';
        else
            $categoriesList = 'false';
        $cancelUrl = $this->view->url(array(
            'action' => $this->_defaultAction,
            $this->_ID => null
        ));
        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
            'action' => $this->_defaultAction,
            $this->_ID => null
        )));

        if ($this->view->aclIsAllowed('events','edit',true))
        {
            $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];
            // generate the form
            $form = new FormEvents(array(
                'baseDir'   => $baseDir,
                'imageSrc'  => $imageSrc,
                'cancelUrl' => $cancelUrl,
                'categoriesList' => "$categoriesList",
                'eventID'=>'',
                'isNewImage'=>$isNewImage
            ));
            $this->view->form = $form;

            if ($this->_request->isPost()){
                $formData = $this->_request->getPost();
                    if ($form->isValid($formData)) {
                        if(!empty($pageID))
                        {
                            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                            $formData['CategoryID'] = $blockParameters[0]['P_Value'];
                        }
                        else
                            $formData['CategoryID'] = $this->_getParam('Param1');

                        if($formData['Status'] == 0)
                            $formData['Status'] = 2;

                        $eventsObject = new EventsObject();
                        $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['Title']);
                        $formData['ValUrl'] = $formattedName;
                        $eventID = $eventsObject->insert( $formData, Zend_Registry::get("currentEditLanguage"));


                        /*IMAGES*/
                        if (!is_dir($this->_imagesFolder . $eventID))
                        {
                            mkdir($this->_imagesFolder.$eventID) or die ("Could not make event directory");
                            mkdir($this->_imagesFolder.$eventID."/tmp") or die ("Could not make tmp directory");
                        }
                        // Save image
                        $this->_setImage($this->_imageSrc, $formData, $eventID);

                        $date = $formData['DateRange'][0]['from'];
                        $indexData['pageID']    = $event['CategoryID'];
                        $indexData['moduleID']  = $this->_moduleID;
                        $indexData['contentID'] = $eventID;
                        $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                        $indexData['title']     = $formData['Title'];
                        $indexData['text']      = '';
                        $indexData['link']      = $date . '/' . $formData['ValUrl'];
                        $indexData['object']  = 'EventsObject';
                        $indexData['contents']  = $formData['Title'] . " " . $formData['Brief'] . " " . $formData['Text'] . " " . $formData['ImageAlt'];

                        if($formData['Status'] == 1)
                            $indexData['action'] = 'add';
                        else
                            $indexData['action'] = 'delete';

                        Cible_FunctionsIndexation::indexation($indexData);

                        // redirect
                        if (isset($formData['submitSaveClose']))
                            $this->_redirect($returnUrl);
                        else
                            $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                        'action' => 'edit',
                                        $this->_ID => $eventID
                                    )))
                            );
                    }
                    else{
                        $form->populate($formData);
                 }
            }
        }
    }

    public function editAction(){

        // variables
        $eventID = $this->_getParam($this->_ID);
        $pageID = $this->_getParam('pageID');
        $returnAction = $this->_getParam('return');
        $blockID = $this->_getParam('blockID');
        $baseDir = $this->view->baseUrl();

        if ($this->view->aclIsAllowed('events','edit',true)){
            $this->_editMode = true;
            $cancelUrl = $this->view->url(array(
                'action' => $this->_defaultAction,
                $this->_ID => null
            ));

            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                'action' => $this->_defaultAction,
                $this->_ID => null
            )));
            // get event details
            $eventsObject = new EventsObject();
            $event = $eventsObject->populate($eventID, $this->getCurrentEditLanguage());

            // image src.
            $imageSource = $this->_setImageSrc($event, $this->_imageSrc, $eventID);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];
            // generate the form
            $form = new FormEvents(array(
                'baseDir'   => $baseDir,
                'imageSrc'  => $imageSrc,
                'cancelUrl' => $cancelUrl,
                'categoriesList' => "false",
                'eventID' => $eventID,
                'isNewImage'=>$isNewImage
            ));
            $this->view->form = $form;

            // action
            if ( !$this->_request->isPost() ){

                if(isset($event['Status']) && $event['Status'] == 2)
                    $event['Status'] = 0;

                $form->populate($event);

            }
            else {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData)) {
                    $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['Title']);
                    $formData['ValUrl'] = $formattedName;
                    if ($formData[$this->_imageSrc] <> ''  && $isNewImage)
                        $this->_setImage($this->_imageSrc, $formData, $eventID, $isNewImage);
                    $date = $formData['DateRange'][0]['from'];
                    $indexData['pageID']    = $event['CategoryID'];
                    $indexData['moduleID']  = $this->_moduleID;
                    $indexData['contentID'] = $eventID;
                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                    $indexData['title']     = $formData['Title'];
                    $indexData['text']      = '';
                    $indexData['link']      = $date . '/' . $formData['ValUrl'];
                    $indexData['object']  = 'EventsObject';
                    $indexData['contents']  = $formData['Title'] . " " . $formData['Brief'] . " " . $formData['Text'] . " " . $formData['ImageAlt'];

                    if($formData['Status'] == 1)
                        $indexData['action'] = 'update';
                    else
                        $indexData['action'] = 'delete';

                    Cible_FunctionsIndexation::indexation($indexData);

                    if($formData['Status'] == 0)
                        $formData['Status'] = 2;

                    $eventsObject->save($eventID, $formData, $this->getCurrentEditLanguage());

                    // redirect
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                    {
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                'action' => 'edit',
                                $this->_ID => $eventID
                            )))
                        );
                    }

                }
            }
        }
    }

    public function deleteAction(){

         // variables
        $pageID = (int)$this->_getParam( 'pageID' );
        $blockID = (int)$this->_getParam( 'blockID' );
        $eventID = (int)$this->_getParam( $this->_ID );
        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
            'action' => $this->_defaultAction,
            $this->_ID => null
        )));
        $this->view->return = $this->view->baseUrl()."/events/index/list/blockID/$blockID/pageID/$pageID";

        $eventsObject = new EventsObject();
        $eventData = $eventsObject->populate($eventID, Zend_Registry::get("currentEditLanguage"));
         if(Cible_ACL::hasAccess($pageID)){
             if ($this->_request->isPost()) {
                 $del = $this->_request->getPost('delete');
                 if ($del && $eventID > 0) {
                     $eventsObject->delete($eventID);

                     $indexData['moduleID']  = $this->_moduleID;
                     $indexData['contentID'] = $eventData['EI_ValUrl'];
                     $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                     $indexData['action']    = 'delete';
                     Cible_FunctionsIndexation::indexation($indexData);

                     Cible_FunctionsGeneral::delFolder($this->_imagesFolder.$eventID);
                 }
                 $this->_redirect($returnUrl);
             }
             else {
                if ($eventID > 0) {
                    // get event details

                    $this->view->event = $eventsObject->populate($eventID, Zend_Registry::get('currentEditLanguage'));
                 }
             }
         }
    }

    public function toExcelAction(){
        $this->filename = 'Events.xlsx';

        $tables = array(
                'EventsData' => array('ED_ID','ED_CategoryID'),
                'EventsIndex' => array('EI_EventsDataID','EI_LanguageID','EI_Title','EI_Status'),
                'Status' => array('S_Code')
        );

        $this->fields = array(
            'EI_Title' => array(
                'width' => '',
                'label' => ''
            ),
            'S_Code' => array(
                'width' => '',
                'label' => ''
            )
        );

        $this->filters = array(

        );

        $this->view->params = $this->_getAllParams();

        $events = new EventsData();
        $this->select = $this->_db->select()
            ->from('EventsData')
            //->setIntegrityCheck(false)
            ->join('EventsIndex', 'EventsData.ED_ID = EventsIndex.EI_EventsDataID')
            ->join('Status', 'EventsIndex.EI_Status = Status.S_ID')
            ->where('EI_LanguageID = ?', $this->_defaultEditLanguage)
            ->order('EI_Title');

        $blockID = $this->_getParam( 'blockID' );
        $pageID  = $this->_getParam( 'pageID' );

        if( $blockID && $pageID ){
            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
            $categoryID = $blockParameters[0]['P_Value'];

            $this->select->where('ED_CategoryID = ?', $categoryID);
        }

        parent::toExcelAction();
    }

    public function deleteCategoriesAction(){

        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){
            $id = $this->_getParam('ID');

            if($this->_request->isPost() && isset($_POST['delete']) ){

                $this->_db->delete('Categories', "C_ID = '$id'");
                $this->_db->delete('CategoriesIndex', "CI_CategoryID = '$id'");

                $this->_redirect("/events/index/list-categories/");

            } else if( $this->_request->isPost() && isset($_POST['cancel']) ){
                $this->_redirect('/events/index/list-categories/');
            } else {
                $fails = false;

                $select = $this->_db->select();
                $select->from('CategoriesIndex', array('CI_Title'))
                       ->where('CategoriesIndex.CI_CategoryID = ?', $id);

                $categoryName = $this->_db->fetchOne($select);

                $this->view->assign('category_id', $id);
                $this->view->assign('category_name', $categoryName);

                $select = $this->_db->select();
                $select->from('EventsData')
                       ->where('EventsData.ED_CategoryID = ?', $id);

                $result = $this->_db->fetchAll($select);

                if( $result ){
                    $fails = true;
                }

                if( !$fails ){
                    $select = $this->_db->select();
                    $select->from('Blocks')
                           ->joinRight('Parameters', 'Parameters.P_BlockID = Blocks.B_ID')
                           ->where('Parameters.P_Number = ?', 1)
                           ->where('Parameters.P_Value = ?', $id)
                           ->where('Blocks.B_ModuleID = ?', $this->_moduleID);

                    $result = $this->_db->fetchAll($select);

                    if( $result ){
                        $fails = true;
                    }
                }

                $this->_db->delete('ModuleCategoryViewPage', $this->_db->quoteInto('MCVP_CategoryID = ?',$id));

                $this->view->assign('module_name', $this->_moduleName);
                $this->view->assign('module_id', $this->_moduleID);
                $this->view->assign('returnUrl', '/events/index/list-categories/');
                $this->view->assign('fails', $fails);
            }

        }
    }

    private function getEventsOnlineCount($categoryID)
    {
        return $this->_db->fetchOne("SELECT COUNT(*) FROM EventsData LEFT JOIN EventsIndex ON EventsData.ED_ID = EventsIndex.EI_EventsDataID WHERE ED_CategoryID = '$categoryID' AND EI_Status = '1'");
    }
}