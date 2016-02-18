<?php

class Banners_IndexController extends Cible_Controller_Action {

    protected $_labelSuffix;
    protected $_colTitle = array();
    protected $_moduleID = 18;
    protected $_defaultAction = 'list';
    protected $_moduleTitle = 'banners';
    protected $_name = 'index';
    protected $_ID = 'id';
    protected $_currentAction = '';
    protected $_actionKey = '';
    protected $_imageSrc = 'BII_Filename';
    protected $_imagesFolder;
    protected $_rootImgPath;
    protected $_rootVideoPath;
    protected $_formName = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'list-group' => 'GroupObject',
        'list-images' => 'ImageObject'
    );
    protected $_actionsList = array();
    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF = false;
    protected $_disableExportToCSV = false;
    protected $_enablePrint = false;

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init() {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('banners.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('banners.css'), 'all');

        $this->_rootVideoPath = $this->_filesFolder . "videos/";

        $this->view->cleaction = $this->_name;
        $this->_imgIndex = 'imagefeat';
    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function indexAction() {
        $blockID = $this->_request->getParam('BlockID');



        if ($blockID) {
            $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.min.js', 'jquery'));

            $this->_blockID = $blockID;
            $params = Cible_FunctionsBlocks::getBlockParameters($blockID);
            $groupId;
            $autoPlay;
            $delais;
            $transition;
            $navi;
            $effect;
            foreach ($params as $param)
                $blockParams[$param['P_Number']] = $param['P_Value'];

            switch ($blockParams[6]) {
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

            $groupId = $blockParams[1];
            $autoPlay = $blockParams[2];
            $delais = $blockParams[3];
            $transition = $blockParams[4];
            $navi = $blockParams[5];
            $effect = $blockParams[6];

            $groupCollection = new GroupObject();
            $this->view->bannersGroup = $groupCollection->groupCollection($groupId);
            $this->view->bannerRenderer($groupId, $autoPlay, $delais, $transition, $navi, $effect);
        }
    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    public function featuredAction() {
        $this->_addSubFolder = true;
        $this->_currentAction = 'featured';
        parent::init();

        $langId = Zend_Registry::get('languageID');
        $blockID = $this->_request->getParam('BlockID');

        if ($blockID) {
            $this->_blockID = $blockID;
            $params = Cible_FunctionsBlocks::getBlockParameters($blockID);

            $groupId = 0;
            $autoPlay = 0;
            $delais = 0;
            $transition = 0;
            $navi = 0;
            $effect = 0;

            foreach ($params as $param) {
                $blockParams[$param['P_Number']] = $param['P_Value'];
            }

            $videos = new VideoObject();
            $listVideo = array();
            $listVideo = $videos->getVideosList();

            $groupId = str_replace('_f', '', $blockParams[1]);
            $this->view->autoPlay = $blockParams[2];
            $this->view->delais = $blockParams[3];
            $this->view->transition = $blockParams[4];
            $this->view->navi = $blockParams[5];
            $this->view->effect = $blockParams[6];

            $oBannerFeat = new BannerFeaturedObject();
            $oImageFeat = new BannerFeaturedImageObject();

            $banner = $oBannerFeat->populate($groupId, $langId);
            $imgBanner = $oImageFeat->getData($langId, $groupId);

            $config = Zend_Registry::get('config');
            $cfgBanner = $config->banners->imagefeat->toArray();

            $this->view->imgCfg = $cfgBanner;
            $this->view->imgFeat = $imgBanner;
            $this->view->imgPath = $this->_rootImgPath . $groupId . '/';

            $this->view->videoPath = $this->_rootVideoPath;



            //$this->view->listVideos = $listVideo;
        }
    }

    /**
     * Display the list
     *
     *
     *
     * @return void
     */
    /*
      public function featured($request)
      {
      $langId  = Zend_Registry::get('languageID');
      $blockID = $request->getParam('BlockID');

      if ($blockID)
      {
      $this->_blockID = $blockID;
      $params = Cible_FunctionsBlocks::getBlockParameters($blockID);

      $groupId = 0;
      $autoPlay = 0;
      $delais = 0;
      $transition = 0;
      $navi = 0;
      $effect = 0;

      foreach ($params as $param)
      {
      $blockParams[$param['P_Number']] = $param['P_Value'];
      }

      $groupId = str_replace('_f', '', $blockParams[1]);
      $this->view->autoPlay = $blockParams[2];
      $this->view->delais = $blockParams[3];
      $this->view->transition = $blockParams[4];
      $this->view->navi = $blockParams[5];
      $this->view->effect = $blockParams[6];

      $oBannerFeat = new BannerFeaturedObject();
      $oImageFeat  = new BannerFeaturedImageObject();

      $banner    = $oBannerFeat->populate($groupId, $langId);
      $imgBanner = $oImageFeat->getData($langId, $groupId);

      $config    = Zend_Registry::get('config');
      $cfgBanner = $config->banners->imagefeat->toArray();

      $this->view->imgCfg  = $cfgBanner;
      $this->view->imgFeat = $imgBanner;
      $this->view->imgPath = $this->_imagesFolder . 'featured/' . $groupId . '/';

      }
      } */
}
