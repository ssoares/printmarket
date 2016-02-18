<?php
/**
* Building the tree
*
* The system returns the tree generated
*
* PHP versions 5
*
* LICENSE:
*
* @category   Controller
* @package    Default
* @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
* @copyright  2009 CIBLE Solutions d'Affaires
* @license    http://www.ciblesolutions.com
* @version    CVS: <?php $ ?> Id:$
*/
class Cible_View_Helper_GetTreeView
{
    /**
    * Principal function to build the tree
    *
    * 1- Get all the main pages (pages that are not associated with a parent)
    * 2- Call a recursive function to get all the child pages
    * 3- Return the result to display
    *
    * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
    */
    public function getTreeView($mode = 'edit')
    {

        // if mode isn't manage, then only call pages for edit content (indexController)
        $controller = $mode == 'edit' ? 'index' : 'manage';

        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        // get all first level page (parentid = 0)
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select()
                        ->from('PagesIndex')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->where('PagesIndex.PI_LanguageID = ?', Zend_Registry::get("languageID"))
                        ->where('Pages.P_ParentID = ?', '0')
                        ->order('Pages.P_Position');

        $Rows = $Pages->fetchAll($Select);

        // build the tree to display
        $menu  = "<ul class='navigation'>";

        $menu .= "<li><h1>www.ciblesolutions.com</h1><ul>";
        foreach($Rows as $Row){
            $menu .= "<li>
                        <a href='".$_baseUrl."/page/".$controller."/index/ID/".$Row['PI_PageID']."'>".$Row['PI_PageTitle']."</a>
                        ";
            // get all childrens of the page
            $menu .=  $this->findChildrensPage($Row['P_ID'], $controller);
            $menu .= "</li>";
        }

        $menu .= "</ul></li></ul>";

        return $menu;

    }

    /**
    * Recursive function that find all children's page of a parent page
    *
    * 1- Get all the children's page (pages that are associated with the parentID)
    * 2- Call the function again to find the children's children recursively
    * 3- Return the result to the previous call
    *
    * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
    */
    public function findChildrensPage($ParentID, $controller)
    {
        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        // get all childrens associated with the parentID
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select()
                        ->from('PagesIndex')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->where('Pages.P_ParentID = ?', $ParentID)
                        ->where('PagesIndex.PI_LanguageID = ?', Zend_Registry::get("languageID"))
                        ->where('Pages.P_ParentID <> ?', '0')
                        ->order('Pages.P_Position');

        $Rows = $Pages->fetchAll($Select);

        // continue to build the tree...
        $menu = "";
        if(count($Rows) > 0){
               $menu  = "<ul>";
               foreach($Rows as $Row){
                   $menu .= "<li>
                                <a href='".$_baseUrl."/page/".$controller."/index/ID/".$Row['PI_PageID']."'>".$Row['PI_PageTitle']."</a>";
                   // get all childrens of the children
                   $menu .=  $this->findChildrensPage($Row['P_ID'], $controller);
                   $menu .= "</li>";
               }
               $menu .= "</ul>";
        }
        return $menu;
    }
}