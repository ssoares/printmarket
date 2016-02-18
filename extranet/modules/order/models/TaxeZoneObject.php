<?php
/**
 * Cible Solutions -
 * Orders management.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: TaxeZoneObject.php 422 2011-03-24 03:25:10Z ssoares $
 */

/**
 * Manage data in database for the taxes.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class TaxeZoneObject extends DataObject
{
    protected $_dataClass   = 'TaxeZoneData';
    protected $_dataId      = 'TZ_ProvCode';
//    protected $_dataColumns = array();
    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();

    protected $_query;

    public function  __construct()
    {
        $this->_delimiter      = "|";
        $this->_fileWithHeader = false;
        parent::__construct();
        $this->_dataId = 'TZ_ProvCode';
        unset($this->_dataColumns['TZ_ID']);
    }
}