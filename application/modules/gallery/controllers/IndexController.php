<?php

class Gallery_IndexController extends Cible_Controller_Action
{
    /**
    * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function siteMapAction(){
        $newsRob = new GalleryRobots();
        $dataXml = $newsRob->getXMLFile($this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);
    }

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('gallery.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('gallery.css'), 'all');
    }

    public function indexAction()
    {

    }

     public function gallerymenuAction(){

        $db  = Zend_Registry::get('db');
        $gallerySelect = new GalleriesIndex();
        $select = $db->select()
            ->distinct()
            ->from('ModuleCategoryViewPage')
            ->join('CategoriesIndex', 'CI_CategoryID = MCVP_CategoryID AND CI_LanguageID = '. Zend_Registry::get("languageID"))
            ->where('MCVP_ViewID = 9001');
        $galleryData = $db->fetchAll($select);
        $arrayLink = array();

        foreach ($galleryData as $gallery){
            $link = Cible_FunctionsPages::getPageLinkByID($gallery['MCVP_PageID']);
            array_push($arrayLink,array('ID'=>$gallery['MCVP_PageID'],'title'=>$gallery['CI_Title'],'link'=>$link));
        }

        $pageParentID = Cible_FunctionsPages::findParentPageID(Zend_Registry::get("pageID"));
        $parentID = 9002;

        if($pageParentID['P_ParentID']==9016){
            $parentID = 9009;
        }
        $selectedMenuItem = Cible_FunctionsPages::findMenuID(10,$parentID);

        $this->view->assign('parentID',$pageParentID['P_ParentID']);

        $this->view->assign('menuSelectedItem',$selectedMenuItem['MII_MenuItemDataID']);
        $this->view->assign('arrayLinks',$arrayLink);

    }

    public function detailsAction()
    {
        $numberOfImagesPerPage = 5;
        $numberOfImagesPerColumn = 4;
        $gallery = new GalleryObject();

        $blockID = $this->_getParam('BlockID');
        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        $regularDetailsShow = true;
        if(isset($blockParameters[1]['P_Value'])){
            if($blockParameters[1]['P_Number']!=999){
                if($blockParameters[1]['P_Value']!=0){
                    $regularDetailsShow = false;
                }
            }
       }
        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView(9001,'list');
        $listall_page = $this->view->baseUrl() . "/" . $listall_page;
        $this->view->assign('listall_page_gallery', $listall_page);


       if($regularDetailsShow==false){
            $id = 0;
            $idGallery =  $blockParameters[1]['P_Value'];
            $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($this->_request->getPathInfo(),true);

            $baseDir = $this->view->baseUrl();
            $config  = Zend_Registry::get('config')->toArray();
            $imageId = $idGallery;
           // $this->view->headLink()->appendStylesheet($baseDir . '/themes/default/css/prettyPhoto.css', 'screen');

            $galleryID     = $idGallery;
            $db            = Zend_Registry::get('db');
            $gallerySelect = new GalleriesIndex();
            $select = $db->select()
                ->distinct()
                ->from('GalleriesIndex')
                ->joinRight('Galleries', 'GI_GalleryID = G_ID AND GI_LanguageID = '. Zend_Registry::get("languageID"))
                    ->where('GI_GalleryID = ?', $galleryID);
            $galleryData = $db->fetchRow($select);

            //var_dump($galleryData);
            if (count($galleryData) > 0)
            {
                $this->view->headTitle($galleryData['GI_Title']);
                $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
                $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
                $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];

                $imagesSelect = new GalleriesImages();
                $select = $imagesSelect->select()->setIntegrityCheck(false)
                        ->from('Galleries_Images')
                        ->join('Images', 'I_ID = GI_ImageID')
                        ->joinLeft('ImagesIndex', 'II_ImageID = I_ID AND II_LanguageID = '. Zend_Registry::get("languageID"))
                        ->where('GI_GalleryID = ?', $galleryID)
                        ->where('GI_Online = 1')
                        ->order('GI_Position');

                $imagesData = $imagesSelect->fetchAll($select)->toArray();
                $i = 0;
                foreach ($imagesData as $image)
                {
                    $imagesData[$i]['thumbName'] = str_replace($image['I_OriginalLink'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);
                    $imagesData[$i]['originalName'] = str_replace($image['I_OriginalLink'], $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);
                    $i++;
                }
                $this->view->assign('galleryData', $galleryData);
                $this->view->assign('imagesData', $imagesData);
                $paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Array($imagesData));
                $paginator->setItemCountPerPage($numberOfImagesPerPage);

                $pageNum = $this->_request->getParam('page');
                if($pageNum==""){
                    $pageNum = Cible_FunctionsGeneral::getPageNumberWithoutParamOrder($this->_request->getPathInfo());
                }
                //var_dump($paginator);

                $paginator->setCurrentPageNumber($pageNum);
                $this->view->assign('paginator', $paginator);
                $this->view->assign('numberItemPerColumn',$numberOfImagesPerColumn);
                $this->view->assign('showTitleGalerry',"false");
            }
        }
        else{
            $id = 0;
            $idGallery =  0;
            $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($this->_request->getPathInfo(),true);
            if($titleUrl!=""){
                $idGallery = $gallery->getIdByName($titleUrl);
            }
            if(empty($idGallery)){
                $idGallery = $titleUrl;
            }



            $baseDir = $this->view->baseUrl();
            $config  = Zend_Registry::get('config')->toArray();
            $imageId = $idGallery;
           // $this->view->headLink()->appendStylesheet($baseDir . '/themes/default/css/prettyPhoto.css', 'screen');

            $galleryID     = $idGallery;
            $db            = Zend_Registry::get('db');
            $gallerySelect = new GalleriesIndex();
            $select = $db->select()
                ->distinct()
                ->from('GalleriesIndex')
                ->joinRight('Galleries', 'GI_GalleryID = G_ID AND GI_LanguageID = '. Zend_Registry::get("languageID"))
                    ->where('GI_GalleryID = ?', $galleryID);
            $galleryData = $db->fetchRow($select);
            //echo $select;
            //var_dump($galleryData);
            if (count($galleryData) > 0)
            {
                $this->view->headTitle($galleryData['GI_Title']);
                $originalMaxHeight = $config['gallery']['image']['original']['maxHeight'];
                $originalMaxWidth = $config['gallery']['image']['original']['maxWidth'];
                $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
                $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];

                $imagesSelect = new GalleriesImages();
                $select = $imagesSelect->select()->setIntegrityCheck(false)
                        ->from('Galleries_Images')
                        ->join('Images', 'I_ID = GI_ImageID')
                        ->joinLeft('ImagesIndex', 'II_ImageID = I_ID AND II_LanguageID = '. Zend_Registry::get("languageID"))
                        ->where('GI_GalleryID = ?', $galleryID)
                        ->where('GI_Online = 1')
                        ->order('GI_Position');

                $imagesData = $imagesSelect->fetchAll($select)->toArray();
                $i = 0;
                foreach ($imagesData as $image)
                {
                    $imagesData[$i]['thumbName'] = str_replace($image['I_OriginalLink'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);
                    $imagesData[$i]['originalName'] = str_replace($image['I_OriginalLink'], $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $image['I_OriginalLink'], $image['I_OriginalLink']);
                    $i++;
                }
                $this->view->assign('galleryData', $galleryData);
                $this->view->assign('imagesData', $imagesData);
                $paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Array($imagesData));
                $paginator->setItemCountPerPage($numberOfImagesPerPage);

                $pageNum = $this->_request->getParam('page');
                if($pageNum==""){
                    $pageNum = Cible_FunctionsGeneral::getPageNumberWithoutParamOrder($this->_request->getPathInfo());
                }
                //var_dump($paginator);
                $paginator->setCurrentPageNumber($pageNum);
                $this->view->assign('paginator', $paginator);
                $this->view->assign('numberItemPerColumn',$numberOfImagesPerColumn);
                $this->view->assign('showTitleGalerry',"true");




                 $galleriesSelect = new Galleries();
                 $select = $galleriesSelect->select()->setIntegrityCheck(false)
                    ->from('Galleries')
                    ->where('G_ID = ?', $galleryID);

                $row = $galleriesSelect->fetchRow($select)->toArray();

                 $categoryID = $row['G_CategoryID'];



                $select = $galleriesSelect->select()->setIntegrityCheck(false)
                    ->from('Galleries')
                    ->join('GalleriesIndex', 'GI_GalleryID = G_ID AND GI_LanguageID = ' . Zend_Registry::get("languageID"))
                    ->join('Images', 'I_ID = G_ImageID')
                    ->where('G_CategoryID = ?', $categoryID)
                    ->where('G_Online = 1')
                    ->order('G_Position');

                $galleriesData = $galleriesSelect->fetchAll($select)->toArray();



                $details_page = Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details', 9);

                $this->view->assign('details_page', $this->view->baseUrl() . "/" . $details_page);

                $this->view->assign('galleriesData', $galleriesData);
                $menuGallery = $this->view->MenuGallery($categoryID,$details_page);
                $view = $this->getHelper('ViewRenderer')->view;
                $view->assign('menuGallery', $menuGallery);
                $view->assign('galleryID', $galleryID);
                $view->assign('class', "menuLeftGallery");

                $html = $view->render('partials/menu.phtml');
                $this->view->assign('menuGalleries', $html);


            }
        }
    }

    public function listAction()
    {
        $blockID = $this->_getParam('BlockID');
        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        $categoryID = $blockParameters[0]['P_Value'];

        $galleriesSelect = new Galleries();
        $select = $galleriesSelect->select()->setIntegrityCheck(false)
            ->from('Galleries')
            ->join('GalleriesIndex', 'GI_GalleryID = G_ID AND GI_LanguageID = ' . Zend_Registry::get("languageID"))
            ->join('Images', 'I_ID = G_ImageID')
            ->where('G_CategoryID = ?', $categoryID)
            ->where('G_Online = 1')
            ->order('G_Position');
        $galleriesData = $galleriesSelect->fetchAll($select)->toArray();

        $config = Zend_Registry::get('config')->toArray();
        $thumbMaxHeight = $config['gallery']['image']['thumb']['maxHeight'];
        $thumbMaxWidth = $config['gallery']['image']['thumb']['maxWidth'];
        $mediumMaxHeight = $config['gallery']['image']['original']['maxHeight'];
        $mediumMaxWidth = $config['gallery']['image']['original']['maxWidth'];

        $i = 0;
        foreach ($galleriesData as $gallery)
        {
            $galleriesData[$i]['I_ThumbLink'] = str_replace($gallery['I_OriginalLink'], $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $gallery['I_OriginalLink'], $gallery['I_OriginalLink']);
            $galleriesData[$i]['I_MediumLink'] = str_replace($gallery['I_OriginalLink'], $mediumMaxWidth . 'x' . $mediumMaxHeight . '_' . $gallery['I_OriginalLink'], $gallery['I_OriginalLink']);

            $galleriesData[$i]['linkDetails'] = Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details', 9);
            $i++;
        }
       // var_dump($galleriesData);
        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details', 9);
        $this->view->assign('details_page', $this->view->baseUrl() . "/" . $details_page);


        $this->view->assign('galleriesData', $galleriesData);
        $menuGallery = $this->view->MenuGallery($categoryID,$details_page);
        $view = $this->getHelper('ViewRenderer')->view;
        $view->assign('menuGallery', $menuGallery);
        $view->assign('galleryID', -1);
        $view->assign('class', "menuLeftGallery");
        $html = $view->render('partials/menu.phtml');
        $this->view->assign('menuGalleries', $html);
    }

    public function addAction()
    {

    }

    public function editAction()
    {

    }

    public function deleteAction()
    {

    }

}
?>