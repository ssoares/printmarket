<?php
/*****
 *
 *  Return the string that will be put in the robots.txt
 *
 ****/
class GalleryRobots extends DataObject
{
    protected $_dataClass   = 'NewsData';

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

        /* This is for the future when we want to separate the categories in different xml file*/
        /*
        $select2 = $db->select()
            ->distinct()
            ->from('NewsData',
            array(
                'ND_CategoryID')
         );

        $cats = $db->fetchAll($select2);*/

        //echo $select2;
        foreach ($langs as $lang){
            //foreach ($cats as $cat){
                //$xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "/cat/" . $cat['ND_CategoryID'] . "\n";
                $xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "\n";
            //}
        }
        return $xmlString;
    }

    public function getXMLFile($path = "", $lang = "")
    {
        $path = Zend_Registry::get('absolute_web_root');
        $moduleID = 9;
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
            if (in_array($row['MV_Name'], $this->_specificAction)){
                $select3 = $db->select()
                    ->distinct()
                    ->from('GalleriesIndex')
                    ->join('Galleries', 'GalleriesIndex.GI_GalleryID = Galleries.G_ID')
                    ->where('GalleriesIndex.GI_LanguageID =?',$lang);
                $NewsRows = $db->fetchAll($select3);

                foreach ($NewsRows as $NewsRow){
                    $details_page = Cible_FunctionsCategories::getPagePerCategoryView( $row['MCVP_CategoryID'], 'details', $moduleID, $lang);
                    $arrayURL = array();
                    array_push($arrayURL, $path . "/" . $details_page . "/" . $NewsRow['GI_ValUrl']);
                    array_push($arrayURL,"0.5");
                    array_push($arrayURL,"weekly");
                    array_push($arrayURL, date("Y-m-d H:m:s"));
                    array_push($arrayForXML,$arrayURL);

                    $select4 = $db->select()
                        ->distinct()
                        ->from('Galleries_Images')
                        ->where('Galleries_Images.GI_GalleryID=?', $NewsRow['G_ID']);
                    $RowsGalleryImages = $db->fetchAll($select4);

                    $iii = 0;
                    foreach ($RowsGalleryImages as $RowsGalleryImage){
                        $arrayURL = array();
                        array_push($arrayURL, $path . "/" . $details_page . "/" . $NewsRow['GI_ValUrl'] . "#!prettyPhoto[gallery1]/" . $iii . "/");
                        array_push($arrayURL,"0.5");
                        array_push($arrayURL,"weekly");
                        array_push($arrayURL, date("Y-m-d H:m:s"));
                        array_push($arrayForXML,$arrayURL);
                        $iii++;
                    }
                }
            }
        }
        return $arrayForXML;
    }
}