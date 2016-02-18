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
 * @version   $Id: ProductsCategoriesObject.php 1842 2016-01-14 15:21:52Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsCategoriesObject.php 1842 2016-01-14 15:21:52Z ssoares $id
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
    public function getCategories($id)
    {
        $assoc = array();
        $data = $this->getAll(1, true, $id);
        foreach($data as $row){
            $assoc[] = $row[$this->_foreignKey];
        }
        return $assoc;
    }

    private function _save($id, $value)
    {
        $oAssociation = new $this->_dataClass();
        $associateData = $oAssociation->createRow();
        $col1 = $this->_dataId;
        $col2 = $this->_foreignKey;
        $associateData->$col1 = $id;
        $associateData->$col2 = $value;
        $associateData->save();
    }

    public function deleteAssociation($id, $field = '')
    {
        if (empty($field))
            $field = $this->_dataId;
        $association = new $this->_dataClass();
        $where = $this->_db->quoteInto( $field . " = ?", $id);
        $association->delete($where);
    }
}