<?php

class Cible_View_Helper_BuildCatalogMenu extends Zend_View_Helper_Abstract
{

    public function buildCatalogMenu($options = array())
    {
        $menuCatalogId = 0;
        $defaultCat = '';
        $oMenu = new MenuObject();
        if (isset ($options['menu']))
        {
            $oMenu->initMenu($options['menu']);
            $menuCatalog = $oMenu->getMenuItemByPageId( null, 'catalog');
//            if ($this->view->currentPageID)
//            {
//                $parentPage = Cible_FunctionsPages::findParentPageID($this->view->currentPageID);
//                while ($parentPage['P_ParentID'] != 0 )
//                {
//                   $parentPage = Cible_FunctionsPages::findParentPageID($parentPage['P_ParentID']);
//                }
//
//                $pageId      = $parentPage['P_ID'];
//                $menuCatalog = $oMenu->getMenuItemByPageId($pageId);
//            }
//            else
//            {
//                $menuCatalog = $oMenu->getMenuItemByPageId();
                $parentPage  = Cible_FunctionsPages::findParentPageID($menuCatalog['MII_PageID']);
//            }
        }
        if ($this->view->controller != $parentPage['PI_PageIndex'])
            $link = $parentPage['PI_PageIndex'] . "/";
        else
            $link = $this->view->selectedPage . "/";

        $tree   = array();
        $langId = Zend_Registry::get('languageID');

        $oCategories = new CatalogCategoriesObject();

        if(Zend_Registry::isRegistered('defaultCategory')
            && !is_null(Zend_Registry::get('defaultCategory')))
        {
            $defaultCatId = Zend_Registry::get('defaultCategory');
            $tmpCat       = $oCategories->populate($defaultCatId, $langId);
            if(empty ($tmpCat['CI_ValUrl']))
                $tmpCat['CI_ValUrl'] = "";
            $defaultCat   = $tmpCat['CI_ValUrl'];
        }

        $categories     = $oCategories->getAll($langId);
        $oSubCategories = new SubCategoriesObject();
        $catalog = Array(
            'ID'     => $menuCatalog['MID_ID'],
            'Title'  => $menuCatalog['MII_Title'],
            'PageID' => '',
            'Link'   => $link . $defaultCat,
            'Placeholder' => 0,
            'child' => array()
        );

        foreach ($categories as $category)
        {
            $childs  = array();
            $id      = $category['C_ID'];
            $name    = Cible_FunctionsGeneral::formatValueForUrl($category['CI_Name']);
            $linkCat = $link . $name;

            $menu['ID']          = $category['C_ID'];
            $menu['Title']       = $category['CI_Name'];
            $menu['PageID']      = '';
            $menu['Link']        = $linkCat;
            $menu['Placeholder'] = '2';

            $subCategories = $oSubCategories->getSubCatByCategory($id, true, $langId);
            if ($options['nesting'] > 1)
            {
                foreach ($subCategories as $subCat)
                {
                    $name = "/" . Cible_FunctionsGeneral::formatValueForUrl($subCat['SCI_Name']);
                    $linkSubCat = $linkCat . $name;

                    $child['ID']          = $subCat['SC_ID'];
                    $child['Title']       = $subCat['SCI_Name'];
                    $child['PageID']      = '';
                    $child['Link']        = $linkSubCat;
                    $child['Placeholder'] = '2';

                    $childs[] = $child;
                    $name     = '';
                }
            }
            $menu['child']      = $childs;
            $catalog['child'][] = $menu;

        }

        $oMenu->initMenu($options['menu']);
        $first = $oMenu->populate($menuCatalog['MID_ID']);
        $childCombined = array();

        if (isset($options['merge']) && $options['merge'])
        {
            $childCombined = array_merge($catalog['child'], $first);
            $catalog['child'] = $childCombined;
        }


        $tree[] = $catalog;

        return $tree;
    }
}