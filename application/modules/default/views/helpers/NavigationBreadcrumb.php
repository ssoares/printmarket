<?php
/**
* Build navigation breadcrumb dynamically
*
* The system finds all the parents of the current page to build the breadcrumb. 
* If the current page is a main page (not a child of another page), the breadcrumb will not be built.
*
* PHP versions 5
*
* LICENSE: 
*
* @category   Views Helpers
* @package    Default
* @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
* @copyright  2009 CIBLE Solutions d'Affaires
* @license    http://www.ciblesolutions.com
* @version    CVS: <?php $ ?> Id:$
*/

class Zend_View_Helper_NavigationBreadcrumb
{
    /**
    * Principal function to build the breadcrumb navigation
    *
    * 1- Call recursive function to build the breadcrumb
    * 2- If the current page is a main page(not a child of another page), do not display the breadcrumb 
    * 3- Return the result to the main view
    *
    * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
    */
    public function navigationBreadcrumb()
    {
        // call recursive function to build the breadcrumb
        $breadcrumb = $this->findParentsPage(Zend_Registry::get("pageID"));
        if ($breadcrumb <> ""){
            $breadcrumb = "<div class='navigationbreadcrumb'>".$breadcrumb."</div>";
        }
        
        return $breadcrumb;
    }
    
    /**
    * Recursive function that find all parents of the current page and build the breadcrumb navigation
    *
    * 1- Get information related to the requested page
    * 2- If the page requested is a child of another page, called the function again to find the parent of that child.
    * 2.1- Keep the information of the current record to be displayed in the breadcrumb 
    * 3-If the displayed page is a main page(not a child of another page), do not display the link to that page 
    *
    * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
    */
    public function findParentsPage($PageID)
    {
        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        // get information related to the requested page
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select()
                        ->from('PagesIndex')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->where('Pages.P_ID = ?', $PageID)
                        ->where('PagesIndex.PI_LanguageID = ?', Zend_Registry::get("languageID"));
        
        $Row = $Pages->fetchRow($Select);
        $breadcrumb = "";
        
        // if the page is a child, call again the function to find the parent and keep the information of the current page
        if ($Row['P_ParentID'] <> 0){
            $breadcrumb .= $this->findParentsPage($Row['P_ParentID']);
            $breadcrumb .= "&nbsp;&gt;&nbsp;";
            $breadcrumb .= "<a href='".$_baseUrl."/".$Row['PI_PageIndex']."'>".$Row['PI_PageTitle']."</a>";                                                      
        }
        
        // do not displayed the link if the asked page is a main page
        if ($Row['P_ParentID'] == 0 && $Row['P_ID'] <> Zend_Registry::get("pageID")){
            $breadcrumb .= "<a href='".$_baseUrl."/".$Row['PI_PageIndex']."'>".$Row['PI_PageTitle']."</a>";    
        }
        
        return  $breadcrumb;
    }
}
