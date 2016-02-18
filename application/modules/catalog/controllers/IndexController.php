<?php
/**
 * Cible Solutions
 *
 *
 * @category  Modules
 * @package   Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: IndexController.php 1867 2016-02-08 19:29:11Z freynolds $
 */

/**
 * Catalog index controller
 * Manage actions to display catalog.
 *
 * @category  Modules
 * @package   Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class Catalog_IndexController extends Cible_Controller_Action
{
    protected $_moduleID      = 14;
    protected $_defaultAction = 'list';
    protected $_moduleTitle   = 'catalog';
    protected $_name          = 'index';
    protected $_filterFields  = array();

    /**
    * Overwrite the function define in the SiteMapInterface implement Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function siteMapAction(){
        $newsRob = new CatalogRobots();
        $dataXml = $newsRob->getXMLFile($this->_request->getParam('lang'));
    
        parent::siteMapAction($dataXml);
    }

    public function init()
    {
        Zend_Registry::set('module', $this->_moduleTitle);
        $Action = $this->getRequest()->getActionName();
        parent::init();

        if($Action!="site-map"){
            $langId = Zend_Registry::get('languageID');
            $this->setModuleId();
            $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('catalog.css'), 'all');
            $this->view->headLink()->appendStylesheet($this->view->locateFile('catalog.css'), 'all');
            $searchForm = new FormSearchCatalog();
            $this->view->formSearch = $searchForm;
            $this->_filterFields = $searchForm->getElementsId();
            $this->view->filterFields = $this->_filterFields;
            Zend_Registry::set('filterFields', $this->_filterFields);

        }
        else{
            $this->_helper->viewRenderer->setNoRender(true);
            $this->view->layout()->disableLayout();
        }

       
    }
    /**
     * List products according given parameters.
     * This list is only for display purpose. No actions except Excel export.
     *
     * @return void
     */
    public function listAction()
    {
        $img = $this->_getParam('img');
        if (!empty($img))
        {
            $this->downloadAction();
            exit;
        }

        $this->view->params['exclude'] = $this->_filterFields;
        $this->view->params['actions'] = $this->_request->getPathInfo();
        /* List products */
        $oProducts = new CatalogCollection($this->view->params);
        $oProducts->setDataByName();
        $filterData = $this->_getFilterData();
        if (!empty($filterData)){
            $oProducts->setProdId(0);
            $form = $this->view->formSearch;
            $filters = $form->populate($filterData)->getValues();
            $oProducts->setParameters($filters);
        }
        $products = $oProducts->getList();
        $this->view->kwDelimiter = $oProducts->getKwDelimiter();
//        $searchCount = count($products);

        /* Params */
        $blockParams = $oProducts->getBlockParams();
        $categorieId = $oProducts->getCatId();
        $productId   = $oProducts->getProdId();

        $url = $this->view->absolute_web_root
                 . ltrim($this->getRequest()->getPathInfo(), '/');
        Cible_View_Helper_LastVisited::saveThis($url);
        if (!$categorieId && !empty($blockParams[1])){
            $categorieId = $blockParams[1];
        }
        $this->view->isSkuList = false;
        if (!$productId)
        {
//            $searchWords = (isset($this->view->params['keywords']) && $this->view->params['keywords'] != $this->view->getCibleText('form_search_catalog_keywords_label')) ? $this->view->params['keywords'] : '';

            /* Search form */
    //        $searchForm = new FormSearchCatalogue(
    //            array(
    //                'categorieId'   => $categorieId,
    //                'subCategoryId' => $subCategoryId,
    //                'keywords'      => $searchWords)
    //            );
    //
    //        $this->view->assign('searchForm', $searchForm);
//            $oCategory = new CatalogCategoriesObject();
            if ($categorieId > 0){
                $oCategory = $oProducts->getOCategory();

                $category  = $oCategory->populate($categorieId, $this->_registry->languageID);
                $oCategory->getDataCatagory($this->_registry->languageID, false, $categorieId);
                $stringUrl = implode('/', $oCategory->setCategoriesLink()->getLink());
                $this->view->assign('stringUrl', $stringUrl);
                $this->view->assign('title', $category['CCI_Name']);
            }
            if ($blockParams[2] == 2 && $blockParams[3] == 2){
                $this->view->isSkuList = true;
            }
            $lastSearch = array();
            if(!empty ($searchWords))
                $lastSearch['keywords'] = $searchWords;

            $this->view->assign('searchUrl', $lastSearch);

            $page = 1;
            $paginator = new Zend_Paginator( new Zend_Paginator_Adapter_Array( $products ) );
            $paginator->setItemCountPerPage( $oProducts->getLimit() );

//            $filter    = $oProducts->getFilter();
            $paramPage = $this->_request->getParam('page');
            $page      = (isset($paramPage)) ? $this->_request->getParam('page') : ceil($page/$paginator->getItemCountPerPage());

            $paginator->setCurrentPageNumber($page);

            $this->view->assign('categoryId', $categorieId);

            $this->view->assign('params', $oProducts->getBlockParams());
            $this->view->assign('paginator', $paginator);

//            $this->view->assign('keywords', $searchWords);
//            $this->view->assign('searchCount', $searchCount);
//            $this->view->assign('filter', $filter);

            if(isset($category['CCI_ValUrl']))
                echo $this->_registry->set('selectedCatalogPage', $category['CCI_ValUrl']);

            $this->renderScript('index/' . $oProducts->getType());

        }
        else
        {
            $this->view->headScript()->appendFile($this->view->locateFile('jsAddToCart.js'));
            $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.min.js', 'jquery'));
            $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.swipe.min.js', 'jquery'));
//            $this->_registry->set('category', $this->_registry->get('catId_'));
//            $this->_registry->set('productCase','1');
            $url = $this->view->absolute_web_root
                 . $this->getRequest()->getPathInfo();
            Cible_View_Helper_LastVisited::saveThis($url);
            $this->_registry->set('selectedCatalogPage', $products['data']['CCI_ValUrl']);
            $this->view->imgProductPath = $this->_rootImgPath . 'products/';
            $this->view->assign('productDetails', $products);
            $this->view->assign('nbRelated', $this->_config->catalog->nbRelated);
            $this->renderScript('index/detail-product.phtml');
        }
    }


    public function detailproductAction()
    {
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.min.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.cycle2.swipe.min.js', 'jquery'));
            $this->listAction ();
    }

    public function downloadAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->view->layout()->disableLayout();

        $img = $this->_getParam('img');

        // Process the file
        $config = Zend_Registry::get('config');
        $width  = $config->catalog->image->original->maxWidth;
        $height = $config->catalog->image->original->maxHeight;

        $prefix = $width . 'x' . $height . '_';
        $filename  = $prefix . $img;
        $file = Zend_Registry::get('document_root') . '/data/images/catalog/products/' . $this->_getParam('pid') .'/'. $filename;

        if (file_exists($file)){

            $this->getResponse()
             ->setHeader('Content-Disposition', 'attachment; filename='.$filename)
             ->setHeader('Content-Length', filesize($file));

            $this->getResponse()->sendHeaders();
            readfile($file);
            exit;
        }
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');

        $oProducts = new CatalogCollection();
        $oProducts->setActions($this->_request->getPathInfo());
        $oProducts->setDataByName();
        $categorieId = $oProducts->getCatId();
        $productId = $oProducts->getProdId();
        $urltmp = str_replace($this->view->BaseUrl(), '', $url);
        $tmpArray = explode('/', $urltmp);
        if ($categorieId)
            $tmpArray = $this->_getCategoriesForUrl($tmpArray);

        if ($productId)
        {
            $tmpArray = $this->_getCategoriesForUrl($tmpArray);
            $oProd = new ProductsObject();
            $data = $oProd->populate($productId, $lang);
            if (!empty($data['PI_ValUrl']))
                $tmpArray[$this->_tmpId] = $data['PI_ValUrl'];
        }

        $url = $this->view->BaseUrl() . implode('/', $tmpArray);

        echo $url;
    }

    private function _getFilterData()
    {
        $filterData = array();
        $uri = explode('/', $this->_request->getRequestUri());
        foreach($uri as $key => $value){
            if (in_array($value, $this->_filterFields)){
                $filterData[$value] = urldecode($uri[$key+1]);
            }
        }

        return $filterData;
    }

    private function _getCategoriesForUrl($tmpArray = array())
    {
        $lang = $this->_getParam('lang');
        $oCat = new CatalogCategoriesObject();
        $listParams = array_slice($tmpArray, 2, null, true);
        foreach ($listParams as $key => $value)
        {
            $id = $oCat->getIdByName($value);
            if (!empty($id))
            {
                $data = $oCat->populate($id, $lang);
                if (!empty($data['CCI_ValUrl']))
                    $tmpArray[$key] = $data['CCI_ValUrl'];
            }
            $this->_tmpId = $key;
        }

        return $tmpArray;
    }

    public function ajaxAction()
    {
        $action = $this->_getParam('actionAjax');
        $this->disableView();
        $value = $this->_getParam('term');
        $list = array();

        switch ($action) {
            case 'kwlist':
                $category = (int)$this->_getParam('category');
                $params['actions'] = $this->_request->getPathInfo();
                $oProducts = new CatalogCollection($params);
                $list = $oProducts->getKeywordsByCategory($category, $value);
                echo json_encode($list);
               break;
            default:
               break;
        }
    }
}