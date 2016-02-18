<?php

/**
 * Module Utilities
 * Management of the references data.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Utilities
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ReferencesObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ReferencesObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */
class ReferencesObject extends DataObject
{

    protected $_dataClass = 'ReferencesData';
    protected $_indexClass = 'ReferencesIndex';
    protected $_indexLanguageId = 'RI_LanguageID';
    protected $_usedColumns = array();
    protected $_usedClass = 'ReferencesUsedData';
    protected $_foreignKey = 'ILK_RefId';
    protected $_orderBySeq = true;
    protected $_orderBy = array('R_Seq ASC');
    protected $_constraint      = '';
    protected $_searchColumns = array('index' => array('RI_Value'));
    protected $_titleField      = 'RI_Value';

    public function getTitleField()
    {
        return $this->_titleField;
    }

    /**
     *
     * @var Array
     */
    protected $_orderBySeqList = array(
        'productsKeywords',
    );
    protected $_chkboxList = array(
        'album',
    );
    protected $_query;

    public function setOrderBySeqList($orderBySeqList)
    {
        if (is_string($orderBySeqList))
            array_push($this->_orderBySeqList, $orderBySeqList);
        elseif (is_array(($orderBySeqList)))
            array_merge($this->_orderBySeqList, $orderBySeqList);
    }

    public function setOrderBySeq($type)
    {
        $this->_orderBySeq = in_array($type, $this->_orderBySeqList);
    }

    public function getOrderBySeq()
    {
        return $this->_orderBySeq;
    }

    public function getChkboxList()
    {
        return $this->_chkboxList;
    }

    public function setChkboxList($chkboxList)
    {
        array_push($this->_chkboxList, $chkboxList);
    }

    public function getValueById($id, $langId = null)
    {
        $select = parent::getAll($langId, false, $id);

        $result = $this->_db->fetchRow($select);

        return array('reason' => $result['R_TypeRef'], 'value' => $result['RI_Value']);
    }

    public function getRefByType($type, $lang = null)
    {
        $select = parent::getAll($lang, false);
        $select->where('R_TypeRef = ?', $type);

        $this->setOrderBySeq($type);
        if ($this->_orderBySeq)
            $select->order($this->_orderBy);
        $select->order('RI_Value ASC');

        $result = $this->_db->fetchAll($select);

        return $result;
    }

    public function referencesCollection($typeRef, $langId)
    {
        (array) $array = array();

        $select = $this->getAll($langId, false);
        $select->where('R_TypeRef = ?', $typeRef);
        $products = $this->_db->fetchAll($select);

        return $products;
    }

    public function getListValues($list, $langId = null, $addDefault = true)
    {
        $src = array();
        if (is_null($langId) && SESSIONNAME == 'extranet')
            $langId = Cible_Controller_Action::getDefaultEditLanguage();
        else
            $langId = Zend_Registry::get('languageID');
        $roles = $this->getRefByType($list, $langId);
        if ($addDefault)
            $src[''] = Cible_Translation::getCibleText('form_select_default_label');

        foreach ($roles as $role)
            $src[$role['R_ID']] = $role['RI_Value'];

        return $src;
    }

    public function setUtilization($list, $meta)
    {
        $usedByTable = $meta['TABLE_NAME'];
        $usedByColumn = $meta['COLUMN_NAME'];
        $obj = new $this->_usedClass();
        $infos = $obj->info(Zend_Db_Table_Abstract::METADATA);
        $this->_usedColumns = array_keys($infos);
        $data = array_combine($this->_usedColumns, array($list, $usedByTable, $usedByColumn));
        $exists = $this->_dataExists($obj, $data);
        if (!$exists)
        {
            $row = $obj->createRow();
            foreach ($data as $key => $val)
            {
                if (isset($data[$key]))
                    $row->$key = $data[$key];
            }

            $row->save();
        }
    }

    public function isInUse($id, $lang = null)
    {
        $isFree = false;
        $count = 0;
        $value = $this->getAll($lang, true, $id);
        $list = $value[0]['R_TypeRef'];
        $obj = new $this->_usedClass();
        $select = $obj->select()->where('RLU_List = ?', $list);
        $params = $this->_db->fetchAll($select);

        foreach ($params as $key => $value)
        {
            $qryInUse = $this->_db->select()
                ->from($value['RLU_Table'])
                ->where($value['RLU_Column'] . ' = ?', $id);
            try
            {
                $result = $this->_db->fetchAll($qryInUse);
            }
            catch (Exception $exc)
            {
                print_r($qryInUse->assemble());

                echo "<pre>";
                echo $exc->getTraceAsString();
                echo "</pre>";
                exit;
            }

            $count += count($result);
        }

        if ($count == 0)
            $isFree = true;

        return $isFree;
    }

    private function _dataExists($obj, $data)
    {
        $found = false;
        $select = $obj->select();
        foreach ($data as $key => $value)
            $select->where($this->_db->quoteInto($key . ' = ?', $value));

        $result = $this->_db->fetchAll($select);

        if (count($result) > 0)
            $found = true;

        return $found;
    }

    public function listExists($list)
    {
        $exists = false;
        $lists = $this->getEnum('R_TypeRef');

        $exists = in_array($list, $lists);

        return $exists;
    }

    public function completeQuery($langId = null, $array = true)
    {
        $select = '';

        if (!empty($this->_query))
        {
            $select = $this->_query;
            $select->joinLeft($this->_oDataTableName, $this->_dataId . ' = ' . $this->_foreignKey)
                ->joinLeft($this->_oIndexTableName, $this->_dataId . ' = ' . $this->_indexId);

            if (!empty($this->_orderBy))
            {
                $select->order($this->_orderBy);
            }
            else
            {
                $select->order('RI_Seq ASC')
                ->order('RI_Value ASC');
            }

            if (!is_null($langId))
                $select->where($this->_indexLanguageId . ' = ?', $langId);

            if ($array)
            {
                $data = $this->_db->fetchAll($select);
                if (empty($data))
                {
                    $where = $select->getPart('where');
                    $select->reset(Zend_Db_Select::WHERE);
                    $select->where($where[0]);
                    $data = $this->_db->fetchAll($select);
                }

                return $data;
            }
            else
            {
                return $select;
            }
        }
    }

    public function delete($id)
    {
        parent::delete($id);
    }

}
