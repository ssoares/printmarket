<?php
/**
 * Module Catalog
 * Management of the Items.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsImagesObject.php 1309 2013-10-29 14:50:48Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsImagesObject.php 1309 2013-10-29 14:50:48Z ssoares $id
 */
class ProductsImagesObject extends DataObject
{
    protected $_dataClass       = 'ProductsImagesData';
    protected $_constraint      = '';
    protected $_foreignKey      = 'CPI_ProductId';
    protected $_productId;
    protected $_query;

    public function setProductId($_productId)
    {
        $this->_productId = $_productId;

        return $this;
    }

    public function insert($data, $langId)
    {
        $imgs = array();

        foreach ($data as $values)
        {
            if (!empty($values['P_Photo']))
            {
                $imgs[$this->_foreignKey] = $this->_productId;
                $imgs['CPI_Img'] = $values['P_Photo'];
                $imgs['CPI_Seq'] = $values['P_Photo_Seq'];
                parent::insert($imgs, $langId);
            }
        }
    }

    public function deleteByProductId()
    {
        $this->_db->delete(
            $this->_oDataTableName,
            $this->_db->quoteInto("{$this->_foreignKey} = ?", $this->_productId)
        );

        return $this;
    }

    public function getAll($langId = null, $array = true, $id = null)
    {
        $this->_query = parent::getAll($langId, false, $id);
        $this->_query->where($this->_foreignKey . ' = ?', $this->_productId);
        if (!empty($this->_orderBy))
            $this->_query->order($this->_orderBy);

        if ($array)
            $list = $this->_db->fetchAll($this->_query);
        else
            $list = $select;

        return $list;

    }

}