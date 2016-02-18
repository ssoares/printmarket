<?php
/**
 * Module Catalog
 * Management of the products for Logiflex.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationData.php 435 2011-03-28 03:57:25Z ssoares $id
 */

/**
 * Manage data from the table whith data establishing relation between
 * collections and products.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationData.php 435 2011-03-28 03:57:25Z ssoares $id
 */
class ProductsAssociationData extends Zend_Db_Table
{
    protected $_name = "Catalog_AssociatedProducts";
}