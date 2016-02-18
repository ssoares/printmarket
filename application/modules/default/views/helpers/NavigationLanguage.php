<?php
/**
* Build navigation languages dynamically
*
* The system will find links to other languages of the current page to allow to change the language on the same page. 
* If no other language is related to the current page, links to other languages leads to the reception
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

class Zend_View_Helper_NavigationLanguage
{
    /**
    * Principal function to build the language navigation
    *
    * 1- Get all links of the current page into other languages than the current one
    * 2- If no links to other language for the current page, show links to the home page
    * 3- Return the result to the main view
    *
    * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
    */
    public function navigationLanguage()
    {
        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        
        // get all links to the current page in another language
        $Pages = new PagesIndex();
        $Select = $Pages->select()->setIntegrityCheck(false);
        $Select->from('PagesIndex');
        $Select->join('Languages', 'Languages.L_ID = PagesIndex.PI_LanguageID');
        $Select->where('PagesIndex.PI_LanguageID <> ?', Zend_Registry::get("languageID"));
        $Select->where('PagesIndex.PI_PageID = ?', Zend_Registry::get("pageID"));
        $Select->where('PagesIndex.PI_Status = ?', 'en ligne');
        $Select->order('Languages.L_Title ASC');
            
        $Rows = $Pages->fetchAll($Select);
        
        // build the language navigation to display
        $navigationlangue = "<ul class='navigationlanguage'>"; 
        if ($Rows->count() > 0){
            foreach ($Rows as $Row){
                $navigationlangue .= "<li><a href='".$baseUrl."/".$Row['PI_PageIndex']."'>".$Row['L_Title']."</a></li>";  
            }
        }
        // if no links to other language for the current page
        else{
            // get all links to the home page in another language
            $Languages = Zend_Registry::get("db");
            $Select = $Languages->select()
                            ->from('Languages')
                            ->join('PagesIndex', 'PagesIndex.PI_LanguageID = Languages.L_ID')
                            ->where('Languages.L_ID <> ?', Zend_Registry::get("languageID"))
                            ->where('PagesIndex.PI_PageID = ?', '0')
                            ->where('PagesIndex.PI_Status = ?', 'en ligne')            
                            ->order('Languages.L_Title');
            $Rows = $Languages->fetchAll($Select);
            foreach ($Rows as $Row){
                $navigationlangue .= "<li><a href='".$_baseUrl."/".$Row['PI_PageIndex']."'>".$Row['L_Title']."</a></li>";
            }
        }
        $navigationlangue .= "</ul>";    
        
        return $navigationlangue;
    }
}
