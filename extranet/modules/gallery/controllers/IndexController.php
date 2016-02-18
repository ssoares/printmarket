<?php

class Gallery_IndexController extends Cible_Controller_Categorie_Action {

    protected $_moduleID = 9;
    protected $_defaultAction = 'list';
    protected $_categoryID = 0;

    public function init($options = null)
    {
        parent::init();
        if($this->_getParam('catID'))
            $this->_categoryID = $this->_getParam('catID');
    }
    public function listAction() {
        $this->view->title = "Liste des galleries";
        if ($this->view->aclIsAllowed('gallery','manage',true)){
        // variables
        $this->view->params = $this->_getAllParams();
        $blockID = $this->_getParam('blockID');
        $pageID = $this->_getParam('pageID');
        $baseDir = $this->view->baseUrl();

        $base = substr($baseDir, 0, strpos($baseDir, "/{$this->_config->document_root}/"));

        if ($blockID <> '') {
            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
            $this->_categoryID = $blockParameters[0]['P_Value'];
        }


        $this->view->headScript()->appendFile($baseDir . '/js/jquery.json-1.3.min.js');
        $this->view->headScript()->appendFile($baseDir . '/js/tiny_mce/plugins/imagemanager/js/mcimagemanager.js');

        //get category details
        $this->view->categoryDetails = Cible_FunctionsCategories::getCategoryDetails($this->_categoryID);

        // get all galleries
        $galleriesSelect = new Galleries();
        $select = $galleriesSelect->select()->setIntegrityCheck(false)
                ->from('Galleries')
                ->join('GalleriesIndex', 'G_ID = GI_GalleryID')
                ->join('Images', 'G_ImageID = I_ID')
                ->where('GI_LanguageID = ?', $this->_defaultEditLanguage)
                ->order('G_Position')
                ->order('GI_Title');

        if ($this->_categoryID > 0)
            $select->where('G_CategoryID = ?', $this->_categoryID);

        $galleriesData = $galleriesSelect->fetchAll($select);
        $this->view->galleriesData = $galleriesData->toArray();

        $this->view->imagesAjaxLink = "$baseDir/gallery/index/list-all-images";

        if ($blockID <> '')
            $this->view->imagesEditLink = "$baseDir/gallery/image/edit/blockID/$blockID";
        else
            $this->view->imagesEditLink = "$baseDir/gallery/image/edit";

        $this->view->imagesDeleteLink = "$baseDir/gallery/image/delete";
        /*
          $galleriesImages = new GalleriesImages();
          $select = $galleriesImages->select()->setIntegrityCheck(false)
          ->from('Galleries_Images')
          ->join('ImagesIndex', 'II_ImageID = GI_ImageID')
          ->where('GI_GalleryID = ?', $galleryID);

          $ImagesData = $galleriesImages->fetchAll($select);
         */


        }
    }

    public function listAllImagesAction() {
        $galleryID = $_REQUEST['galleryID'];


        $imagesSelect = new GalleriesImages();
        $select = $imagesSelect->select()->setIntegrityCheck(false)
                ->from('Galleries_Images')
                ->join('ImagesIndex', 'II_ImageID = GI_ImageID')
                ->join('Images', 'I_ID = II_ImageID')
                ->where('GI_GalleryID = ?', $galleryID)
                ->order('GI_Position')
                ->group('II_ImageID');
        $imagesData = $imagesSelect->fetchAll($select)->toArray();

        $this->view->imagesData = $imagesData;
        $config = Zend_Registry::get('config')->toArray();
        $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
        $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
        $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
        $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];

        if (count($imagesData) > 0) {
            $i = 0;
            foreach ($imagesData as $image) {
                $imagesData[$i]['II_Title'] = $image['II_Title'];
                $imagesData[$i]['II_Description'] = $image['II_Description'];
                $imagesData[$i]['I_OriginalLink'] = str_replace($image['I_OriginalLink'], $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);
                $imagesData[$i]['I_ThumbLink'] = str_replace($image['I_OriginalLink'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);

                $i++;
            }
        }
        echo json_encode($imagesData);
    }

    public function addAction() {

        $dir = "../../{$this->_config->document_root}/data/images/gallery/tmp";
        //echo $dir;
        if(!file_exists($dir)){
            mkdir($dir);
        }
        $this->view->title = "Ajout d'une galerie";
        //if ($this->view->aclIsAllowed('gallery','manage',true)){
        // variables
        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $pageID = $this->_getParam('pageID');
        $blockID = $this->_getParam('blockID');
        $baseDir = $this->view->baseUrl();

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($formData['ImageSrc'] <> '')
                $imageSrc = Zend_Registry::get("www_root") . "/data/images/gallery/tmp/mcith/mcith_" . $formData['ImageSrc'];
            else
                $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
        }
        else {
            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
        }
        // generate the form
        if ($blockID <> '')
            $cancelUrl = "$baseDir/gallery/index/list/blockID/$blockID/pageID/$pageID";
        elseif ($this->_categoryID <> '')
            $cancelUrl = "$baseDir/gallery/index/list/catID/$this->_categoryID";
        else
            $cancelUrl = "$baseDir/gallery/index/list/";

        $form = new FormGallery(array(
                'baseDir' => $baseDir,
                'cancelUrl' => '',
                'galleryID' => '',
                'imageSrc' => $imageSrc,
                'isNewImage' => 'true',
                'addAction' => true,
                'requiredTitle' => true,
                'requiredPosition' => false,
                'requiredDescription' => false
            ));

        $this->view->form = $form;

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {
                $formData['G_CreationDate'] = date("Y-m-d");
                $formData['G_ImageID'] = '0';

                if ($blockID <> '') {
                    $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                    $formData['G_CategoryID'] = $blockParameters[0]['P_Value'];
                } else {
                    if($this->_categoryID>0)
                        $formData['G_CategoryID'] = $this->_categoryID;
                }

                $galleryObject = new GalleryObject();
                $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['GI_Title']);
                $formData['GI_ValUrl'] = $formattedName;

                $galleryID = $galleryObject->insert($formData, $this->_config->defaultEditLanguage);

                mkdir("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID) or die("Could not make directory");
                mkdir("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/tmp") or die("Could not make directory");


                // create the image
                $imageObject = new ImageObject();
                $formData['II_Title'] = $formData['GI_Title'];
                $formData['II_Description'] = $formData['GI_Description'];
                $imageID = $imageObject->insert($formData, $this->_config->defaultEditLanguage);

                // update the galery
                $galleryData = $galleryObject->populate($galleryID, Zend_Registry::get("currentEditLanguage"));
                $formData['G_CreationDate'] = $galleryData['G_CreationDate'];
                $formData['G_ImageID'] = $imageID;
                $galleryObject->save($galleryID, $formData, Zend_Registry::get("currentEditLanguage"));

                $config = Zend_Registry::get('config')->toArray();

                if($form->getValue('ImageSrc')!=""){

                    $srcOriginal = "../../{$this->_config->document_root}/data/images/gallery/tmp/" . $form->getValue('ImageSrc');
                    $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
                    $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
                    $originalName = str_replace($form->getValue('ImageSrc'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));


                    $srcThumb = "../../{$this->_config->document_root}/data/images/gallery/tmp/mcith/mcith_" . $form->getValue('ImageSrc');
                    $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                    $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];
                    $thumbName = str_replace($form->getValue('ImageSrc'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));

                    Cible_FunctionsImageResampler::resampled(array('src' => $srcOriginal, 'maxWidth' => $originalMaxWidth, 'maxHeight' => $originalMaxHeight));
                    Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                    // Attempts to create the directory specified by pathname.
                    mkdir("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID) or die("Could not make directory");
                    // Rename and move the original image to the specific place
                    rename("../../{$this->_config->document_root}/data/images/gallery/tmp/" . $form->getValue('ImageSrc'), "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID . "/" . $originalName);
                    // Rename and move the thumbnail image to the specific place
                    rename("../../{$this->_config->document_root}/data/images/gallery/tmp/mcith/mcith_" . $form->getValue('ImageSrc'), "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID . "/" . $thumbName);


                }
                if ($formData['G_Online'] == 1 && $blockID <> '') {
                    $indexData['pageID'] = $blockParameters[0]['P_Value'];
                    $indexData['moduleID'] = $this->_moduleID;
                    $indexData['contentID'] = $formData['GI_ValUrl'];
                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                    $indexData['title'] = $formData['GI_Title'];
                    $indexData['text'] = '';
                    $indexData['link'] = 'gallery';
                    $indexData['contents'] = $formData['GI_Title'] . " " . $formData['GI_Description'];
                    $indexData['action'] = 'add';

                    Cible_FunctionsIndexation::indexation($indexData);
                }

                if (!$this->_isXmlHttpRequest) {
                    //$this->_redirect('/');
                    if ($blockID <> '')
                        $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                    elseif ($this->_categoryID <> '')
                        $this->_redirect("/gallery/index/list/catID/$this->_categoryID");
                    else
                        $this->_redirect("/gallery/index/list/");
                }

                else {
                    $buttonAction = $formData['buttonAction'];
                    $this->view->assign('buttonAction', $buttonAction);
                    $this->view->assign('success', true);
                    $this->view->assign('galleryID', $galleryID);
                    $this->view->assign('galleryTitle', $form->getValue('GI_Title'));
                    $this->view->assign('galleryDescription', $form->getValue('GI_Description'));
                    $this->view->assign('galleryDate', $formData['G_CreationDate']);
                }
                //exit;
            } else {
                $form->populate($formData);
            }
        }

        //}
    }

    public function editAction(){

        $this->view->title = "Modification d'une galerie";
        //if ($this->view->aclIsAllowed('gallery','manage',true)){
        // variables
        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $pageID = $this->_getParam('pageID');
        $blockID = $this->_getParam('blockID');
        $galleryID = $this->_getParam('galleryID');

        $baseDir = $this->view->baseUrl();

        $galleryObject = new GalleryObject();
        $galleryData = $galleryObject->populate($galleryID, Zend_Registry::get("currentEditLanguage"));

        $imageObject = new ImageObject();
        $imageData = $imageObject->populate($galleryData['G_ImageID'], Zend_Registry::get("currentEditLanguage"));

        if (!$galleryData) {
            if ($this->_request->isPost()) {
                $this->view->assign('success', true);
            }
            $this->view->assign('deleted', true);
            $this->view->assign('galleryID', $galleryID);
        } else {
            $this->view->assign('deleted', false);

            $config = Zend_Registry::get('config')->toArray();
            $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
            $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];
            $thumbName = str_replace($imageData['ImageSrc'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $imageData['ImageSrc'], $imageData['ImageSrc']);


            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();
                $isNewImage = $formData['isNewImage'];

                if ($isNewImage == 'true')
                    $imageSrc = Zend_Registry::get("www_root") . "/data/images/gallery/" . $galleryID . "/tmp/mcith/mcith_" . $formData['ImageSrc'];
                else
                    $imageSrc = Zend_Registry::get("www_root") . "/data/images/gallery/$galleryID/" . $galleryData['G_ImageID'] . "/" . $thumbName;
            }
            else {
                //$imageData['ImageSrc_preview'] = Zend_Registry::get("www_root")."/data/images/gallery/$galleryID/".$galleryData['G_ImageID']."/".$thumbName;
                $imageSrc = Zend_Registry::get("www_root") . "/data/images/gallery/$galleryID/" . $galleryData['G_ImageID'] . "/" . $thumbName;
                //$this->view->assign('imageUrl', $imageData['ImageSrc_preview']);
                $this->view->assign('imageUrl', $imageSrc);
                $isNewImage = 'false';
                //$imageSrc   = $imageData['ImageSrc_preview'];
            }
            // generate the form
            if ($blockID <> '')
                $cancelUrl = "$baseDir/gallery/index/list/blockID/$blockID/pageID/$pageID";
            elseif ($this->_categoryID <> '')
                $cancelUrl = "$baseDir/gallery/index/list/catID/$this->_categoryID";
            else
                $cancelUrl = "$baseDir/gallery/index/list/";

            $form = new FormGallery(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => '',
                    'galleryID' => $galleryID,
                    'imageSrc' => $imageSrc,
                    'isNewImage' => $isNewImage,
                    'requiredTitle' => true,
                    'requiredPosition' => false,
                    'requiredDescription' => false
                ));

            if ($this->_request->isPost()) {

                $formData = $this->_request->getPost();

                if ($form->isValid($formData)) {


                    $galleryObject = new GalleryObject();
                    $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['GI_Title']);
                    $formData['GI_ValUrl'] = $formattedName;

                    if ($blockID <> '') {
                        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                        $formData['G_CategoryID'] = $blockParameters[0]['P_Value'];
                    } elseif ($this->_categoryID <> '') {
                        $formData['G_CategoryID'] = $this->_categoryID;
                    }

                    if ($formData['isNewImage'] == 'true') {

                        // create the image
                        $imageObject = new ImageObject();
                        $formData['II_Title'] = $formData['GI_Title'];
                        $formData['II_Description'] = $formData['GI_Description'];
                        $imageID = $imageObject->insert($formData, Zend_Registry::get("currentEditLanguage"));

                        // update the galery
                        $formData['G_CreationDate'] = $galleryData['G_CreationDate'];
                        $formData['G_ImageID'] = $imageID;
                        $galleryObject->save($galleryID, $formData, Zend_Registry::get("currentEditLanguage"));

                        $config = Zend_Registry::get('config')->toArray();
                        $srcOriginal = "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/tmp/" . $form->getValue('ImageSrc');
                        $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
                        $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
                        $originalName = str_replace($form->getValue('ImageSrc'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));

                        $srcThumb = "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/tmp/mcith/mcith_" . $form->getValue('ImageSrc');
                        $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];
                        $thumbName = str_replace($form->getValue('ImageSrc'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $form->getValue('ImageSrc'), $form->getValue('ImageSrc'));


                        Cible_FunctionsImageResampler::resampled(array('src' => $srcOriginal, 'maxWidth' => $originalMaxWidth, 'maxHeight' => $originalMaxHeight));
                        Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                        // Attempts to create the directory specified by pathname.
                        mkdir("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID) or die("Could not make directory");
                        // Rename and move the original image to the specific place
                        rename("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/tmp/" . $form->getValue('ImageSrc'), "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID . "/" . $originalName);
                        // Rename and move the thumbnail image to the specific place
                        rename("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/tmp/mcith/mcith_" . $form->getValue('ImageSrc'), "../../{$this->_config->document_root}/data/images/gallery/" . $galleryID . "/" . $imageID . "/" . $thumbName);
                    } else {
                        $formData['G_CreationDate'] = $galleryData['G_CreationDate'];
                        $formData['G_ImageID'] = $galleryData['G_ImageID'];
                        ;
                        $galleryObject->save($galleryID, $formData, Zend_Registry::get("currentEditLanguage"));
                    }

                    if ($blockID <> '') {
                        $indexData['pageID'] = $blockParameters[0]['P_Value'];
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['contentID'] = $formData['GI_ValUrl'];
                        $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                        $indexData['title'] = $formData['GI_Title'];
                        $indexData['text'] = '';
                        $indexData['link'] = 'gallery';
                        $indexData['contents'] = $formData['GI_Title'] . " " . $formData['GI_Description'];

                        if ($formData['G_Online'] == 1)
                            $indexData['action'] = 'update';
                        else
                            $indexData['action'] = 'delete';

                        Cible_FunctionsIndexation::indexation($indexData);
                    }

                    $imageSelect = new GalleriesImages();
                    $select = $imageSelect->select()->setIntegrityCheck(false)
                            ->from('Galleries_Images')
                            ->where('GI_GalleryID = ?', $galleryID)
                            ->join('ImagesIndex', 'II_ImageID = GI_ImageID')
                            ->where('II_LanguageID = ?', Zend_Registry::get("currentEditLanguage"));

                    $imageData = $imageSelect->fetchAll($select)->toArray();
                    $cpt = count($imageData);

                    for ($i = 0; $i < $cpt; $i++) {
                        $indexData['pageID'] = $galleryID;
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['contentID'] = $formData['GI_ValUrl'];
                        $indexData['languageID'] = $imageData[$i]['II_LanguageID'];
                        $indexData['title'] = $imageData[$i]['II_Title'];
                        $indexData['text'] = '';
                        $indexData['link'] = 'image';
                        $indexData['contents'] = $imageData[$i]['II_Title'] . " " . $imageData[$i]['II_Description'];

                        if ($formData['G_Online'] == 1) {
                            $indexData['action'] = 'update';
                            if ($imageData[$i]['GI_Online'] == 1)
                                Cible_FunctionsIndexation::indexation($indexData);
                        }
                        else {
                            $indexData['action'] = 'delete';
                            Cible_FunctionsIndexation::indexation($indexData);
                        }
                    }

                    if (!$this->_isXmlHttpRequest) {
                        if ($blockID <> '')
                            $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                        else
                            $this->_redirect("/gallery/index/list/catID/$this->_categoryID");
                    }
                    else {
                        $buttonAction = $formData['buttonAction'];
                        $this->view->assign('buttonAction', $buttonAction);
                        $this->view->assign('success', true);
                        $this->view->assign('galleryID', $galleryID);
                        $this->view->assign('galleryTitle', $form->getValue('GI_Title'));
                        $this->view->assign('galleryDescription', $form->getValue('GI_Description'));
                        $this->view->assign('galleryLanguage', Zend_Registry::get("currentEditLanguage"));
                    }
                } else {
                    $this->view->form = $form;
                }
            } else {
                $form->populate($galleryData);
                $form->populate($imageData);
                $this->view->form = $form;
            }
        }
        //}
    }

    public function deleteAction() {
        // web page title
        $this->view->title = "Suppression d'une galerie";

        //if ($this->view->aclIsAllowed('gallery','manage',true)){
        // variables
        $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
        $this->view->assign('success', false);

        $pageID = (int) $this->_getParam('pageID');
        $blockID = (int) $this->_getParam('blockID');
        $galleryID = (int) $this->_getParam('galleryID');

        if ($blockID <> '')
            $return = "/gallery/index/list/blockID/$blockID/pageID/$pageID";
        elseif ($this->_categoryID)
            $return = "/gallery/index/list/catID/$this->_categoryID";
        else
            $return = "/gallery/index/list/";


        $this->view->return = $this->view->baseUrl() . $return;

        $galleryObject = new GalleryObject();
        $galleryDataIndex = $galleryObject->populate($galleryID, $this->_defaultEditLanguage);
        if (!$galleryDataIndex) {
            if ($this->_request->isPost()) {
                $this->view->assign('success', true);
            }
            $this->view->assign('deleted', true);
            $this->view->assign('galleryID', $galleryID);
        } else {
            $this->view->assign('deleted', false);
            $this->view->gallery = $galleryDataIndex;

            if ($this->_request->isPost()) {
                $formData = $this->_request->getPost();
                if ($galleryDataIndex) {
                    $gallerySelect = new GalleriesIndex();
                    $select = $gallerySelect->select()
                            ->where('GI_GalleryID = ?', $galleryID);
                    $galleryData = $gallerySelect->fetchAll($select);

                    foreach ($galleryData as $gallery) {
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['contentID'] = $gallery['GI_ValUrl'];
                        $indexData['languageID'] = $gallery['GI_LanguageID'];
                        $indexData['action'] = 'delete';
                        Cible_FunctionsIndexation::indexation($indexData);

                        $imageSelect = new GalleriesImages();
                        $select = $imageSelect->select()->setIntegrityCheck(false)
                                ->from('Galleries_Images')
                                ->where('GI_GalleryID = ?', $galleryID)
                                ->join('ImagesIndex', 'II_ImageID = GI_ImageID')
                                ->where('II_LanguageID = ?', $gallery['GI_LanguageID']);

                        $imageData = $imageSelect->fetchAll($select)->toArray();
                        $cpt = count($imageData);

                        for ($i = 0; $i < $cpt; $i++) {
                            $indexData['moduleID'] = $this->_moduleID;
                            $indexData['contentID'] = $gallery['GI_ValUrl'];
                            $indexData['languageID'] = $imageData[$i]['II_LanguageID'];
                            $indexData['action'] = 'delete';
                            Cible_FunctionsIndexation::indexation($indexData);
                        }
                    }

                    //delete all images associated with the gallery
                    $galleryImagesSelect = new GalleriesImages();
                    $select = $galleryImagesSelect->select()
                            ->where('GI_GalleryID = ?', $galleryID);
                    $galleryImagesData = $galleryImagesSelect->fetchAll($select);

                    foreach ($galleryImagesData as $galleryImages) {
                        $imageDelete = new Images();
                        $where = "I_ID = " . $galleryImages['GI_ImageID'];
                        $imageDelete->delete($where);

                        $imageIndexDelete = new ImagesIndex();
                        $where = "II_ImageID = " . $galleryImages['GI_ImageID'];
                        $imageIndexDelete->delete($where);
                    }

                    $galleryImagesDelete = new GalleriesImages();
                    $where = "GI_GalleryID = " . $galleryID;
                    $galleryImagesDelete->delete($where);

                    $galleryObject->delete($galleryID);
                    /*
                      //delete the gallery
                      $galleryDelete = new Galleries();
                      $where = 'G_ID = ' .  $galleryID;
                      $galleryDelete->delete($where);

                      $galleryIndexDelete = new GalleriesIndex();
                      $where = 'GI_GalleryID = ' .  $galleryID;
                      $galleryIndexDelete->delete($where);
                     */

                    //delete the gallery folder
                    Cible_FunctionsGeneral::delFolder("../../{$this->_config->document_root}/data/images/gallery/" . $galleryID);


                    if (!$this->_isXmlHttpRequest) {

                        if ($blockID <> ''){
                            $this->_redirect("/gallery/index/list/blockID/$blockID/pageID/$pageID");
                        }
                        elseif ($this->_categoryID){
                            $this->_redirect("/gallery/index/list/catID/$this->_categoryID");
                        }
                        else{
                            $this->_redirect("/gallery/index/list/");
                        }
                    }
                    else
                    {
                        $buttonAction = $formData['buttonAction'];
                        $this->view->assign('success', true);
                        $this->view->assign('buttonAction', $buttonAction);
                        $this->view->assign('galleryID', $galleryID);
                        $this->view->assign('deleted', true);
                    }
                }
            }
        }
        //}
    }

    public function deleteCategoriesAction()
    {

        if ($this->view->aclIsAllowed($this->view->current_module, 'edit'))
        {
            $id = $this->_getParam('ID');

            if ($this->_request->isPost() && isset($_POST['delete']))
            {

                $this->_db->delete('Categories', "C_ID = '$id'");
                $this->_db->delete('CategoriesIndex', "CI_CategoryID = '$id'");

                $this->_redirect("/gallery/index/list-categories/");
            }
            else if ($this->_request->isPost() && isset($_POST['cancel']))
            {
                $this->_redirect('/gallery/index/list-categories/');
            }
            else
            {
                $fails = false;

                $select = $this->_db->select();
                $select->from('CategoriesIndex', array('CI_Title'))
                    ->where('CategoriesIndex.CI_CategoryID = ?', $id);

                $categoryName = $this->_db->fetchOne($select);

                $this->view->assign('category_id', $id);
                $this->view->assign('category_name', $categoryName);

                $select = $this->_db->select();
                $select->from('Galleries')
                    ->where('Galleries.G_CategoryID = ?', $id);

                $result = $this->_db->fetchAll($select);

                if ($result)
                {
                    $fails = true;
                }

                if (!$fails)
                {
                    $select = $this->_db->select();
                    $select->from('Blocks')
                        ->joinRight('Parameters', 'Parameters.P_BlockID = Blocks.B_ID')
                        ->where('Parameters.P_Number = ?', 1)
                        ->where('Parameters.P_Value = ?', $id)
                        ->where('Blocks.B_ModuleID = ?', $this->_moduleID);

                    $result = $this->_db->fetchAll($select);

                    if ($result)
                    {
                        $fails = true;
                    }
                }

                $this->_db->delete('ModuleCategoryViewPage', $this->_db->quoteInto('MCVP_CategoryID = ?', $id));

                $this->view->assign('module_name', $this->_moduleName);
                $this->view->assign('module_id', $this->_moduleID);
                $this->view->assign('returnUrl', '/gallery/index/list-categories/');
                $this->view->assign('fails', $fails);
            }
        }
    }

}
