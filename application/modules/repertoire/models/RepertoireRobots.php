<?php
/*****
 * 
 *  Return the string that will be put in the robots.txt
 * 
 ****/
class RepertoireRobots extends DataObject
{
    protected $_dataClass   = 'RepertoireData';
    
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    
    protected $_specificAction = array('details');  // this are the action that need a specific treatment

    public function getXMLFilesString($path = "", $title = "")
    {
        $db = Zend_Registry::get('db');     
        $xmlString = "";
        (array) $array = array();
        
        $select1 = $db->select()
            ->distinct()
            ->from('Languages');
        $langs = $db->fetchAll($select1); 
        
        foreach ($langs as $lang){
            $xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "\n";           
        }
        return $xmlString;
    }
    
    public function getXMLFile($path = "", $lang = "")
    {        
        $moduleID = 20;        
        $db = Zend_Registry::get('db');     
        $xmlString = "";
        $arrayForXML = array();  
                
        $select2 = $db->select()
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
        $Rows = $db->fetchAll($select2);        
       
        foreach ($Rows as $row){
            if (in_array($row['MV_Name'], $this->_specificAction)) {
                $select3 = $db->select()
                    ->distinct()
                    ->from('RepertoireIndex')      
                    ->join('RepertoireData', 'RepertoireIndex.RI_RepertoireDataID = RepertoireData.RD_ID')
                    ->where('RepertoireIndex.RI_Status = 1')
                    ->where('RepertoireIndex.RI_LanguageID =?',$lang)
                    ->order('RepertoireData.RD_ReleaseDate DESC');
                
                $RepertoireRows = $db->fetchAll($select3);    
                foreach ($RepertoireRows as $RepertoireRow){
                    $details_page = Cible_FunctionsCategories::getPagePerCategoryView( $row['MCVP_CategoryID'], 'details', $moduleID, $lang);
                    $arrayURL = array();  
                    array_push($arrayURL, $path . "/" . $details_page . "/" . $RepertoireRow['RD_ReleaseDate'] . "/" . $RepertoireRow['RI_ValUrl']);
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