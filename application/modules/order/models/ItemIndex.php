<?php
/**
 * Cible Solutions
 * Order management.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ItemIndex.php 435 2011-03-28 03:57:25Z ssoares $
 */

/**
 * Database access to the table "Catalog_ItemsIndex"
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ItemIndex extends Zend_Db_Table
{
    protected $_name = 'Catalog_ItemsIndex';
}