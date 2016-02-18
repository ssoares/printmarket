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
 * @version   $Id: ProductsAssociationObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */
class ProductsAssociationObject extends DataObject
{
    protected $_dataClass   = 'ProductsAssociationData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();
    
    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';

    /**
     * Get the products related to the selected product.
     * 
     * @param int $productId
     *
     * @return array
     */
    public function getProductsByProductId($productId)
    {
        $associated = $this->populate($productId, Zend_Registry::get('languageID'));


    }
}