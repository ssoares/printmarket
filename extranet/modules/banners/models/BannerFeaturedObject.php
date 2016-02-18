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
 * @version   $Id: BannerFeaturedObject.php 160 2011-07-05 04:21:41Z ssoares $
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerFeaturedObject.php 160 2011-07-05 04:21:41Z ssoares $
 */
class BannerFeaturedObject extends DataObject
{
    protected $_dataClass   = 'BannerFeaturedData';

//    protected $_indexClass      = 'BannerFeaturedIndex';
//    protected $_indexLanguageId = 'BFI_LanguageID';

    protected $_constraint      = '';
    protected $_foreignKey      = '';

    public function loadData($recordID, $langId)
    {
        $oBannerImgFeat = new BannerFeaturedImageObject();

        $data   = $this->populate($recordID, $langId);
        $record = $this->getAll($langId, false, $recordID);

        $oBannerImgFeat->setQuery($record);
        $tmpData = $oBannerImgFeat->completeQuery($langId);
//        $tmpData = $oBannerImgFeat->getData($langId, $recordID);
       
        foreach ($tmpData as $imgData)
        {
            foreach ($imgData as $key => $value)
            {
                $recordKey = $key . $imgData['IF_ImgID'];
                $data[$recordKey] = $value;
            }
        }
        
        return $data;
    }
}