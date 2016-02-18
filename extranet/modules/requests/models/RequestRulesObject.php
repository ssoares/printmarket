<?php
/**
 * Module RequestRules
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_RequestRules
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RequestRulesObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */

/**
 * Manage data from colletion table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_RequestRules
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RequestRulesObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */
class RequestRulesObject extends DataObject
{
    protected $_dataClass       = 'RequestRulesData';
    protected $_constraint      = '';
    protected $_foreignKey      = '';
    protected $_orderBy         = array('RR_CollectionId', 'RR_ModifDate');
    protected $_query;
    protected $_name            = '';
    protected $_requestId       = 0;

    public function setRequestId($requestId)
    {
        $this->_requestId = $requestId;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function saveRules($data)
    {
        if ($this->_requestId){
            $this->delete($this->_requestId);
            foreach($data as $collecId => $collection){
                $this->_insertRowCriterias($collecId, $collection);
            }
        }
    }

    private function _insertRowCriterias($collecId, $collection)
    {
        foreach($collection as $criteria)
        {
            if (!empty($criteria['filterSet']))
            {
                $criteria = $this->_formatInputData($criteria);
                $row = $this->_oData->createRow();
                $row->RR_FilterId = $this->_requestId;
                $row->RR_CollectionId = $collecId;
                $row->RR_FieldId = $criteria['filterSet'];
                $row->RR_ConditionId = $criteria['operators'];
                $row->RR_Value = $criteria['filterValue'];
                $row->save();
            }
        }
    }

//    public function insert($data, $langId)
//    {
//        $data = parent::_formatInputData($data);
//        $data['RR_CreaDate'] = date('Y-m-d H:i:s');
//        return parent::insert($data, $langId);
//    }
//
//    public function save($id, $data, $langId)
//    {
//        $data = parent::_formatInputData($data);
//        return parent::save($id, $data, $langId);
//    }

    public function delete($id)
    {
        $where = $this->_db->quoteInto( $this->_dataId . " = ?", $id);
        $this->_db->delete($this->_oDataTableName, $where);
    }

    protected function _formatInputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'filterValueDt':
                    if (!empty($values)){
                        if ($data['filterValue'] != $values){
                            $values = $data['filterValue'];
                        }
                        $data['filterValue'] = date('Y-m-d', strtotime($values));
                        $data[$field] = $data['filterValue'];
                    }
                    break;
                default:
                    break;
            }
        }

        return $data;

    }

    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case ($field == 'RR_Value' && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $values)):
                    if (!empty($values) && $values != '0000-00-00'){
                        $data[$field . 'Dt'] = $values;
                        $data[$field] = date('d-m-Y', strtotime($values));
                    }else{
                        $data[$field . 'Dt'] = '';
                        $data[$field] = '';
                    }
                    break;

                default:
                    break;
            }
        }

        return $data;
    }

    public function loadRules()
    {
        $rules = array();
        $data = $this->getAll(null, true, $this->_requestId);
        foreach($data as $row)
        {
            $row = $this->_formatOutputData($row);
            $collectionId = $row['RR_CollectionId'];
            $rules[$collectionId][] = array(
                'filterSet' => $row['RR_FieldId'],
                'operators' => $row['RR_ConditionId'],
                'filterValue' => $row['RR_Value']
            );

        }
        return $rules;
    }

}