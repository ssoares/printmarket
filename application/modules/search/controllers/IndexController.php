<?php

class Search_IndexController extends Cible_Controller_Action
{
    protected $_searchCount = 0;

    public function indexAction()
    {
        $searchParams = $this->_getAllParams();
        if (!isset($searchParams['words']))
            $searchParams['words'] = '';

        $action = $this->_getParam('action');
        $searchParams['currentSite'] = $this->view->currentSite;
        if (empty($searchParams['sites']))
            $searchParams['sites'] = $this->view->currentSite;
        if ($action === 'advanced')
        {
            $this->view->headTitle($this->view->getCibleText('recherche_avancee'));
            $this->view->pageTitle($this->view->getCibleText('recherche_avancee'));
            $form = new FormAdvanceSearch();
            if ($this->_request->isPost())
            {
                $searchParams = $this->_request->getPost();
                $searchParams['currentSite'] = $this->view->currentSite;
                $form->populate($searchParams);
            }
            else
                $form->populate($searchParams);

            $this->view->form = $form;
        }
        $searchType = 0;
        if (!empty($searchParams['searchOption']))
            $searchType = $searchParams['searchOption'];
        $this->view->assign('words',($searchParams['words']));
        $this->view->assign('searchType',$searchType);
        $languageID = $lang = Zend_Registry::get('languageID');
        $db = Zend_Registry::get("db");
        $searchResults = array();

        $searchCount = 0;
        try
        {
            $searchResult = Cible_FunctionsIndexation::indexationSearch($searchParams);
        }
        catch(Exception $exc)
        {
            $searchResult = array();
        }

        $dbs = Zend_Registry::get('dbs');
        $defaultAdapter = $dbs->getDb();
        foreach($searchResult as $key => $result)
        {
            $dbAdapter = $dbs->getDb($key);
            Zend_Registry::set('db', $dbAdapter);
            $searchResults[$key] = $this->_getFinalSearchResults($result);
            $this->view->$key = $this->_config->domainNames->$key;
            $searchCount += $this->_searchCount;
        }
        Zend_Registry::set('db', $defaultAdapter);
        $this->view->assign('searchResults', $searchResults);

        $this->view->assign('searchCount', $searchCount);
    }

    private function _getFinalSearchResults($searchResult)
    {
        $searchResults = array();
        $languageID = $lang = Zend_Registry::get('languageID');
        $db = Zend_Registry::get("db");
        Zend_Registry::set('pageIDArray', array());
        foreach ($searchResult as $key => $result)
        {
            switch ((int)$result['moduleID'])
            {
                case 0:
                case 1:
                    $oMod = new TextObject();
                    $module = 'text';
                    $label = $this->view->getCibleText('treeview_contents_management_title');
                    break;
                case 999:
                    $oMod = null;
                    $module = 'pdf';
                    $label = $this->view->getCibleText('pdf_files');
                    break;

                default:
                    $module = Cible_FunctionsModules::getModuleNameByID((int)$result['moduleID']);
                    $objName = ucfirst($module) . 'Object';
                    $oMod = new $objName();
                    $label = $this->view->getCibleText($module . '_module_name');
                    break;
            }
            if ($result['languageID'] == $languageID && !is_null($oMod))
            {
                $tmp = $oMod->getIndexationData($result);
                if (!empty($tmp))
                {
                    if (isset($tmp[1]))
                    {
                        foreach ($tmp as $vals)
                        {
                            $searchResults[$module][] = $vals;
                            $this->_searchCount++;
                        }
                    }
                    else
                    {
                        $searchResults[$module][] = $tmp;
                        $this->_searchCount++;
                    }

                    $searchResults[$module]['label'] = $label;
                }

                unset($searchResult[$key]);
            }
            elseif(is_null($oMod))
            {
                $searchResults[$module]['label'] = $label;

                $searchResults[$module][] = $result;
                $this->_searchCount++;
            }

        }

        return $searchResults;
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        echo $url;
    }

}
