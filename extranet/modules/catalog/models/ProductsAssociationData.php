<?php
/**
 * Module Catalog
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationData.php 422 2011-03-24 03:25:10Z ssoares $id
 */

/**
 * Manage data from the table whith data establishing relation between
 * collections and products.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationData.php 422 2011-03-24 03:25:10Z ssoares $id
 */
class ProductsAssociationData extends Zend_Db_Table
{
    protected $_name = "Catalog_AssociatedProducts";
}