<?php
class News_IndexController extends Cible_Controller_Action
{
    protected $_showBlockTitle = false;
    protected $_homePage = false;
    protected $_moduleID = 2;
    protected $_moduleTitle = 'news';

    /**
     * Overwrite the function defined in the SiteMapInterface implement in Cible_Controller_Action
     *
     * This function return the sitemap specific for this module
     *
     * @access public
     *
     * @return a string containing xml sitemap
     */
    public function siteMapAction()
    {
       
        $newsRob = new NewsRobots();
        $dataXml = $newsRob->getXMLFile($this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);
    }

    public function init()
    {
        Zend_Registry::set('module', $this->_moduleTitle);
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('news.css'),'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('news.css'),'all');
        $this->view->assign('otherData', false);
    }

    public function detailsAction()
    {
        $news = new NewsCollection($this->_blockId);
        $news->setBlock();
        $this->_testDuplicatedContent(true);
        if (!empty($this->_duplicateData))
            $news->setDb($this->_db)
            ->setDuplicateId ($this->_duplicateData['B_DuplicateId'])
            ->setFromSite ($this->_duplicateData['B_FromSite']);
        $completeUrl = $news->setActions($this->_request->getPathInfo())
            ->getDataByName();
        if ($completeUrl ){
            $this->view->newsUrlCampaign = '/'.$news->getActions(1);
            $str = $news->getActions(2);
            $isDate = false;
            if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $str)){
                $isDate = true;
            }
            if (!empty($str) && !$isDate){
                $this->view->newsUrlCampaign .= '/' . $str;
                $news->setCurrentLang($this->_defaultEditLanguage);
            }
        }
        $id = $news->getId();
        $data = $news->getDetails($id);
        if (!empty($data)){
            $newsCategoryDetails = Cible_FunctionsCategories::getCategoryDetails($data[0]['ND_CategoryID']);
            $this->view->assign('newsCategoryDetails', $newsCategoryDetails);
        }else{
            $otherData = (bool)$this->_hasContent($news, $id);
            if ($otherData)
                $this->view->assign('otherData', true);
        }
        $this->_resetDefaultAdapter();
        $listall_page = $this->view->parentPage($this->_blockId);
        $listall_page .= $this->view->newsUrlCampaign;
        $this->view->assign('params', $news->getBlockParams());
        $this->view->assign('news', $data);

        $this->view->assign('listall_page', $listall_page);
    }

    public function homepagelistAction()
    {
        $this->_homePage = true;
        $this->listallAction();
    }

    public function homepagelist1colAction()
    {
        $this->homepagelistAction();
    }

    public function listallAction()
    {
        $detailsPageView = 'details';
        $completeUrl = false;
        $newsObject = new NewsCollection($this->_blockId);
        $block = null;
        if(isset($this->view->params['forceBlock'])
            && $this->view->params['forceBlock']){
            $this->_homePage = true;
            $block = $this->view->params;
            if ($this->view->list['CA_NewsCategory'] > 0){
            $this->view->newsUrlCampaign = '/' . $this->view->list['CA_ID'] . '-'
                . $this->view->list['CAI_ValUrl'];
            }
            $category = $newsObject->setCatDefault()->getCatDefault();
            if (!empty($block[8])){
                $this->view->newsUrlCampaign .= '/' . $block[8];
                $newsObject->setCurrentLang($this->_defaultEditLanguage);
            }
        }else{
            $str = '';
            $isDate = false;
            $category = $newsObject->getBlockParam(1);
            $completeUrl = $newsObject->setActions($this->_request->getPathInfo())
                ->getDataByName();
            $this->view->newsUrlCampaign = $completeUrl ? '/'.$newsObject->getActions(1) : '';
            $str = $newsObject->getActions(2);
            $isDate = false;
            if (preg_match('/[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}/', $str)){
                $isDate = true;
            }
            if (!$isDate && !empty($str)){
                $this->view->newsUrlCampaign .= '/' . $str;
                $newsObject->setCurrentLang($this->_defaultEditLanguage);
            }
        }
        $newsObject->setBlock($block);
        $detailsPaginator = false;

        $limit = $this->_homePage? $newsObject->getBlockParam(2) : null;
        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($category, 'listall', $this->_moduleID, null, true);
        if ($this->_homePage){
            $oPage = new PagesObject();
            $page = $oPage->pageIdByController($listall_page);
            $pageId = $page[$oPage->getDataId()];
        }else{
            $pageId = Zend_Registry::get('pageID');
        }

        $pageDetails = Cible_FunctionsPages::getPageDetails($pageId);
        $childPageDetails = Cible_FunctionsPages::findChildPage($pageDetails['P_ID'])->toArray();

        $oBlock = new BlocksObject();
        foreach($childPageDetails as $childPage)
        {
            $oBlock->setProperties(array('pageId' => $childPage['P_ID']));
            $detailsPageView = $oBlock->getViewByModuleID($this->_moduleID);
            if($detailsPageView == 'detailswithpreviousnext'
                && $pageId == $childPage['P_ID'])
            {
                $detailsPaginator = true;
                break;
            }
        }

        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($category, $detailsPageView, $this->_moduleID, null, true);
        $listall_page .= $this->view->newsUrlCampaign;
        $details_page .= $this->view->newsUrlCampaign;
        $this->view->assign('listall_page', $this->view->baseUrl() . '/' . $listall_page);
        $this->view->assign('details_page', '/' . $details_page);

        if (is_null($block)){
            $this->_testDuplicatedContent(true);
        }
        if (!empty($this->_duplicateData))
            $newsObject->setDb($this->_db)
            ->setDuplicateId ($this->_duplicateData['B_DuplicateId'])
            ->setFromSite ($this->_duplicateData['B_FromSite']);

        $options = $this->_setFilter($newsObject);
        $news = $newsObject->getList($limit, $options['filtre']);

        if(!$news){
            $news = $newsObject->getList($limit, $options['filtre'], Cible_FunctionsCategories::getDefaultCategoryPerModuleID($this->_moduleID));
        }

        if (empty($news))
        {
            $otherData = (bool)$this->_hasContent($newsObject);
            if ($otherData)
                $this->view->assign('otherData', true);
        }
        $this->_resetDefaultAdapter();
        if ($this->_homePage){
            $this->view->assign('news', $news);
        }else{
            $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($news));
            $perPage = $detailsPaginator ? 1 : $newsObject->getBlockParam(2);
            $paginator->setItemCountPerPage($perPage);
            $paginator->setCurrentPageNumber($this->_request->getParam('page'));
            $this->view->assign('paginator', $paginator);
            $this->view->assign('detailsPageWithPaginator', $detailsPaginator);
        }

        $this->view->assign('params', $newsObject->getBlockParams());
    }

    public function detailswithpreviousnextAction()
    {
        $this->listallAction();
        $news = $this->view->paginator->getItem(0);
        $newsCategoryDetails = Cible_FunctionsCategories::getCategoryDetails($news['ND_CategoryID']);
        $this->view->assign('newsCategoryDetails', $newsCategoryDetails);

    }

    public function listall2Action()
    {
        $this->listallAction();
    }

    public function listall3columnsAction()
    {
        $this->listallAction();
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        $obj = new NewsCollection();
        $obj->setActions($url)->getDataByName();
        $path = $obj->getActions();
        $length = count($path);
        if ($length - 1 >= 2)
        {
            $val = $obj->getId();
            if (!is_null($val))
            {
                $valUrl = $obj->getValUrl($val, $lang);
                if($valUrl!=""){
                    $path[$length - 1] = $valUrl;
                    $url = implode('/', $path);
                }
            }
        }
        echo '/' . ltrim($url,'/');
    }

    /**
     * Defines data and build the filter.
     *
     * @param NewsCollection $newsObject The collection data object.
     * @return array
     */
    private function _setFilter($newsObject)
    {
        $datesList = array();
        $options["filtre"] = '';
        $param = $newsObject->getBlockParam('7');
        if ($param)
        {
            $options["filtre"] = $this->_request->getParam('listeFiltre');
            $arraySelect = $newsObject->getFilterArchive();
            $date = 0;
            foreach ($arraySelect as $value)
            {
                if ($value['dates'] > $date)
                    $date = date('Y-m', strtotime($value['dates']));
                $year = date('Y', strtotime($value['dates']));
                $index = date('Y-m', strtotime($value['dates']));
                $valDt = Cible_FunctionsGeneral::dateToString($value['dates'], Cible_FunctionsGeneral::DATE_MONTH_YEAR);
                $datesList[$year][$index] = $valDt . ' ('. $value['nbNews'] .')';

            }

            $options['datesList'] = $datesList;
            if($options['filtre'] == "")
                $options['filtre'] = $date;

            $form = new FormFilterDate($options);
            $this->view->formSelect = $form;
            if($this->_request->isPost())
            {
                 $form->populate($this->_request->getPost());
            }
        }

        return $options;
    }

    private function _hasContent($obj, $id = null)
    {
        $nbValues = 0;
        $langs = Cible_FunctionsGeneral::getAllLanguage();
        $lang = $this->view->languageId;
        foreach ($langs as $lg)
        {
            if ($lg['L_ID'] != $lang)
            {
                $obj->setCurrentLang($lg['L_ID']);
                if (is_null($id))
                    $data = $obj->getList($obj->getBlockParam('2'));
                else
                    $data = $obj->getDetails($id);

                $nbValues += count($data);
            }
        }

        return $nbValues;
    }
}
