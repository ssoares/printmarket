<?php
/**
 * Cible
 *
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Build the breadcrumb for catalog pages
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_BreadcrumbCatalog extends Zend_View_Helper_Abstract
{
    /**
     * Build the breadcrumd for the catalog page.
     *
     * @param int $lang  <Optional> Id of the current language
     *
     * @return string
     */
    public function breadcrumbCatalog($level = 1, $showHome = true, $langId = null, $options = array())
    {

        $_breadcrumb = array();
        $this->first = true;

        if( $langId == null )
            $langId = Zend_Registry::get('languageID');
        $this->langId = $langId;

        $pathInfo  = $this->view->request->getPathInfo();
        $oProducts = new CatalogCollection();
        $oProducts->setActions($pathInfo);
        $oProducts->setDataByName();

        $this->catId = $oProducts->getCatId();
        $this->prodId = $oProducts->getProdId();
        $details = Cible_FunctionsPages::getPageDetails($this->view->currentPageID, $this->langId);
        $pageLink = $this->view->link($this->view->baseUrl() . '/' .$details['PI_PageIndex'],$details['PI_PageTitle']);
        if ($this->catId == null && $this->prodId == null)
        {
            $_breadcrumb[0] = $this->view->pageTitle()->toString(null, null, true);
            return  $_breadcrumb;
        }
        else
        {
            $this->pathElemts = $oProducts->getActions();
            $tmp = array_reverse($this->pathElemts, true);
            foreach( $tmp as $key => $value)
            {
                if ($value != $details['PI_PageIndex']){
                    $link = $this->_getLinks($value);
                    unset($this->pathElemts[$key]);
                    if (!empty($link)){
                        array_push($_breadcrumb, $link);
                    }
                }
            }
            array_push($_breadcrumb, $pageLink);
            return $_breadcrumb;
        }
    }

    private function _getLinks($value)
    {
        if ($this->prodId > 0){
            $link = $this->_getProductLink($value);
            $this->prodId = !empty($link) ? 0 : $this->prodId;
        }else{
            $link = $this->_getCategoriesLink($value);
        }

        return $link;
    }

    private function _getProductLink($value)
    {
        $link = $_class = '';
        $obj = new ProductsObject();
        $testValue = $obj->getIdByName($value);
        if (!empty($testValue)){
            $details = $obj->populate($this->prodId, $this->langId);
            if( $this->first){
                $_class = 'current_page';
                $link = $details[$obj->getTitleField()];
                $this->first = false;

            }else{
                $href = rtrim($this->view->baseUrl()) . '/' . implode('/', $this->pathElemts);
                $link = $this->view->link($href, $details[$obj->getTitleField()]);
            }
        }

        return $link;
    }

    private function _getCategoriesLink($value)
    {
        $_class = '';
        $obj = new CatalogCategoriesObject();
        if( $this->first){
            $details = $obj->populate($this->catId, $this->langId);
            $_class = 'current_page';
            $link = $details[$obj->getTitleField()];
            $this->first = false;
        }else{
            $id = $obj->getIdByName($value);
            $details = $obj->populate($id, $this->langId);
            $href = rtrim($this->view->baseUrl()) . '/' . implode('/', $this->pathElemts);
            $link = $this->view->link($href, $details[$obj->getTitleField()]);
        }

        return $link;
    }
}