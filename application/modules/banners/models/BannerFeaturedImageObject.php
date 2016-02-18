<?php
/**
 * Module Utilities
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerFeaturedImageObject.php 160 2011-07-05 04:21:41Z ssoares $
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerFeaturedImageObject.php 160 2011-07-05 04:21:41Z ssoares $
 */
class BannerFeaturedImageObject extends DataObject
{
    protected $_dataClass   = 'BannerFeaturedImageData';

    protected $_indexClass      = 'BannerFeaturedImageIndex';
    protected $_indexLanguageId = 'IFI_LanguageID';

    protected $_constraint      = '';
    protected $_foreignKey      = '';

    public function getData($langId = null, $bannerId = null, $hasVideos = false)
    {
        $select = parent::getAll($langId, false);

        $select->order('IF_ImgID ASC');

        if($hasVideos)
        {
            $select->join('Videos','Videos.V_ID = IFI_Video' );
            $select->join('VideosIndex','Videos.V_ID = VideosIndex.VI_ID' );
            $select->where('VI_LanguageID = ?', $langId);
        }

        if ($bannerId)
            $select->where('IF_DataID = ?', $bannerId);

        $data = $this->_db->fetchAll($select);
        return $data;
    }
}