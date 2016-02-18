<?php
    class Newsletter_RecipientController extends Cible_Extranet_Controller_Module_Action{
        function indexAction(){
            
        }
        
        function addAction(){
            $this->view->title = "Ajouter un destinataire";
            
            if ($this->view->aclIsAllowed('newsletter','manage',true)){
                $pageID         = (int)$this->_getParam('pageID');
                $blockID        = (int)$this->_getParam('blockID');
                $newsletterID   = (int)$this->_getParam( 'newsletterID' );  
                
                $baseDir = $this->view->baseUrl();
                
                $return =  "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $cancelUrl =  $baseDir . $return;
                $form = new FormNewsletterRecipient(array(
                    'baseDir'   => $baseDir,
                    'cancelUrl' => $cancelUrl 
                ));
                
                $this->view->form = $form;
                
                if ($this->_request->isPost()){
                    $formData = $this->_request->getPost();    
                        if ($form->isValid($formData)) {
                            $profile = new Profile(2);
                            $members = $profile->findMembers(array('email'=>$formData['email']));
                        
                            if (count($members) == 0){
                                foreach($formData as $key=>$val){
                                    if($key <> "submitSave"){
                                        $data[$key] = $val;
                                    }
                                }
                                $categorySelect = new NewsletterReleases();
                                $select = $categorySelect->select();
                                $select->where('NR_ID = ?', $newsletterID);
                                $categoryData = $categorySelect->fetchRow($select);
                                
                                $data['newsletter_categories'] = $categoryData['NR_CategoryID'];
                                
                                $profile->addMember($data);
                                
                                $this->_redirect($return);
                                
                                
                            }
                            else{
                                $form->getElement('email')->addError($this->view->getCibleText('validation_message_used_email'));
                            }
                        }
                        else{
                            $form->populate($formData);
                     }
                }
            }
        }
        
        function editAction(){
            $this->view->title = "Edition des informations d'un destinataire";
            
            if ($this->view->aclIsAllowed('newsletter','manage',true)){
                $pageID         = (int)$this->_getParam('pageID');
                $blockID        = (int)$this->_getParam('blockID');
                $newsletterID   = (int)$this->_getParam( 'newsletterID' );  
                $recipientID    = (int)$this->_getParam( 'recipientID' );
                
                $baseDir = $this->view->baseUrl();
                
                $profile = new Profile(2);
                $recipientDetails = $profile->getMemberDetails($recipientID);
                
                
                // generate the form
                $return =  "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $cancelUrl =  "$baseDir/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $form = new FormNewsletterRecipient(array(
                    'baseDir'   => $baseDir,
                    'cancelUrl' => $cancelUrl 
                ));
                
                $this->view->form = $form;
                
                if ($this->_request->isPost()) {
                    $formData = $this->_request->getPost();
                    if ($form->isValid($formData)) {
                        $members = $profile->findMembers(array('email'=>$formData['email']));
                        
                        if ((count($members) == 1 and $members[0]['MemberID'] == $recipientID) or (count($members) == 0)){
                            foreach($formData as $key=>$val){
                                if($key <> "submitSave"){
                                    $profile->updateMember($recipientID,array($key=>$val));                                
                                }
                            }    
                        }
                        else{
                            $form->getElement('email')->addError($this->view->getCibleText('validation_message_used_email'));    
                        }
                        $this->_redirect($return);      
                    }
                }
                else{
                    $form->populate($recipientDetails);
                }
            }
        }
        
        function deleteAction(){
            // web page title
            $this->view->title = "Suppression d'un destinataire";
            
            if ($this->view->aclIsAllowed('newsletter','manage',true)){
                 // variables
                $pageID         = (int)$this->_getParam( 'pageID' );
                $blockID        = (int)$this->_getParam( 'blockID' );
                $newsletterID   = (int)$this->_getParam( 'newsletterID' );
                $recipientID    = (int)$this->_getParam( 'recipientID' ); 
                
                $return =  "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $this->view->return = $this->view->baseUrl() . $return;
                
                $profile = new Profile(2);
                $recipientDetails = $profile->getMemberDetails($recipientID);
                $this->view->recipient =  $recipientDetails;
                
                if ($this->_request->isPost()) {
                     $del = $this->_request->getPost('delete');
                     if ($del) {                                                 
                         // search the category id of the release
                         $categorySelect = new NewsletterReleases();
                         $select = $categorySelect->select();
                         $select->where('NR_ID = ?', $newsletterID);
                         $categoryData = $categorySelect->fetchRow($select);
                         
                         $categoryData = $categoryData->toArray();
                         if($categoryData){
                            $categories = explode(",",$recipientDetails['newsletter_categories']);
                            $searchID   = array_search($categoryData['NR_CategoryID'],$categories,true);
                            
                            $categories[$searchID] = "";
                            $categories = array_filter($categories, "is_numeric");
                            $val = implode(',',$categories);
                            
                            $profile->updateMember($recipientID,array("newsletter_categories"=>"$val"));                                  
                         }
                     }
                     $this->_redirect($return);    
                }
            }
        }    
    }
?>
