<?php
/**
 * Cible Solutions -
 * Orders management.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: OrderObject.php 1191 2013-05-14 02:45:42Z ssoares $
 */

/**
 * Manage data in database for the orderss.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class OrderObject extends DataObject
{
    protected $_dataClass   = 'OrderData';
    protected $_dataId      = '';
//    protected $_dataColumns = array();
    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();

    protected $_query;

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