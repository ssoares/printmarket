<?php
/*****
 *
 *  Return the string that will be put in the robots.txt
 *
 ****/

class CatalogRobots extends DataObject
{    
    
    protected $_dataClass   = 'CatalogCategoriesData';

    protected $_indexClass      = '';
    protected $_indexLanguageId = '';

    protected $_specificAction = array('list');  // this are the action that need a specific treatment

    public function getXMLFile($lang = ""){
        $path = Zend_Registry::get('absolute_web_root');

        $config = Zend_Registry::get('config');
        $path = $this->_protocol . $config->site->domainsName->$lang; // pour les multidomains

        $moduleID = 14;
        $db = Zend_Registry::get('db');
        $xmlString = "";
        $arrayForXML = array();
        $productCollection = new CatalogCollection(array('lang' => $lang));

        $select = $db->select()
            ->distinct()
            ->from('Blocks')
            ->join('BlocksIndex', 'BlocksIndex.BI_BlockID = Blocks.B_ID')
            ->join('ModuleCategoryViewPage','ModuleCategoryViewPage.MCVP_PageID = Blocks.B_PageID')
            ->join('ModuleViews','ModuleViews.MV_ID = ModuleCategoryViewPage.MCVP_ViewID')
            ->join('PagesIndex','PagesIndex.PI_PageID = ModuleCategoryViewPage.MCVP_PageID')
            ->where('Blocks.B_ModuleID = ?', $moduleID)
            ->where('PagesIndex.PI_LanguageID = ?',$lang)
            ->where('ModuleCategoryViewPage.MCVP_ModuleID = ?', $moduleID)
            ->where('Blocks.B_Online = 1')
            ->where('BlocksIndex.BI_LanguageID =?',$lang)
            ->order('Blocks.B_Position ASC');
        $Rows = $db->fetchAll($select);

        foreach ($Rows as $row){
            $details_page = $row['PI_PageIndex'];
            $list_page = Cible_FunctionsCategories::getPagePerCategoryView( $row['MCVP_CategoryID'], 'list', $moduleID, $lang);
            if (in_array($row['MV_Name'], $this->_specificAction)){
                $productArray = $productCollection->getProductsUrl();
                foreach ($productArray as $product){
                    $arrayURL = array();
                    array_push($arrayURL, $path . "/" . $details_page . "/" . $product);
                    array_push($arrayURL,"0.5");
                    array_push($arrayURL,"weekly");
                    array_push($arrayURL, date("Y-m-d H:m:s"));
                    array_push($arrayForXML,$arrayURL);
                }
            }
        }
        return $arrayForXML;

    }
}