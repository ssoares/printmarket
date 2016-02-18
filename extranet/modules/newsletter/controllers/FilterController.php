<?php
     class Newsletter_FilterController extends Cible_Controller_Categorie_Action
    {
        protected $_moduleID = 8;
        protected $_defaultAction = 'list';


        public function addAction(){}
        public function editAction(){}
        public function deleteAction(){}

        public function ajaxAction(){
            $this->_helper->viewRenderer->setNoRender();

            $filterOption   = $this->_getParam('filterOption');
            $filterID       = $this->_getParam('filterID');
            $filterSetID    = $this->_getParam('filterSetID');

            $newElement = Newsletter_FilterController::getNewElement($filterOption, $filterID, $filterSetID);
            echo(Zend_Json::encode(array('newElement'=> $newElement)));
        }

        public function listCollectionAction(){
            if ($this->view->aclIsAllowed('newsletter','edit',true)){
                $tables = array(
                    'NewsletterFilter_CollectionsSet' => array('NFCS_ID','NFCS_Name'),
                );

                $field_list = array(
                    'NFCS_Name' => array(
                        'width' => '300px'
                    )
                );

                $options = array(
                    'commands' => array(
                        $this->view->link($this->view->url(array('controller'=>'filter','action'=>'add-collection')),$this->view->getCibleText('button_add_collection'), array('class'=>'action_submit add') )
                    ),
                    //'disable-export-to-excel' => 'true',
                    //'filters' => array(),
                    'action_panel' => array(
                        'width' => '50',
                        'actions' => array(
                            'edit' => array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => "{$this->view->baseUrl()}/newsletter/filter/edit-collection/collectionID/%ID%",
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'NFCS_ID'
                                )
                            ),
                            'delete' => array(
                                'label' => $this->view->getCibleText('button_delete'),
                                'url' => "{$this->view->baseUrl()}/newsletter/filter/delete-collection/collectionID/%ID%",
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'NFCS_ID'
                                )
                            )
                        )
                    )
                );

                $filterCollectionSet = new NewsletterFilterCollectionsSet();
                $select = $filterCollectionSet->select();


                $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

                $this->view->assign('mylist', $mylist);
            }
        }

        public function toExcelAction(){
            $this->filename = 'CollectionFilters.xlsx';

            $tables = array(
                'NewsletterFilter_CollectionsSet' => array('NFCS_ID','NFCS_Name'),
            );

            $this->fields = array(
                    'NFCS_Name' => array(
                        'width' => '300px',
                        'label' => ''
                    )
                );

            $this->filters = array(
            );

            $filterCollectionSet = new NewsletterFilterCollectionsSet();
            $this->select = $filterCollectionSet->select();

            parent::toExcelAction();
        }

        public function addCollectionAction(){
            if ($this->view->aclIsAllowed('newsletter','edit',true)){
                $baseDir = $this->view->baseUrl();
                $returnUrl = "/newsletter/filter/list-collection/";
                $form = new FormNewsletterFilterCollection(array('cancelUrl' => "$baseDir$returnUrl",));
                $this->view->assign('form', $form);

                $filterOptionsSelect = new NewsletterFilterProfilesFields();
                $select = $filterOptionsSelect->select();
                $filterOptionsData = $filterOptionsSelect->fetchAll($select)->toArray();

                $cpt = count($filterOptionsData);
                for($i=0;$i<$cpt;$i++){
                    $filterOptionsData[$i]['name'] = $this->view->getCibleText('newsletter_send_filter_'.$filterOptionsData[$i]['NFPF_Name']);
                }

                $this->view->assign('filterOptionsData', $filterOptionsData);

                if ($this->_request->isPost()) {
                    $formData = $this->_request->getPost();

                    if(array_key_exists('filterSet',$formData)){
                        $filterSetArray = $formData['filterSet'];
                        $i = 1;
                        foreach($filterSetArray as $key=>$filterSet){
                            $y = 1;
                            foreach($filterSet as $keyFilter=>$filter){
                                if($filter['filterSet'] <> '0'){
                                    $element = Newsletter_FilterController::getNewElement($filter['filterSet'],$y,$i, $filter['filterValue']);
                                    $filterSetArray[$key][$keyFilter]['element'] = $element;
                                    $y++;
                                }
                                else{
                                    unset($filterSetArray[$key][$keyFilter]);
                                }

                            }
                            if(count($filterSetArray[$key]) == 0)
                                unset($filterSetArray[$key]);
                            $i++;
                        }
                    }

                    else
                        $filterSetArray = array();

                    if ($form->isValid($formData)) {
                        $collectionData = new NewsletterFilterCollectionsSet();
                        $collection = $collectionData->createRow();
                        $collection->NFCS_Name = $formData['collectionForm']['NFCS_Name'];
                        $collection->save();

                        $collectionID = $collection->NFCS_ID;

                        foreach($filterSetArray as $filterSet){
                            $filterSetData = new NewsletterFilterFiltersSet();
                            $filterSetCreate = $filterSetData->createRow();
                            $filterSetCreate->save();
                            $filterSetID =  $filterSetCreate->NFFS_ID;

                            $collectionFilterSetData = new NewsletterFilterCollectionsFiltersSet();
                            $collectionFilterSet = $collectionFilterSetData->createRow();
                            $collectionFilterSet->NFCFS_CollectionSetID = $collectionID;
                            $collectionFilterSet->NFCFS_FilterSetID = $filterSetID;
                            $collectionFilterSet->save();

                            foreach($filterSet as $filter){
                                $filterData = new NewsletterFilterFilters();
                                $filterCreate = $filterData->createRow();
                                $filterCreate->NFF_ProfileFieldName = $filter['filterSet'];
                                $filterCreate->NFF_FilterSetID = $filterSetID;
                                $filterCreate->NFF_Value = $filter['filterValue'];
                                $filterCreate->save();
                            }
                        }
                        $this->_redirect($returnUrl);
                    }
                    else{
                        $this->view->assign('filterSetArray', $filterSetArray);
                    }
                }
            }
        }

        public function editCollectionAction(){
            if ($this->view->aclIsAllowed('newsletter','edit',true)){
                $collectionID = $this->_getParam('collectionID');

                $baseDir = $this->view->baseUrl();
                $returnUrl = "/newsletter/filter/list-collection/";
                $form = new FormNewsletterFilterCollection(array('cancelUrl' => "$baseDir$returnUrl",));

                $this->view->assign('form', $form);

                $filterOptionsSelect = new NewsletterFilterProfilesFields();
                $select = $filterOptionsSelect->select();
                $filterOptionsData = $filterOptionsSelect->fetchAll($select)->toArray();

                $cpt = count($filterOptionsData);
                for($i=0;$i<$cpt;$i++){
                    $filterOptionsData[$i]['name'] = $this->view->getCibleText('newsletter_send_filter_'.$filterOptionsData[$i]['NFPF_Name']);
                }

                $this->view->assign('filterOptionsData', $filterOptionsData);

                if ($this->_request->isPost()) {
                    $formData = $this->_request->getPost();

                    if(array_key_exists('filterSet',$formData)){
                        $filterSetArray = $formData['filterSet'];
                        $i = 1;
                        foreach($filterSetArray as $key=>$filterSet){
                            $y = 1;
                            foreach($filterSet as $keyFilter=>$filter){
                                if($filter['filterSet'] <> '0'){
                                    $element = Newsletter_FilterController::getNewElement($filter['filterSet'],$y,$i, $filter['filterValue']);
                                    $filterSetArray[$key][$keyFilter]['element'] = $element;
                                    $y++;
                                }
                                else{
                                    unset($filterSetArray[$key][$keyFilter]);
                                }

                            }
                            if(count($filterSetArray[$key]) == 0)
                                unset($filterSetArray[$key]);
                            $i++;
                        }
                    }

                    else
                        $filterSetArray = array();

                    //$this->view->dump($filterSetArray);
                    if ($form->isValid($formData)) {
                        $db = $this->_db;
                        $where = "NFCS_ID = $collectionID";
                        $db->update('NewsletterFilter_CollectionsSet', array('NFCS_Name'=> $formData['collectionForm']['NFCS_Name']), $where);


                        $filterSetSelect = new NewsletterFilterCollectionsFiltersSet();
                        $select = $filterSetSelect->select();
                        $select->where('NFCFS_CollectionSetID = ?', $collectionID);
                        $filterSetData = $filterSetSelect->fetchAll($select)->toArray();

                        foreach($filterSetData as $filterSet){
                            $filterSetDelete = new NewsletterFilterFiltersSet();
                            $where = 'NFFS_ID = ' . $filterSet['NFCFS_FilterSetID'];
                            $filterSetDelete->delete($where);

                            $filterDelete = new NewsletterFilterFilters();
                            $where = 'NFF_FilterSetID = ' . $filterSet['NFCFS_FilterSetID'];
                            $filterDelete->delete($where);

                            $collectionFilterSetDelete = new NewsletterFilterCollectionsFiltersSet();
                            $where = 'NFCFS_FilterSetID = ' . $filterSet['NFCFS_FilterSetID'];
                            $collectionFilterSetDelete->delete($where);
                        }



                        foreach($filterSetArray as $filterSet){
                            $filterSetData = new NewsletterFilterFiltersSet();
                            $filterSetCreate = $filterSetData->createRow();
                            $filterSetCreate->save();
                            $filterSetID =  $filterSetCreate->NFFS_ID;

                            $collectionFilterSetData = new NewsletterFilterCollectionsFiltersSet();
                            $collectionFilterSet = $collectionFilterSetData->createRow();
                            $collectionFilterSet->NFCFS_CollectionSetID = $collectionID;
                            $collectionFilterSet->NFCFS_FilterSetID = $filterSetID;
                            $collectionFilterSet->save();

                            foreach($filterSet as $filter){
                                $filterData = new NewsletterFilterFilters();
                                $filterCreate = $filterData->createRow();
                                $filterCreate->NFF_ProfileFieldName = $filter['filterSet'];
                                $filterCreate->NFF_FilterSetID = $filterSetID;
                                $filterCreate->NFF_Value = $filter['filterValue'];
                                $filterCreate->save();
                            }
                        }
                        $this->_redirect($returnUrl);

                    }
                    else{
                        $this->view->assign('filterSetArray', $filterSetArray);
                    }
                }
                else{
                    $collectionSelect = new NewsletterFilterCollectionsSet();
                    $select = $collectionSelect->select();
                    $select->where('NFCS_ID = ?', $collectionID);
                    $collectionData = $collectionSelect->fetchRow($select)->toArray();
                    $form->populate($collectionData);

                    $filterSetSelect = new NewsletterFilterCollectionsFiltersSet();
                    $select = $filterSetSelect->select()->setIntegrityCheck(false);
                    $select->from('NewsletterFilter_CollectionsFiltersSet')
                    ->where('NFCFS_CollectionSetID = ?', $collectionID)
                    ->join('NewsletterFilter_Filters', 'NFF_FilterSetID = NFCFS_FilterSetID')
                    ->order('NFF_FilterSetID')
                    ->order('NFF_ID');
                    $filterSetData = $filterSetSelect->fetchAll($select)->toArray();

                    $i = 0;
                    $y = 1;
                    $filterSetID = 0;
                    $filterSetArray = array();
                    foreach($filterSetData as $filterSet){
                        if($filterSetID <> $filterSet['NFF_FilterSetID']){
                            $filterSetID = $filterSet['NFF_FilterSetID'];
                            $i++;
                            $y = 1;
                        }
                        $filterSetArray[$i][$y]['filterSet'] = $filterSet['NFF_ProfileFieldName'];
                        $filterSetArray[$i][$y]['filterValue'] = $filterSet['NFF_Value'];
                        $filterSetArray[$i][$y]['element'] = Newsletter_FilterController::getNewElement($filterSet['NFF_ProfileFieldName'], $y, $i, $filterSet['NFF_Value']);

                        $y++;
                    }

                    $this->view->assign('filterSetArray', $filterSetArray);
                }
            }
        }

        public function deleteCollectionAction(){
            if ($this->view->aclIsAllowed('newsletter','edit',true)){
                $returnUrl = "/newsletter/filter/list-collection/";
                $collectionID = $this->_getParam('collectionID');

                if ($this->_request->isPost()) {
                    $del = $this->_request->getPost('delete');
                    if ($del && $collectionID > 0) {
                        $collectionDelete = new NewsletterFilterCollectionsSet();
                        $where = "NFCS_ID = $collectionID";
                        $collectionDelete->delete($where);

                        $filterSetSelect = new NewsletterFilterCollectionsFiltersSet();
                        $select = $filterSetSelect->select();
                        $select->where('NFCFS_CollectionSetID = ?', $collectionID);
                        $filterSetData = $filterSetSelect->fetchAll($select)->toArray();

                        foreach($filterSetData as $filterSet){
                            $filterSetDelete = new NewsletterFilterFiltersSet();
                            $where = 'NFFS_ID = ' . $filterSet['NFCFS_FilterSetID'];
                            $filterSetDelete->delete($where);

                            $filterDelete = new NewsletterFilterFilters();
                            $where = 'NFF_FilterSetID = ' . $filterSet['NFCFS_FilterSetID'];
                            $filterDelete->delete($where);

                            $collectionFilterSetDelete = new NewsletterFilterCollectionsFiltersSet();
                            $where = 'NFCFS_FilterSetID = ' . $filterSet['NFCFS_FilterSetID'];
                            $collectionFilterSetDelete->delete($where);
                        }
                    }
                    $this->_redirect($returnUrl);
                }
                else{
                    $collectionSelect = new NewsletterFilterCollectionsSet();
                    $select = $collectionSelect->select();
                    $select->where('NFCS_ID = ?', $collectionID);
                    $collectionData = $collectionSelect->fetchRow($select)->toArray();

                    $this->view->assign('collection', $collectionData);
                }
            }
        }

        private function getNewElement($filterOption, $filterID, $filterSetID, $filterValue=''){
            $newElement = '';
            if($filterOption == 'GP_Language'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterValue]'>";
                foreach( Cible_FunctionsGeneral::getAllLanguage() as $lang ){
                    if($filterValue == $lang['L_ID'])
                        $newElement .= "<option value='".$lang['L_ID']."' selected='selected'>".$lang['L_Title']."</option>";
                    else
                        $newElement .= "<option value='".$lang['L_ID']."'>".$lang['L_Title']."</option>";
                }
                $newElement .= "</select>";
            }
            elseif($filterOption == 'GP_Salutation'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterValue]'>";
                $greetings = $this->view->getAllSalutation();
                foreach ($greetings as $greeting){
                    if($filterValue == $greeting['S_ID'])
                        $newElement .= "<option value='".$greeting['S_ID']."' selected='selected'>".$greeting['ST_Value']."</option>";
                    else
                        $newElement .= "<option value='".$greeting['S_ID']."'>".$greeting['ST_Value']."</option>";
                }
                $newElement .= "</select>";
            }
            elseif($filterOption == 'NP_Categories'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterValue]'>";
                $newsletterCategories = $this->view->GetAllNewsletterCategories();
                foreach($newsletterCategories as $cat){
                    if($filterValue == $cat['C_ID'])
                        $newElement .= "<option value='".$cat['C_ID']."' selected='selected'>".$cat['CI_Title']."</option>";
                    else
                        $newElement .= "<option value='".$cat['C_ID']."'>".$cat['CI_Title']."</option>";
                }
                $newElement .= "</select>";

            }
            elseif($filterOption == 'NP_SubscriptionDate'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterValue]'>";
                $oNl = new NewsletterObject();
                $filterDates = $oNl->getFilterDates();
                foreach ($filterDates as $key => $filterData){
                    if($filterValue == $filterData['filter'])
                        $newElement .= "<option value='".$filterData['filter']."' selected='selected'>".$filterData['label']."</option>";
                    else
                        $newElement .= "<option value='".$filterData['filter']."'>".$filterData['label']."</option>";
                }
                $newElement .= "</select>";
            }
            elseif($filterOption == 'NP_TypeID'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterValue]'>";
                $oRef = new ReferencesObject();
                $types = $oRef->getListValues('typeClient', $this->_defaultEditLanguage, true);
                foreach($types as $id => $choice){
                    if($filterValue == $id)
                        $newElement .= "<option value='".$id."' selected='selected'>".$choice."</option>";
                    else
                        $newElement .= "<option value='".$id."'>".$choice."</option>";
                }
                $newElement .= "</select>";

            }
            elseif($filterOption == 'newFilter'){
                $newElement = "<select name='filterSet[".$filterSetID."][".$filterID."][filterSet]' id='filterSet-filterSet' class='selectFilterOption'>";
                $newElement .= "<option value='0'>".$this->view->getCibleText('newsletter_send_filter_selectOne')."</option>";

                $filterOptionsSelect = new NewsletterFilterProfilesFields();
                $select = $filterOptionsSelect->select();
                $filterOptionsData = $filterOptionsSelect->fetchAll($select)->toArray();

                $cpt = count($filterOptionsData);
                for($i=0;$i<$cpt;$i++){
                    $newElement .= "<option value='".$filterOptionsData[$i]['NFPF_Name']."'>".$this->view->getCibleText('newsletter_send_filter_'.$filterOptionsData[$i]['NFPF_Name'])."</option>";
                }
                $newElement .= "</select>";
            }
            return $newElement;
        }

    }
?>
