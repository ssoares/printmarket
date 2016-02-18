<?php
/**
 * Module Banners
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannersObject.php 1191 2013-05-14 02:45:42Z ssoares $
 */

/**
 * Manage data from table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannersObject.php 1191 2013-05-14 02:45:42Z ssoares $
 */
class BannersObject extends DataObject
{
    protected $_dataClass   = 'BannerImageData';
    protected $_dataId      = '';
    protected $_constraint      = '';
    protected $_foreignKey      = '';



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
            mkdir ($imgPath . '/featured' );
            mkdir ($imgPath . '/featured/tmp' );
        }
    }
}