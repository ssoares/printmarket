<?php

class Sitemap_IndexController extends Cible_Controller_Action
{
    protected $_menusLayout = array(
        'main' => array(),
        'corpo' => array(),
        'recreo' => array()
        );

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('sitemap.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('sitemap.css'), 'all');
    }

    public function indexAction()
    {
        $tmp        = array();
        $arrayData  = array();
        $groupMenu  = '';
        $layoutFile = Cible_FunctionsPages::getLayoutPath($this->view->currentPageID);

        foreach ($this->_menusLayout as $key => $value)
        {
            if (preg_match('/' . $key . '/', $layoutFile))
                $groupMenu = $key;
        }
        $menusList  = MenuObject::getAllMenuList($groupMenu, true);

        $this->_menusLayout[$groupMenu] = $menusList;
        $oMenu = new MenuObject();
        foreach ($this->_menusLayout[$groupMenu] as $menuId)
        {
            $oMenu->initMenu($menuId);
            $oMenu->setIsSiteMap(true);

            if (!empty ($tmp)){
                $arrayData = $this->appendIfNotFound($oMenu->populate(), $tmp, $arrayData);
            }else{
                $arrayData = $oMenu->populate();
            }
            $tmp = $this->verifyChildren($arrayData);
        }

        $this->view->assign('menus', $arrayData);

    }

    private function verifyChildren($children, $tmp = array())
    {
        foreach($children as $child)
        {
            $index = '';
            if($child['PageID'] != -1)
            {
                $index = Cible_FunctionsPages::getPageNameByID($child['PageID']);
            }
            else
            {
                $index = $child['Link'];
            }

            if(!empty($index) && !isset($tmp[$index]))
            {
                $tmp[$index] = '';
            }

            if(isset($child['child']))
            {
                $tmp = $this->verifyChildren($child['child'], $tmp);
            }
        }

        return $tmp;
    }

    private function appendIfNotFound($children, $tmp, $appendTo = array())
    {
        foreach($children as $child)
        {
            $index = '';
            if($child['PageID'] != -1)
            {
                $index = Cible_FunctionsPages::getPageNameByID($child['PageID']);
            }
            else
            {
                $index = $child['Link'];
            }

            if(!empty($index) && !isset($tmp[$index]) && ($child['MID_Show_Sitemap']==1))
            {
                $tmp[$index] = '';
                array_push($appendTo, array(
                    'ID'    => $child['ID'],
                    'Title' => strip_tags($child['Title']),
                    'Link'  => $child['Link'],
                    'PageID' => $child['PageID'],
                    'Style' => $child['Style'],
                    'Placeholder' => $child['Placeholder']
                ));
                if (!empty($child['child']))
                {
                    $index = count($appendTo) - 1;
                    $appendTo[$index]['child'] = $child['child'];
                }
            }
        }

        return $appendTo;
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        echo $url;
    }

}
