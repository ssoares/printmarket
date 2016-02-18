<?php


class Repertoire_IndexController extends Cible_Controller_Action
{

    protected $_showBlockTitle = false;

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
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $repertoireRob = new RepertoireRobots();
        $dataXml = $repertoireRob->getXMLFile($this->_registry->absolute_web_root, $this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);

    }

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('repertoire.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('repertoire.css'), 'all');
    }

    public function detailsAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $repertoire = new RepertoireCollection($_blockID);

        $id = 0;
        $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($this->_request->getPathInfo());

        if ($titleUrl != "")
        {
            $id = $repertoire->getIdByName($titleUrl);
        }
        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoire->getBlockParam('1'), 'listall');
        $this->view->assign('params', $repertoire->getBlockParams());
        $this->view->assign('repertoire', $repertoire->getDetails($id));
        if (!empty($_SERVER['HTTP_REFERER']))
        {
            $this->view->assign('pagePrecedente', $_SERVER['HTTP_REFERER']);
        }
        else
        {
            $this->view->assign('pagePrecedente', '');
        }
        $this->view->assign('listall_page', $listall_page);
    }

    public function entrepriseAction()
    {


        $_blockID = $this->_request->getParam('BlockID');
        $repertoire = new RepertoireCollection($_blockID);

        $id = 0;
        $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($this->_request->getPathInfo());
        if ($titleUrl != "")
        {
            $id = $repertoire->getIdByName($titleUrl);
        }
        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoire->getBlockParam('1'), 'listall');
        $this->view->assign('params', $repertoire->getBlockParams());
        $this->view->assign('repertoire', $repertoire->getDetails($id));
        if (!empty($_SERVER['HTTP_REFERER']))
        {
            $this->view->assign('pagePrecedente', $_SERVER['HTTP_REFERER']);
        }
        else
        {
            $this->view->assign('pagePrecedente', '');
        }
        $this->view->assign('listall_page', $listall_page);
    }

    public function homepagelistAction()
    {
        $_blockID = $this->_request->getParam('BlockID');

        $repertoire = new RepertoireCollection($_blockID);

        $listall_page = '';
        $details_page = '';
//        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoire->getBlockParam('1'), 'listall');
//        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoire->getBlockParam('1'), 'entreprise');
        //exit;
        $this->view->assign('listall_page', $listall_page);
        $this->view->assign('details_page', $details_page);
        $this->view->assign('params', $repertoire->getBlockParams());
        $this->view->assign('repertoire', $repertoire->getList($repertoire->getBlockParam('1')));
    }

    public function listallAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $repertoireObject = new RepertoireCollection($_blockID);
                
        $details_page = '';
        $this->view->assign('details_page', $details_page);
        $options["name"] = "";
        $options["surname"] = "";
        if($this->_request->isPost())
        {
            $options["name"] = $this->_getParam("name");
            $options["surname"] = $this->_getParam("surname");
            $this->view->assign('repertoires', $repertoireObject->getList(null, $options)); 
            $this->view->assign('name',$options["name"]);   
            $this->view->assign('surname',$options["surname"]);
        }
        else{
            $this->view->assign('name',"");   
            $this->view->assign('surname',"");  
        }  
        
        $this->view->assign('params', $repertoireObject->getBlockParams());
    }

    public function listall2columnsAction()
    {
        
        $_blockID = $this->_request->getParam('BlockID');
        $repertoireObject = new RepertoireCollection($_blockID);

        //$details_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoireObject->getBlockParam('1'),'entreprise');
        $details_page = '';
        $this->view->assign('details_page', $details_page);

        if($this->_request->isPost())
        {
            $options["listId"] = $this->_getParam("filterList");
            $options["alpha"] = $this->_getParam("selectedAlpha");

        }
        else
        {
            $options["listId"] = $this->_getParam("listId");
            $options["alpha"] = $this->_getParam("alpha");
        }

        if (empty($options["listId"]))
            $options["listId"] = 1;
//        $arrayLetter = $repertoireObject->getNameFirstLetter($options["listId"]);
//        $options["Letters"] = $arrayLetter;

        $repertoire = $repertoireObject->getList(null, $options);
        /*$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($repertoire));
        $paginator->setItemCountPerPage($repertoireObject->getBlockParam('2'));
        $paginator->setCurrentPageNumber($this->_request->getParam('page'));*/
        $form = new FormFilter($options);

        $oRegion = new RegionObject();
        $descr = $oRegion->getDescription($options["listId"]);
        $this->view->assign('groupDescription',$descr);
        
        $this->view->assign('repertoires',$repertoire);
        $this->view->assign('alpha', $options["alpha"]);
        $this->view->assign('listId', $options["listId"]);
        $this->view->formFilter = $form;        
        $this->view->assign('params', $repertoireObject->getBlockParams());
        
        
       // $this->view->assign('paginator', $paginator);
    }

    public function listall3columnsAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $repertoireObject = new RepertoireCollection($_blockID);
        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($repertoireObject->getBlockParam('1'), 'details');
        $this->view->assign('details_page', $details_page);
        $repertoire = $repertoireObject->getList();
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($repertoire));
        $paginator->setItemCountPerPage($repertoireObject->getBlockParam('2'));
        $paginator->setCurrentPageNumber($this->_request->getParam('page'));
        $this->view->assign('params', $repertoireObject->getBlockParams());
        $this->view->assign('paginator', $paginator);
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        $path = explode('/', $this->_request->getPathInfo());
        array_shift($path);
        array_shift($path);

        if (count($path) > 2)
        {
            $url = str_replace($this->view->BaseUrl(), '', $url);
            $tmpArray = explode('/', $url);
            $obj = new RepertoireCollection();
            $val = $obj->getIdByName($path[2]);
            if (!is_null($val))
            {
                $valUrl = $obj->getValUrl($val, $lang);
                $tmpArray[4] = $valUrl;
            }
            $url = $this->view->BaseUrl() . implode('/', $tmpArray);
        }
        echo $url;
    }

}