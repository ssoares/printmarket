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
 * @version   $Id: ProductsAssociationObject.php 1820 2015-10-07 14:04:22Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 1820 2015-10-07 14:04:22Z ssoares $id
 */
class ProductsAssociationObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'ProductsAssociationData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = 'AP_RelatedProductID';
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

    private function _save($id, $value)
    {
        $oAssociation = new $this->_dataClass();
        $associateData = $oAssociation->createRow();
        $col1 = $this->_dataId;
        $col2 = $this->_foreignKey;
        $col3 = 'AP_Seq';
        $associateData->$col1 = $id;
        $associateData->$col2 = $value;
        $associateData->$col3 = $this->_seq;
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