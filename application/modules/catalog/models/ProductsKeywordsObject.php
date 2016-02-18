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
 * @version   $Id: ProductsKeywordsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsKeywordsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */
class ProductsKeywordsObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'ProductsKeywordsData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = 'CPK_RefId';
    protected $_listedField     = '';
    protected $_seq             = 0;
    protected $_query;

    public function setAssociation($id, array $ids, $insert = 'save')
    {
        switch ($insert)
        {
            case 'insert':
                if (!empty($ids))
                {
                    foreach ($ids as $value)
                    {
                        $this->_seq += 100;
                        $this->_save($id, $value);
                    }
                }

                break;
            case 'delete':
                $this->deleteAssociation($id);
                break;
            default:
                if (!empty($ids))
                {
                    $this->deleteAssociation($id);
                    foreach ($ids as $value)
                    {
                        $this->_seq += 100;
                        $this->_save($id, $value);
                    }
                }
                break;
        }
    }
    public function getAssociationData($id)
    {
        $assoc = array();
        $data = $this->getAll(1, true, $id);
        foreach($data as $row){
            $assoc[] = $row[$this->_foreignKey];
        }
        return $assoc;
    }
}