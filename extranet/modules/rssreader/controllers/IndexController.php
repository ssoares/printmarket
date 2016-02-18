<?php
    
    class Rssreader_IndexController extends Cible_Controller_Categorie_Action
    {
        protected $_moduleID = 19;
        protected $_defaultAction = '';

        protected function delete($blockID)
        {


        }
        public function setOnlineBlockAction(){
            parent::setOnlineBlockAction();

        }

        public function listAction(){

            if ($this->view->aclIsAllowed('LecteurRss','edit',true)){

                // NEW LIST GENERATOR CODE //
                $tables = array(
                        'LecteurRssData' => array('ND_ID','ND_CategoryID','ND_Date','ND_ReleaseDate'),
                        'LecteurRssIndex' => array('NI_LecteurRssDataID','NI_LanguageID','NI_Title','NI_Status'),
                        'Status' => array('S_Code')
                );

                $field_list = array(
                    'NI_Title' => array(
                        'width' => '300px'
                    ),
                    'ND_Date' => array(
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
                ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"));

                $categoryArray = $category->fetchRow($select);
                $this->view->assign('categoryName', $categoryArray['CI_Title']);

                $LecteurRss = new LecteurRssData();
                $select = $LecteurRss->select()
                    ->from('LecteurRssData')
                    ->setIntegrityCheck(false)
                    ->join('LecteurRssIndex', 'LecteurRssData.ND_ID = LecteurRssIndex.NI_LecteurRssDataID')
                    ->join('Status', 'LecteurRssIndex.NI_Status = Status.S_ID')
                    ->where('ND_CategoryID = ?', $categoryID)
                    ->where('NI_LanguageID = ?', Zend_Registry::get("languageID"));
                    //->order('NI_Title');


                $options = array(
                    'commands' => array(
                        $this->view->link($this->view->url(array('controller'=>'index','action'=>'add')),$this->view->getCibleText('button_add_LecteurRss'), array('class'=>'action_submit add') )
                    ),
                    //'disable-export-to-excel' => 'true',
                    'filters' => array(
                        'LecteurRss-status-filter' => array(
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
                                'url' => "{$this->view->baseUrl()}/LecteurRss/index/edit/LecteurRssID/%ID%/pageID/".$pageID."/blockID/".$blockID,
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'ND_ID'
                                )
                            ),
                            'delete' => array(
                                'label' => $this->view->getCibleText('button_delete'),
                                'url' => "{$this->view->baseUrl()}/LecteurRss/index/delete/LecteurRssID/%ID%/pageID/".$pageID."/blockID/".$blockID,
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'ND_ID'
                                )
                            )
                        )
                    )
                );

                $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

                $this->view->assign('mylist', $mylist);
            }
        }

        public function addAction()
        {
            // variables
            $pageID = $this->_getParam('pageID');
            $blockID = $this->_getParam('blockID');
            $returnAction = $this->_getParam('return');
            $baseDir = $this->view->baseUrl();

            if($returnAction)
                $returnUrl = "/lecteurRss/index/$returnAction";
            elseif($blockID)
                $returnUrl = "/lecteurRss/index/list/blockID/$blockID/pageID/$pageID";
            else
                $returnUrl = "/lecteurRss/index/list-all/";


            // generate the form
            $form = new FormLecteurRss(array(
                'baseDir'   => $baseDir,
                'imageSrc'  => $imageSrc,
                'cancelUrl' => "$baseDir$returnUrl",
                'LecteurRssID'=>'',
                'isNewImage'=>true
                /*,
                'toApprove' => 0,
                'status'    => 2*/
            ));
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    /*if (isset($_POST['submitSaveSubmit'])){
                        $formData['ToApprove'] = 1;
                    }
                    elseif (isset($_POST['submitSaveReturnWriting'])){
                        $formData['Status'] = 2;
                        $formData['ToApprove'] = 0;
                    }
                    elseif(isset($_POST['submitSaveOnline'])){
                        $formData['Status'] = 1;
                        $formData['ToApprove'] = 0;
                    }
                    else{
                        $formData['Status'] = 2;
                        $formData['ToApprove'] = 0;
                    }*/


                    $LecteurRssObject = new LecteurRssObject();
                    $LecteurRssID = $LecteurRssObject->insert( $formData, Zend_Registry::get("currentEditLanguage"));

                    /**********************/

                }
                else
                {
                    $form->populate($formData);
                }
            }

        }

        public function editAction(){

            // variables
            $LecteurRssID = $this->_getParam('LecteurRssID');
            $pageID = $this->_getParam('pageID');
            $returnAction = $this->_getParam('return');
            $blockID = $this->_getParam('blockID');
            $baseDir = $this->view->baseUrl();

            if ($this->view->aclIsAllowed('LecteurRss','edit',true)){

            if($returnAction)
                $returnUrl = "/LecteurRss/index/$returnAction";
            elseif($blockID)
                $returnUrl = "/LecteurRss/index/list/blockID/$blockID/pageID/$pageID";
            else
                $returnUrl = "/LecteurRss/index/list-all/";

                $LecteurRssObject = new LecteurRssObject();
                $LecteurRss = $LecteurRssObject->populate($LecteurRssID, Zend_Registry::get("currentEditLanguage"));

                // image src.
                $config = Zend_Registry::get('config')->toArray();
                $thumbMaxHeight = $config['LecteurRss']['image']['thumb']['maxHeight'];
                $thumbMaxWidth  = $config['LecteurRss']['image']['thumb']['maxWidth'];

                //$this->view->assign('imageUrl', $LecteurRss['ImageSrc']);
                $this->view->assign('imageUrl', Zend_Registry::get("www_root")."/data/images/LecteurRss/$LecteurRssID/".str_replace($LecteurRss['ImageSrc'],$thumbMaxWidth.'x'.$thumbMaxHeight.'_'.$LecteurRss['ImageSrc'],$LecteurRss['ImageSrc']));

                $isNewImage = 'false';
                if ($this->_request->isPost()){
                    $formData = $this->_request->getPost();
                    if ($formData['ImageSrc'] <> $LecteurRss['ImageSrc']){
                        if ($formData['ImageSrc'] == "")
                            $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = Zend_Registry::get("www_root")."/data/images/LecteurRss/$LecteurRssID/tmp/mcith/mcith_".$formData['ImageSrc'];

                        $isNewImage = 'true';
                    }
                    else{
                        if ($LecteurRss['ImageSrc'] == "")
                            $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = Zend_Registry::get("www_root")."/data/images/LecteurRss/$LecteurRssID/".str_replace($LecteurRss['ImageSrc'],$thumbMaxWidth.'x'.$thumbMaxHeight.'_'.$LecteurRss['ImageSrc'],$LecteurRss['ImageSrc']);
                    }
                }
                else{
                    if ( empty( $LecteurRss['ImageSrc'] ) )
                        $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                    else
                        $imageSrc = Zend_Registry::get("www_root")."/data/images/LecteurRss/$LecteurRssID/".str_replace($LecteurRss['ImageSrc'],$thumbMaxWidth.'x'.$thumbMaxHeight.'_'.$LecteurRss['ImageSrc'],$LecteurRss['ImageSrc']);
                }

                // generate the form
                $form = new FormLecteurRss(array(
                    'baseDir'   => $baseDir,
                    'imageSrc'  => $imageSrc,
                    'cancelUrl' => "$baseDir$returnUrl",
                    'categoriesList' => "false",
                    'LecteurRssID' => $LecteurRssID,
                    'isNewImage'=>$isNewImage
                ));
                $this->view->form = $form;

                // action
                if ( !$this->_request->isPost() ){

                    if(isset($LecteurRss['Status']) && $LecteurRss['Status'] == 2)
                        $LecteurRss['Status'] = 0;

                    $form->populate($LecteurRss);

                }
                else {
                    $formData = $this->_request->getPost();

                    if ($form->isValid($formData)) {

                        if($formData['isNewImage'] == 'true' && $form->getValue('ImageSrc') <> ''){
                            $config = Zend_Registry::get('config')->toArray();
                            $srcOriginal    = "../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/tmp/".$form->getValue('ImageSrc');
                            $originalMaxHeight  = $config['LecteurRss']['image']['original']['maxHeight'];
                            $originalMaxWidth   = $config['LecteurRss']['image']['original']['maxWidth'];
                            $originalName       = str_replace($form->getValue('ImageSrc'),$originalMaxWidth.'x'.$originalMaxHeight.'_'.$form->getValue('ImageSrc'),$form->getValue('ImageSrc'));

                            $srcMedium       = "../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/tmp/medium_{$form->getValue('ImageSrc')}";
                            $mediumMaxHeight = $config['LecteurRss']['image']['medium']['maxHeight'];
                            $mediumMaxWidth  = $config['LecteurRss']['image']['medium']['maxWidth'];
                            $mediumName      = str_replace($form->getValue('ImageSrc'),$mediumMaxWidth.'x'.$mediumMaxHeight.'_'.$form->getValue('ImageSrc'),$form->getValue('ImageSrc'));

                            $srcThumb       = "../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/tmp/thumb_{$form->getValue('ImageSrc')}";
                            $thumbMaxHeight = $config['LecteurRss']['image']['thumb']['maxHeight'];
                            $thumbMaxWidth  = $config['LecteurRss']['image']['thumb']['maxWidth'];
                            $thumbName      = str_replace($form->getValue('ImageSrc'),$thumbMaxWidth.'x'.$thumbMaxHeight.'_'.$form->getValue('ImageSrc'),$form->getValue('ImageSrc'));

                            copy($srcOriginal,$srcMedium);
                            copy($srcOriginal,$srcThumb);

                            Cible_FunctionsImageResampler::resampled(array('src'=>$srcOriginal, 'maxWidth'=>$originalMaxWidth, 'maxHeight'=>$originalMaxHeight));
                            Cible_FunctionsImageResampler::resampled(array('src'=>$srcMedium, 'maxWidth'=>$mediumMaxWidth, 'maxHeight'=>$mediumMaxHeight));
                            Cible_FunctionsImageResampler::resampled(array('src'=>$srcThumb, 'maxWidth'=>$thumbMaxWidth, 'maxHeight'=>$thumbMaxHeight));

                            rename($srcOriginal,"../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/$originalName");
                            rename($srcMedium,"../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/$mediumName");
                            rename($srcThumb,"../../{$this->_config->document_root}/data/images/LecteurRss/$LecteurRssID/$thumbName");
                        }
                        if($formData['Status'] == 0)
                            $formData['Status'] = 2;

                        $LecteurRssObject->save($LecteurRssID, $formData, Zend_Registry::get("currentEditLanguage"));

                        if(!empty($pageID))
                        {
                            //$blockData  = Cible_FunctionsBlocks::getBlockDetails($blockID);
                            //$blockStatus    = $blockData['B_Online'];

                            $indexData = array();
                            $indexData['pageID']    = $formData['CategoryID'];;
                            $indexData['moduleID']  = $this->_moduleID;
                            $indexData['contentID'] = $LecteurRssID;
                            $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                            $indexData['title']     = $formData['Title'];
                            $indexData['text']      = '';
                            $indexData['link']      = '';
                            $indexData['contents']  = $formData['Title'] . " " . $formData['Brief'] . " " . $formData['Text'] . " " . $formData['ImageAlt'];
                            $indexData['action']    = '';

                            if($formData['Status'] == 1)
                                $indexData['action'] = 'update';
                            else
                                $indexData['action'] = 'delete';


                            Cible_FunctionsIndexation::indexation($indexData);

                            $this->_redirect($returnUrl);
                        }
                        else
                        {
                            $this->_redirect($returnUrl);
                        }
                    }
                }
            }
        }
        public function deleteAction(){

             // variables
            $pageID = (int)$this->_getParam( 'pageID' );
            $blockID = (int)$this->_getParam( 'blockID' );
            $LecteurRssID = (int)$this->_getParam( 'LecteurRssID' );

            if ($this->view->aclIsAllowed('LecteurRss','publish',true)){
                $this->view->return = !empty($pageID) ? $this->view->baseUrl()."/LecteurRss/index/list/blockID/$blockID/pageID/$pageID" : $this->view->baseUrl()."/LecteurRss/index/list-all/";

                $LecteurRssObject = new LecteurRssObject();

                 if ($this->_request->isPost()) {
                     $del = $this->_request->getPost('delete');
                     if ($del && $LecteurRssID > 0) {
                        $LecteurRssObject->delete($LecteurRssID);
                        $indexData['moduleID']  = $this->_moduleID;
                        $indexData['contentID'] = $LecteurRssID;
                        $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                        $indexData['action'] = 'delete';
                        Cible_FunctionsIndexation::indexation($indexData);

                        Cible_FunctionsGeneral::delFolder("../../{$this->_config->document_root}/data/images/LecteurRss/".$LecteurRssID);
                     }

                     if(!empty($pageID))
                        $this->_redirect("/LecteurRss/index/list/blockID/$blockID/pageID/$pageID");
                     else
                        $this->_redirect("/LecteurRss/index/list-all/");
                 }
                 elseif ($LecteurRssID > 0)
                    $this->view->LecteurRss = $LecteurRssObject->populate($LecteurRssID, Zend_Registry::get('currentEditLanguage'));
            }
        }
        public function toExcelAction(){
            $this->filename = 'LecteurRss.xlsx';

            $tables = array(
                    'LecteurRssData' => array('ND_ID','ND_CategoryID', 'ND_Date', 'ND_ReleaseDate'),
                    'LecteurRssIndex' => array('NI_LecteurRssDataID','NI_LanguageID','NI_Title','NI_Status'),
                    'Status' => array('S_Code')
            );

            $this->fields = array(
                'NI_Title' => array(
                    'width' => '',
                    'label' => ''
                ),
                'ND_ReleaseDate' => array(
                    'width' => '',
                    'label' => ''
                ),
                'ND_Date' => array(
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
            $blockID = $this->_getParam( 'blockID' );
            $pageID  = $this->_getParam( 'pageID' );

            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);

            $categoryID = $blockParameters[0]['P_Value'];

            $LecteurRss = new LecteurRssData();
            $this->select = $this->_db->select()
                ->from('LecteurRssData')
                //->setIntegrityCheck(false)
                ->join('LecteurRssIndex', 'LecteurRssData.ND_ID = LecteurRssIndex.NI_LecteurRssDataID')
                ->join('Status', 'LecteurRssIndex.NI_Status = Status.S_ID')
                ->where('ND_CategoryID = ?', $categoryID)
                ->where('NI_LanguageID = ?', Zend_Registry::get("languageID"))
                ->order('NI_Title');

            parent::toExcelAction();
        }
        public function allLecteurRssToExcelAction(){
            $this->filename = 'LecteurRss.xlsx';

            $tables = array(
                    'LecteurRssData' => array('ND_ID','ND_CategoryID', 'ND_Date', 'ND_ReleaseDate'),
                    'LecteurRssIndex' => array('NI_LecteurRssDataID','NI_LanguageID','NI_Title','NI_Status'),
                    'Status' => array('S_Code'),
                    'CategoriesIndex' => array('CI_Title'),
            );

            $this->fields = array(
                'NI_Title' => array(
                    'width' => '',
                    'label' => ''
                ),
                'ND_ReleaseDate' => array(
                    'width' => '',
                    'label' => ''
                ),
                'ND_Date' => array(
                    'width' => '',
                    'label' => ''
                ),
                'CI_Title' => array(
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

            $LecteurRss = new LecteurRssData();
            $this->select = $this->_db->select()
                ->from('LecteurRssData')
                //->setIntegrityCheck(false)
                ->join('LecteurRssIndex', 'LecteurRssData.ND_ID = LecteurRssIndex.NI_LecteurRssDataID')
                ->join('Status', 'LecteurRssIndex.NI_Status = Status.S_ID')
                ->join('CategoriesIndex', 'LecteurRssData.ND_CategoryID = CategoriesIndex.CI_CategoryID')
                ->where('NI_LanguageID = ?', Zend_Registry::get("languageID"))
                ->order('NI_Title');

           $blockID = $this->_getParam( 'blockID' );
           $pageID  = $this->_getParam( 'pageID' );

           if( $blockID && $pageID ){
                $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                $categoryID = $blockParameters[0]['P_Value'];

                $this->select->where('ND_CategoryID = ?', $categoryID);
            }

            parent::toExcelAction();
        }
        public function listApprobationRequestAction(){
            if( $this->view->aclIsAllowed('LecteurRss', 'publish') ){

                $tables = array(
                        'LecteurRssData' => array('ND_ID','ND_CategoryID','ND_ReleaseDate'),
                        'LecteurRssIndex' => array('NI_LecteurRssDataID','NI_LanguageID','NI_Title','NI_Status'),
                        'CategoriesIndex' => array('CI_Title')
                );

                $field_list = array(
                    'NI_Title' => array(
                        'width' => '400px'
                    ),
                    'CI_Title' => array(
                        /*'width' => '80px',
                        'postProcess' => array(
                            'type' => 'dictionnary',
                            'prefix' => 'status_'
                        )*/
                    ),
                    'ND_ReleaseDate' => array(
                        'width' => '120px'
                    )
                );

                $LecteurRss = new LecteurRssData();
                $select = $LecteurRss->select()
                    ->from('LecteurRssData')
                    ->setIntegrityCheck(false)
                    ->join('LecteurRssIndex', 'LecteurRssData.ND_ID = LecteurRssIndex.NI_LecteurRssDataID')
                    ->joinRight('CategoriesIndex', 'LecteurRssData.ND_CategoryID = CategoriesIndex.CI_CategoryID')
                    ->joinRight('Languages', 'Languages.L_ID = LecteurRssIndex.NI_LanguageID')
                    ->where('LecteurRssData.ND_ToApprove = ?', 1)
                    ->where('LecteurRssIndex.NI_LanguageID = CategoriesIndex.CI_LanguageID')
                    ->order('NI_Title');


                $options = array(
                    'disable-export-to-excel' => 'true',
                    'filters' => array(
                        'filter_1' => array(
                            'default_value' => null,
                            'associatedTo' => 'CI_Title',
                            'choices' => Cible_FunctionsCategories::getFilterCategories($this->_moduleID)
                        ),
                        'filter_2' => array(
                            'default_value' => null,
                            'associatedTo' => 'CI_LanguageID',
                            'choices' => Cible_FunctionsGeneral::getFilterLanguages()
                        )
                    ),
                    'action_panel' => array(
                        'width' => '50',
                        'actions' => array(
                            'edit' => array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => "{$this->view->baseUrl()}/LecteurRss/index/edit/LecteurRssID/%ID%/lang/%LANG%/approbation/true",
                                'findReplace' => array(
                                     array(
                                        'search' => '%ID%',
                                        'replace' => 'ND_ID'
                                    ),
                                    array(
                                        'search' => '%LANG%',
                                        'replace' => 'L_Suffix'
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
        public function deleteCategoriesAction(){

            if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){
                $id = $this->_getParam('ID');

                if($this->_request->isPost() && isset($_POST['delete']) ){

                    $this->_db->delete('Categories', "C_ID = '$id'");
                    $this->_db->delete('CategoriesIndex', "CI_CategoryID = '$id'");

                    $this->_redirect("/LecteurRss/index/list-categories/");

                } else if( $this->_request->isPost() && isset($_POST['cancel']) ){
                    $this->_redirect('/LecteurRss/index/list-categories/');
                } else {
                    $fails = false;

                    $select = $this->_db->select();
                    $select->from('CategoriesIndex', array('CI_Title'))
                           ->where('CategoriesIndex.CI_CategoryID = ?', $id);

                    $categoryName = $this->_db->fetchOne($select);

                    $this->view->assign('category_id', $id);
                    $this->view->assign('category_name', $categoryName);

                    $select = $this->_db->select();
                    $select->from('LecteurRssData')
                           ->where('LecteurRssData.ND_CategoryID = ?', $id);

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
                    $this->view->assign('returnUrl', '/LecteurRss/index/list-categories/');
                    $this->view->assign('fails', $fails);
                }

            }
        }

        private function getLecteurRssOnlineCount()
        {
            return $this->_db->fetchOne("SELECT COUNT(*) FROM LecteurRssData LEFT JOIN LecteurRssIndex ON LecteurRssData.LRD_ID = LecteurRssIndex.LRI_LecteurRssDataID WHERE LRD_AfficherWeb = '1'");
        }
    }
?>