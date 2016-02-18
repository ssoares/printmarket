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
    protected $_indexClass = 'VideoIndex';
    protected $_indexLanguageId = 'VI_LanguageID';

   // protected $_dataId = 'V_ID';


  //  protected $_indexId = 'VI_ID';


    /*protected $_indexColumns = array(
        'VI_ID' => 'VI_ID',
        'VI_LanguageID' => 'VI_LanguageID',
        'VI_Description' => 'VI_Description',
        'VI_Name' => 'VI_Name',
        'VI_Poster' => 'VI_Poster',
        'VI_MP4' => 'VI_MP4',
        'VI_WEBM' => 'VI_WEBM',
        'VI_OGG' => 'VI_OGG'
    );*/




    public function getVideosList()
    {
        $select = parent::getAll(null,false);
        $select->where('VI_LanguageID = ?', Cible_Controller_Action::getDefaultEditLanguage());
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

    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
}