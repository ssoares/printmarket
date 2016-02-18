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
 * @version   $Id: ProductsTabsObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsTabsObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */
class ProductsTabsObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'ProductsTabsData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = 'CPT_LanguageId';
    protected $_constraint      = '';
    protected $_foreignKey      = 'CPT_TabId';
    protected $_listedField     = array('CPT_TabTitle', 'CPT_TabText', 'CPT_LanguageId');
    protected $_seq             = 0;
    protected $_query;
    protected $_languageId;

    protected $_searchColumns = array(
        'index' => array(
            'CPT_TabText'
        )
    );

    public function setLanguageId($languageId)
    {
        $this->_languageId = $languageId;
        return $this;
    }

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
            $key = $row[$this->_foreignKey];
            $assoc[$this->_foreignKey.$key] = $key;
            $assoc[$this->_listedField[0].$key] = $row[$this->_listedField[0]];
            $assoc[$this->_listedField[1].$key] = $row[$this->_listedField[1]];
        }
        return $assoc;
    }

    private function _save($id, $value)
    {
        $oAssociation = new $this->_dataClass();
        $associateData = $oAssociation->createRow();
        $col1 = $this->_dataId;
        $col2 = $this->_foreignKey;
        $col3 = $this->_listedField[0];
        $col4 = $this->_listedField[1];
        $key = current($value);
        $associateData->$col1 = $id;
        $associateData->$col2 = $key;
        $associateData->$col3 = $value[$col3.$key];
        $associateData->$col4 = $value[$col4.$key];
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