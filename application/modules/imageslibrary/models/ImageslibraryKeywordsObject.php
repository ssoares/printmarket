<?php
/**
 * Module Imageslibrary
 * Management of keywords associations.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryKeywordsObject.php 346 2013-01-30 04:36:31Z ssoares $
 */

/**
 * Manage data from Imageslibrary_Keywords table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryKeywordsObject.php 346 2013-01-30 04:36:31Z ssoares $
 */
class ImageslibraryKeywordsObject extends DataObject
{
    protected $_dataClass   = 'ImageslibraryKeywordsData';

    protected $_constraint      = 'ILK_RefId';
    protected $_foreignKey      = 'ILK_RefId';
    protected $_query;

    protected $_clause;
    public function getClause()
    {
        return $this->_clause;
    }

    public function setClause($clause)
    {
        $this->_clause = ' AND ' . $this->_constraint ;
        if (is_array($clause) && count($clause) > 1)
            $this->_clause .=  ' in (' . implode(',',$clause) . ')';
        else
            $this->_clause .=  ' = ' . $clause[0];
    }

    public function setQuery(Zend_Db_Select $query = null)
    {
        $this->_query = $query;
    }

    public function getData($id = null, $langId = null)
    {
        $data = array();
        $oRef = new ReferencesObject();
        $select = parent::getAll($langId, false, $id);
        $oRef->setQuery($select);
        $refs= $oRef->completeQuery($langId, true);
        if (!empty($refs))
        {
            foreach ($refs as $ref)
            {
                $tmp['id'][] = $ref['R_ID'];
                $tmp['value'][] = $ref['RI_Value'];
            }

            $data[$this->_foreignKey] = implode(',', $tmp['id']);
            $data['listKeywords'] = implode(', ', $tmp['value']);
        }

        return $data;
    }
    public function getListByCollectionImages(array $imgIds)
    {
        $list = array();
        $langId = Zend_Registry::get('languageID');
        foreach ($imgIds as $img)
        {
            $select = parent::getAll($langId, false);
            $select->where($this->_dataId . ' = ?', $img['ILC_ImageId']);

            $tmp = $this->_db->fetchAll($select);
            foreach ($tmp as $val)
                $list [$val[$this->_foreignKey]]= $val[$this->_foreignKey];
        }
        return $list;
    }

    public function save($id, $data, $langId)
    {
        $values = explode(',', $data[$this->_foreignKey]);
        parent::delete($id);
        if (!empty($values))
        {
            foreach ($values as $value)
            {
                $tmp = array(
                    $this->_dataId => $id,
                    $this->_foreignKey => $value
                    );
                parent::insert($tmp, $langId);
            }
        }
    }

    public function deleteAssociation($id)
    {
        $this->_db->delete($this->_oDataTableName, $this->_db->quoteInto("{$this->_constraint} = ?", $id));
    }

    public function countImages($refId)
    {
        $total = 0;
        if ($refId){
            $data = $this->findData(array($this->_foreignKey => (int)$refId));
            $total = count($data);
        }

        return $total;
    }
}