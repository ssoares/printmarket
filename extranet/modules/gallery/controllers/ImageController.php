<?php
    class Gallery_ImageController extends Cible_Extranet_Controller_Module_Action{
        protected $_moduleID = 9;


        function indexAction(){
            // not implemented
        }

        function addAction(){
            $this->view->title = "Ajout d'une image à une galerie";
            //if ($this->view->aclIsAllowed('gallery','manage',true)){
                $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
                $this->view->assign('success', false);

                $pageID         = $this->_getParam('pageID');
                $blockID        = $this->_getParam('blockID');
                $galleryID      = $this->_getParam('galleryID');
                $baseDir        = $this->view->baseUrl();

                $galleryObject = new GalleryObject();
                $galleryData = $galleryObject->populate($galleryID, $this->_defaultEditLanguage);

                if ($this->_request->isPost()) {
                    $formData = $this->_request->getPost();

                    if($formData['ImageSrc'] <> '')
                        $imageSrc   = Zend_Registry::get("www_root")."/data/images/gallery/$galleryID/tmp/mcith/mcith_".$formData['ImageSrc'];
                    else
                        $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                }
                else{
                    $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                }

                if(!$galleryData){
                    if ($this->_request->isPost()) {
                        $this->view->assign('success', true);
                    }
                    $this->view->assign('deleted', true);
                    $this->view->assign('galleryID', $galleryID);
                }
                else{
                    $this->view->assign('deleted', false);

                    $cancelUrl =  "/gallery/index/list/blockID/$blockID/pageID/$pageID";
                    $form = new FormImage(array(
                        'baseDir'   => $baseDir,
                        'cancelUrl' => "",
                        'galleryID' => $galleryID,
                        'imageID' => 0,
                        'addAction' => true,
                        'ImageSrc' => "",
                        'ImagePreview' => ""
                    ));

                    $this->view->form = $form;
                    if ($this->_request->isPost()){
                        $formData = $this->_request->getPost();
                        if ($form->isValid($formData)) {
                            if($form->getValue('ImageSrc') == ""){
                                $form->getElement('ImageSrc')->addError('Vous devez choisir une image');
                            }
                            else{
                                $imageObject = new ImageObject();
                                //$this->view->dump($formData); die();
                                $imageID = $imageObject->insert($formData, $this->_config->defaultEditLanguage);

                                $galleryImage = new GalleriesImages();
                                $galleryImageData = $galleryImage->createRow();
                                $galleryImageData->GI_GalleryID     = $galleryID;
                                $galleryImageData->GI_ImageID       = $imageID;
                                $galleryImageData->GI_Online        = $form->getValue('GI_Online');
                                $galleryImageData->GI_Position      = $form->getValue('GI_Position');
                                //$galleryImageData->GI_LinkOriginal  = $form->getValue('ImageSrc');
                                $galleryImageData->save();

                                $config = Zend_Registry::get('config')->toArray();

                                $srcOriginal    = "../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/tmp/".$form->getValue('ImageSrc');
                                $originalMaxHeight  = $config['gallery']['image']['original']['maxHeight'];
                                $originalMaxWidth   = $config['gallery']['image']['original']['maxWidth'];
                                $originalName       = str_replace($form->getValue('ImageSrc'),$originalMaxWidth.'x'.$originalMaxHeight.'_'.$form->getValue('ImageSrc'),$form->getValue('ImageSrc'));

                                $srcThumb       = "../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/tmp/mcith/mcith_".$form->getValue('ImageSrc');
                                $thumbMaxHeight  = $config['gallery']['image']['thumb']['maxHeight'];
                                $thumbMaxWidth   = $config['gallery']['image']['thumb']['maxWidth'];
                                $thumbName      = str_replace($form->getValue('ImageSrc'),$thumbMaxWidth.'x'.$thumbMaxHeight.'_'.$form->getValue('ImageSrc'),$form->getValue('ImageSrc'));

                                Cible_FunctionsImageResampler::resampled(array('src'=>$srcOriginal, 'maxWidth'=>$originalMaxWidth, 'maxHeight'=>$originalMaxHeight));
                                Cible_FunctionsImageResampler::resampled(array('src'=>$srcThumb, 'maxWidth'=>$thumbMaxWidth, 'maxHeight'=>$thumbMaxHeight));

                                // Attempts to create the directory specified by pathname.
                                mkdir("../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/".$imageID) or die ("Could not make directory");
                                // Rename and move the original image to the specific place
                                rename("../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/tmp/".$form->getValue('ImageSrc'),"../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/".$imageID."/".$originalName);
                                // Rename and move the thumbnail image to the specific place
                                rename("../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/tmp/mcith/mcith_".$form->getValue('ImageSrc'),"../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/".$imageID."/".$thumbName);

                                if($formData['GI_Online'] == 1 && $galleryData['G_Online'] == 1 &&($blockID>0)){
                                    $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                                    $categoryID = $blockParameters[0]['P_Value'];

                                    $indexData['pageID']    = $categoryID;
                                    $indexData['moduleID']  = $this->_moduleID;
                                    $indexData['contentID'] =  $galleryID;
                                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                                    $indexData['title']     = $formData['II_Title'];
                                    $indexData['text']      = '';
                                    $indexData['link']      = 'image';
                                    $indexData['contents']  = $formData['II_Title'] . " " . $formData['II_Description'];
                                    $indexData['action']    = 'add';

                                    Cible_FunctionsIndexation::indexation($indexData);
                                }

                                if( !$this->_isXmlHttpRequest ){
                                    //$this->_redirect('/');
                                    $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                                }

                                else {
                                    $buttonAction = $formData['buttonAction'];
                                    $this->view->assign('buttonAction', $buttonAction);
                                    $this->view->assign('success', true);
                                    $this->view->assign('galleryID', $galleryID);
                                    $this->view->assign('imageID', $imageID);
                                    $this->view->assign('orginalName', $originalName);
                                    $this->view->assign('thumbName', $thumbName);
                                    $this->view->assign('imageTitle', $form->getValue('II_Title'));
                                }

                            }
                        }
                        else{
                            $form->populate($formData);
                            $form->getElement('ImageSrc_preview')->setAttrib('src', $imageSrc);
                        }
                    }
                    else{
                        $form->getElement('ImageSrc_preview')->setAttrib('src', $imageSrc);
                    }
                }

            //}
        }

        function editAction(){
            $this->view->title = "Modification d'une image";
            //if ($this->view->aclIsAllowed('gallery','manage',true)){
                $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
                $this->view->assign('success', false);

                $pageID         = $this->_getParam('pageID');
                $blockID        = $this->_getParam('blockID');

                $imageID        = $this->_getParam('imageID');
                $baseDir        = $this->view->baseUrl();

                $imageSelect = new ImagesIndex();
                $select = $imageSelect->select()->setIntegrityCheck(false)
                ->from('ImagesIndex')
                ->join('Galleries_Images', 'GI_ImageID = II_ImageID')
                ->where('II_ImageID = ?', $imageID);
                $imageData = $imageSelect->fetchRow($select);

                if(!$imageData){
                    if ($this->_request->isPost()) {
                        $this->view->assign('success', true);
                    }
                    $this->view->assign('deleted', true);
                    $this->view->assign('imageID', $imageID);
                }
                else{
                    $imageObject = new ImageObject();
                    $imageData = $imageObject->populate($imageID,Zend_Registry::get("currentEditLanguage"));

                    $this->view->assign('deleted', false);

                    $galleryImageSelect = new GalleriesImages();
                    $select = $galleryImageSelect->select()
                    ->where('GI_ImageID = ?', $imageID);
                    $galleryImageData = $galleryImageSelect->fetchRow($select);

                    $imageData['GI_Online'] = $galleryImageData['GI_Online'];
                    $imageData['GI_Position'] = $galleryImageData['GI_Position'];

                    
                     // image src.
                    $config = Zend_Registry::get('config')->toArray();
                    $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                    $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];

                    $ImageSrc = "";
                    if (!empty($imageData['ImageSrc'])){
                        $ImageSrc = $baseDir . "/../data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/" . $imageID . "/" . str_replace($imageData['ImageSrc'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $imageData['ImageSrc'], $imageData['ImageSrc']);
                        $this->view->assign('imageUrl', $ImageSrc);
                    }
                    
                    
                    $cancelUrl =  "/gallery/index/list/blockID/$blockID/pageID/$pageID";
                    $form = new FormImage(array(
                        'baseDir'   => $baseDir,
                        //'cancelUrl' => $baseDir.$cancelUrl,
                        'cancelUrl' => "",
                        'galleryID' => $galleryImageData['GI_GalleryID'],
                        'imageID' => $imageID,
                        'ImageSrc' => $imageData['ImageSrc'],
                        'ImagePreview' => $ImageSrc 
                    ));
                    $isNewImage = 'false';
                    if ($this->_request->isPost()) {
                        
                        $formData = $this->_request->getPost();
                        if ($form->isValid($formData)) {
                            
                            if($formData['ImageSrc']<>$imageData['ImageSrc']){
                                if($formData['ImageSrc'] <> ''){
                                    $imageSrc   = "/data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/tmp/mcith/mcith_".$formData['ImageSrc'];
                                }
                                else{
                                    $imageSrc = $this->view->baseUrl()."/icons/image_non_ disponible.jpg";
                                }                                
                                $isNewImage = 'true';
                            }
                            
                            $thumbName = "";
                            if($isNewImage=='true'){
                               
                                $config = Zend_Registry::get('config')->toArray();
                                $srcOriginal = Zend_Registry::get('document_root') . "/data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/tmp/" . $form->getValue('ImageSrc');
                                $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
                                $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
                                $originalName = str_replace($form->getValue('ImageSrc'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));

                                $srcThumb = Zend_Registry::get('document_root') . "/data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/tmp/thumb_{$form->getValue('ImageSrc')}";
                                $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                                $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];
                                $thumbName = str_replace($form->getValue('ImageSrc'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));

                                copy($srcOriginal, $srcThumb);

                                Cible_FunctionsImageResampler::resampled(array('src' => $srcOriginal, 'maxWidth' => $originalMaxWidth, 'maxHeight' => $originalMaxHeight));
                                Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                                rename($srcOriginal, Zend_Registry::get('document_root') . "/data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/" . $imageID . "/" . $originalName);                            
                                rename($srcThumb, Zend_Registry::get('document_root') . "/data/images/gallery/" . $galleryImageData['GI_GalleryID'] . "/" . $imageID . "/" . $thumbName);

                            
                            }
                            $imageObject = new ImageObject();
                            $langID = Zend_Registry::get("currentEditLanguage");
                            $imageObject->save($imageID, $formData, $langID);

                            $galleryImageData['GI_Online'] = $form->getValue('GI_Online');
                            $galleryImageData['GI_Position'] = $form->getValue('GI_Position');
                            $galleryImageData->save();


                            $gallerySelect = new Galleries();
                            $select = $gallerySelect->select()
                            ->where('G_ID = ?', $galleryImageData['GI_GalleryID']);
                            $galleryData = $gallerySelect->fetchRow($select);

                            if($blockID>0){

                            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                            $categoryID = $blockParameters[0]['P_Value'];

                            $indexData['pageID']    = $categoryID;
                            $indexData['moduleID']  = $this->_moduleID;
                            $indexData['contentID'] = $galleryImageData['GI_GalleryID'];
                            $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                            $indexData['title']     = $formData['II_Title'];
                            $indexData['text']      = '';
                            $indexData['link']      = 'image';
                            $indexData['contents']  = $formData['II_Title'] . " " . $formData['II_Description'];

                            if($formData['GI_Online'] == 1 && $galleryData['G_Online'] == 1)
                                $indexData['action'] = 'update';
                            else
                                $indexData['action'] = 'delete';


                            Cible_FunctionsIndexation::indexation($indexData);
                            }


                            if( !$this->_isXmlHttpRequest ){
                              
                                $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                                
                            }
                            else{
                                $buttonAction = $formData['buttonAction'];
                                $this->view->assign('buttonAction', $buttonAction);
                                $this->view->assign('success', true);
                                $this->view->assign('imageID', $imageID);
                                $this->view->assign('imageTitle', $form->getValue('II_Title'));
                                $this->view->assign('imageDescription', $form->getValue('II_Description'));
                                $this->view->assign('thumbName', $thumbName);
                                $this->view->assign('galleryID', $galleryImageData['GI_GalleryID']);
                                
                                
                            }

                        }
                        else{
                            $this->view->form = $form;
                        }
                    }
                    else{
                        $form->populate($imageData);
                        $this->view->form = $form;
                    }
                }

            //}
        }

        function deleteAction(){
            $this->view->title = "Suppression d'une image";
            //if ($this->view->aclIsAllowed('gallery','manage',true)){
                $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
                $this->view->assign('success', false);

                $pageID     = (int)$this->_getParam( 'pageID' );
                $blockID    = (int)$this->_getParam( 'blockID' );
                $galleryID  = (int)$this->_getParam('galleryID');
                $imageID    = (int)$this->_getParam('imageID');


                $imageIndexSelect = new ImagesIndex();
                $select = $imageIndexSelect->select()
                ->where('II_ImageID = ?', $imageID);
                $imageIndexData = $imageIndexSelect->fetchRow($select);

                if(!$imageIndexData){
                    if ($this->_request->isPost()) {
                        $this->view->assign('success', true);
                    }
                    $this->view->assign('deleted', true);
                    $this->view->assign('imageID', $imageID);
                }
                else{
                    $imageObject = new ImageObject();
                    $imageIndexData = $imageObject->populate($imageID,$this->_defaultEditLanguage);

                    $this->view->assign('deleted', false);
                    $return =  "/gallery/index/list/blockID/$blockID/pageID/$pageID";
                    $this->view->return = $this->view->baseUrl() . $return;

                    $this->view->image =  $imageIndexData;
                    if ($this->_request->isPost()) {
                         //$del = $this->_request->getPost('delete');
                         //if ($del && $imageIndexData) {
                         if ($imageIndexData) {
                            $imageSelect = new ImagesIndex();
                            $select = $imageSelect->select()
                            ->where('II_ImageID = ?',$imageID);

                            $imageData = $imageSelect->fetchAll($select)->toArray();
                            $cpt = count($imageData);

                            for($i=0;$i<$cpt; $i++){
                                $indexData['moduleID']  = $this->_moduleID;
                                $indexData['contentID'] = $imageData[$i]['II_ImageID'];
                                $indexData['languageID'] = $imageData[$i]['II_LanguageID'];
                                $indexData['action'] = 'delete';
                                Cible_FunctionsIndexation::indexation($indexData);
                            }

                            $imageObject->delete($imageID);

                            
                            
                            
                            $galleryImageDelete = new GalleriesImages();
                            $where = 'GI_GalleryID = ' . $galleryID . " and GI_ImageID = " . $imageID;
                            $galleryImageDelete->delete($where);

                            //delete the gallery folder
                            Cible_FunctionsGeneral::delFolder("../../{$this->_config->document_root}/data/images/gallery/".$galleryID."/".$imageID);

                            if( !$this->_isXmlHttpRequest ){
                                $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                            }
                            else{
                                $this->view->assign('success', true);
                                $this->view->assign('imageID', $imageID);
                            }

                         }
                         //$this->_redirect($return);
                    }
                }
            //}
        }
    }
?>
