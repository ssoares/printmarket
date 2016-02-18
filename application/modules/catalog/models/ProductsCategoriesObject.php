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
 * @version   $Id: ProductsCategoriesObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsCategoriesObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */
class ProductsCategoriesObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'ProductsCategoriesData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = 'CPC_CategoryId';
    protected $_listedField     = '';
    protected $_seq             = 0;
    protected $_query;


    public function getCategories($id)
    {
        $assoc = array();
        $data = $this->getAll(1, true, $id);
        foreach($data as $row){
            $assoc[] = $row[$this->_foreignKey];
        }
        return $assoc;
    }
}