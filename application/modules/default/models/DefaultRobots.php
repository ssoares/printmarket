<?php
/*****
 *
 *  Return the string that will be put in the robots.txt
 *
 ****/

class DefaultRobots extends DataObject
{
     protected $_dataClass   = 'NewsData';

    protected $_indexClass      = '';
    protected $_indexLanguageId = '';

    protected $_specificAction = array();  // this are the action that need a specific treatment

    public function getXMLFilesString($path = "", $title = "")
    {
        $db = Zend_Registry::get('db');
        $xmlString = "";
        (array) $array = array();

        $select1 = $db->select()
            ->distinct()
            ->from('Languages')
            ->where('L_Active=1');
        $langs = $db->fetchAll($select1);

        foreach ($langs as $lang){
                $xmlString .= $path . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "\n";
        }
       // echo  $xmlString;
        return $xmlString;
    }

    public function getXMLFile($lang = "")
    {
        //$path = Zend_Registry::get('absolute_web_root');
        $config = Zend_Registry::get('config');
        $path = $this->_protocol . $config->site->domainsName->$lang; // pour les multidomains

        if(substr($path, -1)=="/"){
            $path = substr_replace($path ,"",-1);
        }



        $pageList  = MenuObject::getAllMenuItemForSiteMapRobots($lang);
        $arrayForXML = array();

        foreach ($pageList as $page){
            $arrayURL = array();
            array_push($arrayURL, $path . "/" . $page['PI_PageIndex']);
            array_push($arrayURL,"0.5");
            array_push($arrayURL,"weekly");
            array_push($arrayURL, date("Y-m-d"));
            array_push($arrayForXML,$arrayURL);
        }
        return $arrayForXML;
    }
}