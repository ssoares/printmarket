<?php
/**
 * Module Utilities
 * Management of the references data.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Utilities
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerImageObject.php 42 2011-06-02 00:54:28Z ssoares $
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerImageObject.php 42 2011-06-02 00:54:28Z ssoares $
 */
class VideoObject extends DataObject
{
    protected $_dataClass   = 'VideoData';

    protected $_indexClass      = 'VideoIndex';    

    public function getVideosList()
    {
        $select = parent::getAll(null,false);//
        $select->where('VI_LanguageID = ?', Zend_Registry::get('languageID'));
        $select->order('V_Alias ASC');  
        //echo $select;
      //  exit;
        $data = $this->_db->fetchAll($select);        
        return $data;
    }
    
    public function deleteVideo($id){
        $db = $this->_db;
        $db->delete($this->_oDataTableName, $db->quoteInto("V_ID = ?", $id));
    }
    
    public function getVideoWithID($id)
    {
        $select = parent::getAll(null,false);//
        $select->where('VI_LanguageID = ?', Zend_Registry::get('languageID'))
               ->where('VI_ID = ?',$id);  
        //echo $select;
      //  exit;
        $data = $this->_db->fetchRow($select);        
        return $data;
    }
    
}