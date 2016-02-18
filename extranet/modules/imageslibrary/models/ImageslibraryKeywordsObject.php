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
 * @version   $Id: ImageslibraryKeywordsObject.php 336 2013-01-28 00:56:15Z ssoares $
 */

/**
 * Manage data from Imageslibrary_Keywords table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryKeywordsObject.php 336 2013-01-28 00:56:15Z ssoares $
 */
class ImageslibraryKeywordsObject extends DataObject
{
    protected $_dataClass   = 'ImageslibraryKeywordsData';

    protected $_constraint      = 'ILK_RefId';
    protected $_foreignKey      = 'ILK_RefId';
    protected $_query;


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

    public function save($id, $data, $langId)
    {
        $values = explode(',', $data[$this->_foreignKey]);
        parent::delete($id);
        if (!empty($values[0]))
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
}