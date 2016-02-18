<?php
/*****
 *
 *  Return the string that will be put in the robots.txt
 *
 ****/
class NewsletterRobots extends DataObject
{
    protected $_dataClass   = 'NewsletterReleases';

    protected $_indexClass      = '';
    protected $_indexLanguageId = '';

    protected $_specificAction = array('details_release','details_article');  // this are the action that need a specific treatment

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
        /*$select2 = $db->select()
            ->distinct()
            ->from('Newsletter_Releases',
            array(
                'NR_CategoryID')
         );

        $cats = $db->fetchAll($select2);*/

        //echo $select2;
        foreach ($langs as $lang){
            //foreach ($cats as $cat){
               // $xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "/cat/" . $cat['NR_CategoryID'] . "\n";
                $xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "\n";
            //}


        }
       // echo $xmlString;
        return $xmlString;
    }

    public function getXMLFile($lang = "")
    {
       // $path = Zend_Registry::get('absolute_web_root');

        $config = Zend_Registry::get('config');
        $path = $this->_protocol . $config->site->domainsName->$lang; // pour les multidomains

        if(substr($path, -1)=="/"){
            $path = substr_replace($path ,"",-1);
        }


        $action_infolettre = "details_release";
        $action_article = "details_article";

        $moduleID = 8;

        $db = Zend_Registry::get('db');
        $xmlString = "";
        $arrayForXML = array();

        $selectY = "SELECT DISTINCT `ModuleViews`.`MV_Name` FROM `Blocks`
            INNER JOIN `BlocksIndex` ON BlocksIndex.BI_BlockID = Blocks.B_ID
            INNER JOIN `ModuleCategoryViewPage` ON ModuleCategoryViewPage.MCVP_PageID = Blocks.B_PageID
            INNER JOIN `ModuleViews` ON ModuleViews.MV_ID = ModuleCategoryViewPage.MCVP_ViewID
            INNER JOIN `PagesIndex` ON PagesIndex.PI_PageID = ModuleCategoryViewPage.MCVP_PageID
            WHERE (Blocks.B_ModuleID = 8) AND (PagesIndex.PI_LanguageID ='" . $lang ."')
            AND (ModuleCategoryViewPage.MCVP_ModuleID = 8) AND (Blocks.B_Online = 1)
            AND (BlocksIndex.BI_LanguageID ='" . $lang ."') ORDER BY `Blocks`.`B_Position` ASC";
        $Rows = $db->fetchAll($selectY);
        foreach ($Rows as $row){
            if (in_array($row['MV_Name'], $this->_specificAction)) {
                if($row['MV_Name']==$action_infolettre){
                    $arrayURL = array();
                    $select3 = $db->select()
                        ->distinct()
                        ->from('Newsletter_Releases')
                        ->where('NR_Online = 1')
                        ->where('NR_Date < ?',date("Y-m-d"))
                        ->where('NR_LanguageID =?',$lang);
                    $NewsRows = $db->fetchAll($select3);

                    foreach ($NewsRows as $NewsRow){
                        $details_page = Cible_FunctionsCategories::getPagePerCategoryView( $NewsRow['NR_CategoryID'], 'details_release', $moduleID, $lang);
                        array_push($arrayURL, $path . "/" . $details_page . "/" . $NewsRow['NR_Date'] . "/" . $NewsRow['NR_ValUrl']);
                        array_push($arrayURL,"0.5");
                        array_push($arrayURL,"weekly");
                        array_push($arrayURL,date("Y-m-d H:m:s"));
                        array_push($arrayForXML,$arrayURL);
                        //echo $NewsRow['NR_ValUrl'];
                    }
                }
                else if($row['MV_Name']==$action_article){

                    $select3 = $db->select()
                        ->distinct()
                        ->from('Newsletter_Releases')
                        ->join('Newsletter_Articles', 'Newsletter_Articles.NA_ReleaseID = Newsletter_Releases.NR_ID')
                        ->where('NR_Online = 1')
                        ->where('NR_Date < ?',date("Y-m-d"))
                        ->where('NR_LanguageID =?',$lang);
                    $NewsRows = $db->fetchAll($select3);

                    foreach ($NewsRows as $NewsRow){
                        $arrayURL = array();
                        $details_page = Cible_FunctionsCategories::getPagePerCategoryView( $NewsRow['NR_CategoryID'], 'details_article', $moduleID, $lang);
                        array_push($arrayURL, $path . "/" . $details_page . "/" . $NewsRow['NR_Date'] . "/" . $NewsRow['NA_ValUrl']);
                        array_push($arrayURL,"0.5");
                        array_push($arrayURL,"weekly");
                        array_push($arrayURL,date("Y-m-d H:m:s"));
                        array_push($arrayForXML,$arrayURL);

                    }
                }
            }
        }
        return $arrayForXML;
    }

}