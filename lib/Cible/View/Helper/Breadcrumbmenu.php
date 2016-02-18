<?php

class Cible_View_Helper_Breadcrumbmenu extends Zend_View_Helper_Abstract
{

    public function breadcrumbmenu($options)
    {
        $selectedItemMenuID = 0;
        $menuTitle = isset($options['menuTitle']) ? $options['menuTitle'] : '';
        $showHome = isset($options['showHome']) ? $options['showHome'] : true;
        if (isset($options['separator'])) {
            $options['separator'] = $this->view->image($options['separator']);
        } else {
            $options['separator'] = '>';
        }
        if (Zend_Registry::isRegistered('selectedItemMenuID')){
            $selectedItemMenuID = Zend_Registry::get('selectedItemMenuID');
        }else{
            $menu = Cible_FunctionsPages::getMenuByPageId($this->view->currentPageID, null, false);
            if (!empty($menu) && count($menu) > 1){
                foreach ($menu as $values)
                {
                    if ($values['MID_MenuID'] == 10){
                        $selectedItemMenuID = $values['MID_ID'];
                    }
                }
            }else{
                if(isset($menu[0])){
                    $selectedItemMenuID = $menu[0]['MID_ID'];
                }
            }
        }

        if (isset($options['showHomeImg']))
            $options['homeIcon'] = $this->view->image($options['showHomeImg']);
        else if (isset($options['showHomeClass'])) {
            $options['homeIcon'] = $options['showHomeClass'];
        }
        $module = '';
        if (Zend_Registry::isRegistered('module')){
            $module = Zend_Registry::get('module');
        }
        $addPageTitle = false;
        if (class_exists('CatalogCollection', 0) && $module == CatalogCollection::MODULE_NAME){
            $breadcrumb = $this->view->breadcrumbCatalog(1, $showHome, null, $options);
        }else{
            $breadcrumb =  Cible_FunctionsPages::buildClientBreadcrumbMenu($selectedItemMenuID, 1, $menuTitle, $showHome, null, $options);
            $addPageTitle = true;
        }

        $linkHome = '';
        if($showHome){
            $homeDetails = Cible_FunctionsPages::getHomePageDetails();
            if (isset($options['showHomeImg'])) {
                $linkHome = "<a href='{$this->view->baseUrl()}/{$homeDetails['PI_PageIndex']}' class='first'>" . $options['homeIcon'] . "</a>";
            } else if (isset($options['showHomeClass'])) {
                $linkHome = "<a href='{$this->view->baseUrl()}/{$homeDetails['PI_PageIndex']}' class='first {$options['showHomeClass']}'>" . $homeDetails['PI_PageTitle'] . "</a>";
            } else {
                $linkHome = "<a href='{$this->view->baseUrl()}/{$homeDetails['PI_PageIndex']}' class=''>" . $homeDetails['PI_PageTitle'] . "</a>";
            }
            array_push($breadcrumb, $linkHome);
        }

        $breadcrumb = array_reverse($breadcrumb);

        // add the > after the breadcrumb when only on item is found
        $return = '';
        if( count($breadcrumb) == 1 && !empty($breadcrumb[0]) ){
            $return = $breadcrumb[0];
            $return .= $addPageTitle ? " {$options['separator']} " : '';
        }else{
            $return = implode( " {$options['separator']} ", $breadcrumb);
        }
        if ($addPageTitle)
            $return .= $this->view->pageTitle()->toString(null, null, true);

        return $return;
    }

}
