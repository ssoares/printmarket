<?php
/**
 * Module Template
 * Management of the Items.
 *
 * @category  Application_Module
 * @package   Application_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class TemplateAssociationObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'TemplateAssociationData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = 'XX_RelatedID';
    protected $_listedField     = '';
    protected $_seq             = 0;
    protected $_query;

    public function setQuery( Zend_Db_Select $query)
    {
        $this->_query = $query;
    }

    public function setAssociation($id, array $ids, $insert = 'save')
    {
        $ids = array_unique($ids);

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

    public function getRelated($id, $langId = 1)
    {
        $related = array();
        $selectAssociation = $this->getAll($langId, false);
        $selectAssociation->where($this->_dataId . " = ?", $id);
        $associationFind = $this->_db->fetchAll($selectAssociation);

        foreach ($associationFind as $assocData)
            $related[] = $assocData[$this->_foreignKey];

        return $related;
    }

}