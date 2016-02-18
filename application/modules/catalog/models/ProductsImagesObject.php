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
 * @version   $Id: ProductsImagesObject.php 1307 2013-10-28 21:42:58Z ssoares $id
 */

/**
 * Manage data from products table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsImagesObject.php 1307 2013-10-28 21:42:58Z ssoares $id
 */
class ProductsImagesObject extends DataObject
{
    protected $_dataClass  = 'ProductsImagesData';
    protected $_foreignKey = 'CPI_ProductId';
    protected $_productId  = 0;

    public function setProductId($productId)
    {
        $this->_productId = $productId;
    }

    public function getAll($langId = null, $array = true, $id = null)
    {
        $select = parent::getAll($langId, false, $id);
        $select->where($this->_foreignKey . ' = ?', $this->_productId);
        $select->order('CPI_Seq');

        $list = $this->_db->fetchAll($select);

        return $list;

    }
}