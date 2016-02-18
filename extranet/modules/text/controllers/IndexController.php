<?php

    class Text_IndexController extends Cible_Controller_Block_Abstract
    {
        protected $_moduleID = 1;

        public function init(){
            parent::init();
        }

        public static function getModulePermission()
        {
            return array('foobar');
        }

        protected function add($blockID){
             // add text
            $Languages = Cible_FunctionsGeneral::getAllLanguage();

            foreach($Languages as $Lang){
                $textData = new Text();
                $text = $textData->createRow();
                $text->TD_BlockID = $blockID;
                $text->TD_LanguageID = $Lang["L_ID"];

                $text->save();
            }
        }

        protected function delete($blockID){
            /* DELETE INDEXATION */
            $textSelect = new Text();
            $select = $textSelect->select()
            ->where('TD_BlockID = ?', $blockID);

            $textData = $textSelect->fetchAll($select);
            foreach($textData as $text){
                $indexData['moduleID']  = $this->_moduleID;
                $indexData['contentID'] = $text['TD_ID'];
                $indexData['languageID'] = $text['TD_LanguageID'];
                $indexData['action']    = 'delete';

                Cible_FunctionsIndexation::indexation($indexData);
            }

            /* DELETE ROW IN TEXTDATA TABLE   */
            $textData   = new Text();
            $where      = 'TD_BlockID = ' . $blockID;
            $textData->delete($where);
        }


        public function setOnlineBlockAction(){
            parent::setOnlineBlockAction();
            $blockID = $this->getRequest()->getParam('blockID');

            // index the new text if block online
            $textSelect = new Text();
            $select = $textSelect->select()->setIntegrityCheck(false)
            ->from('TextData')
            ->where('TD_BlockID = ?',$blockID)

            ->join('Blocks','B_ID = TD_BlockID')
            ->join('PagesIndex','PI_PageID = B_PageID')
            ->where('PI_LanguageID = TD_LanguageID');
            $textData = $textSelect->fetchAll($select);
            foreach($textData as $text){

                $indexData['pageID']    = $text['B_PageID'];
                $indexData['moduleID']  = $text['B_ModuleID'];
                $indexData['contentID'] = $text['TD_ID'];
                $indexData['languageID'] = $text['TD_LanguageID'];
                $indexData['title']     = $text['PI_PageTitle'];
                $indexData['text']      = '';
                $indexData['link']      = '';
                $indexData['object']  = 'TextObject';
                $indexData['contents']  = $text['PI_PageTitle'] . " " . $text['TD_OnlineText'];

                if($text['PI_Status'] == 1){
                    if($text['B_Online'] == 1)
                        $indexData['action'] = 'add';
                    else
                        $indexData['action'] = 'delete';
                }
                else
                    $indexData['action'] = 'delete';


                Cible_FunctionsIndexation::indexation($indexData);
            }

        }


        public function addAction(){
            throw new Exception('Not implemented');
        }

        public function editAction(){
            $this->view->title = "Modification d'un texte";
            if ($this->view->aclIsAllowed('text','edit',true)){
                $_blockID = $this->_getParam('blockID');
                $_pageid = $this->_getParam('pageID');
                $_id = $this->_getParam('ID');
                $base_dir = $this->getFrontController()->getBaseUrl();

                $blockText = new Text();
                $select = $blockText->select();
                $select->where('TD_BlockID = ?',$_blockID);
                $select->where('TD_LanguageID = ?', $this->_currentEditLanguage);
                $block = $blockText->fetchRow($select);
                $blockSelect = new Blocks();
                $selectBloc = $blockSelect->select()->setIntegrityCheck(false)
                ->from('Blocks')
                ->where('B_ID = ?', $_blockID)
                ->join('PagesIndex', 'PI_PageID = B_PageID')
                ->where('PI_LanguageID = ?', $block['TD_LanguageID']);

                // If block doesn't exist, creates it.
                if( empty($block) )
                {
                    $block = $blockText->createRow(array(
                        'TD_BlockID' => $_blockID,
                        'TD_LanguageID' => $this->_currentEditLanguage
                    ));

                    $block->save();

                    // load it
                    $block = $blockText->fetchRow($select);
                }

                if($_id)
                    $returnLink = "$base_dir/text/index/list-approbation-request/";
                    else
                    $returnLink = "$base_dir/page/index/index/ID/$_pageid";

                $form = new FormText(array(
                    'baseDir' => $base_dir,
                    'pageID' => $_pageid,
                    'cancelUrl' => $returnLink,
                    'toApprove' => $block["TD_ToApprove"],
                    'mode' => 'edit'
                ));

                if ( !$this->_request->isPost() ){
                    $block_data = empty($block) ? array() : $block->toArray();
                    $blockData = $blockSelect->fetchRow($selectBloc);
                    $block_data['PI_PageTitle'] = $blockData['PI_PageTitle'];
                    $form->populate($block_data);
                } else {

                    $formData = $this->_request->getPost();     if ($form->isValid($formData)) {
                        if (isset($_POST['submitSaveSubmit'])){
                            $block['TD_ToApprove'] = 1;
                            //header("location:".$returnLink);
                        }
                        elseif (isset($_POST['submitSaveReturnWriting'])){
                            $block['TD_ToApprove'] = 0;
                            //header("location:".$returnLink);
                        }
                        elseif(isset($_POST['submitSaveOnline'])){
                            $block['TD_OnlineTitle'] = $formData['TD_DraftTitle'];
                            $block['TD_OnlineText'] =  $formData['TD_DraftText'];
                            $block['TD_ToApprove'] = 0;
                            //header("location:".$returnLink);

                            // index the new text if block online
                            $blockData = $blockSelect->fetchRow($selectBloc);

                            if($blockData['B_Online'] == 1 && $blockData['PI_Status'] == 1){
                                $indexData['pageID']    = $blockData['B_PageID'];
                                $indexData['moduleID']  = $blockData['B_ModuleID'];
                                $indexData['contentID'] = $block['TD_ID'];
                                $indexData['languageID'] = $block['TD_LanguageID'];
                                $indexData['title']     = $blockData['PI_PageTitle'];
                                $indexData['text']      = '';
                                $indexData['link']      = '';
                                $indexData['object']  = 'TextObject';
                                $indexData['contents']  = $blockData['PI_PageTitle'] . " " . $block['TD_OnlineText'];
                                $indexData['action']    = 'update';

                                Cible_FunctionsIndexation::indexation($indexData);
                            }

                        }
                        else{
                            $returnLink = "";
                        }

                        $block['TD_DraftTitle'] = $formData['TD_DraftTitle'];
                        $block['TD_DraftText'] =  $formData['TD_DraftText'];

                        $block->save();

                        $oPage = new PagesObject();
                        $pageData['P_ID'] = $_pageid;
                        $pageData['PI_PageTitle'] = $formData['PI_PageTitle'];

                        $oPage->save($_pageid, $pageData, $this->_currentEditLanguage);


                        //if($returnLink <> "")
                            //$this->_redirect("/page/index/index/ID/$_pageid");
                    }
                }

                $this->view->assign('form', $form);
                $this->view->assign('pageId', $_pageid);
                $this->view->assign('onlineTitle', isset($block['TD_OnlineTitle']) ? $block['TD_OnlineTitle'] : '');
                $this->view->assign('onlineText', isset($block['TD_OnlineText']) ? $block['TD_OnlineText'] : '');
            }

        }

        public function deleteAction(){
            throw new Exception('Not implemented');
        }


        public function listApprobationRequestAction(){
            if( $this->view->aclIsAllowed('text', 'publish') ){

                 $tables = array(
                        'TextData' => array('TD_ID', 'TD_BlockID', 'TD_DraftTitle'),
                        'Blocks' => array('B_PageID'),
                        'BlocksIndex' => array('BI_BlockTitle'),
                        'Languages' => array('L_Suffix')
                );

                $field_list = array(
                    'BI_BlockTitle' => array(
                    )
                );

                $select = $this->_db->select()->from('TextData', $tables['TextData'])
                                        ->joinRight('Blocks', 'Blocks.B_ID = TextData.TD_BlockID')
                                        ->joinLeft('BlocksIndex', 'BlocksIndex.BI_BlockID = Blocks.B_ID', 'BI_BlockTitle')
                                        ->joinLeft('Languages', 'Languages.L_ID = TextData.TD_LanguageID')
                                        ->where('TextData.TD_LanguageID = BlocksIndex.BI_LanguageID')
                                        ->where('TextData.TD_ToApprove = ?', 1);

                $options = array(
                    'disable-export-to-excel' => 'true',
                    'action_panel' => array(
                        'width' => '50',
                        'actions' => array(
                            'edit' => array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => "{$this->view->baseUrl()}/text/index/edit/pageID/%PageID%/blockID/%BlockID%/ID/%ID%/lang/%LANG%",
                                'findReplace' => array(
                                     array(
                                        'search' => '%ID%',
                                        'replace' => 'TD_ID'
                                    ),
                                    array(
                                        'search' => '%BlockID%',
                                        'replace' => 'TD_BlockID'
                                    ),
                                    array(
                                        'search' => '%PageID%',
                                        'replace' => 'B_PageID'
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

        public function listAction(){
            $type = $this->_getParam('site');
            $this->view->type = 's';
            if (!empty($type))
                $this->view->type = $type;

            $this->view->assign('langId', $this->_defaultEditLanguage);

        }
}