<?php
/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of SwitchDB
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Controller_Action_Helper_Reports extends Zend_Controller_Action_Helper_Abstract
{
    protected $_extension = '.csv';
    protected $_data = array();
    protected $_destination = 'local';
    protected $_defaultEditLanguage;
    protected $_defaultInterfaceLanguage;
    protected $_config;
    protected $_rootFilesPath;
    protected $_label = '';
    protected $_oData = null;
    protected $_oReport = null;
    protected $_forModule = null;
    protected $_query = null;

    public function getQuery(){
        return $this->_query;
    }

    public function setForModule($forModule){
        $this->_forModule = $forModule;
        return $this;
    }

    public function setLimit($offset, $i)
    {
        $this->_query->limit($offset, $i);
        return $this;
    }

    public function getDefaultEditLanguage()
    {
        return $this->_defaultEditLanguage;
    }

    public function getDefaultInterfaceLanguage()
    {
        return $this->_defaultInterfaceLanguage;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function getRootFilesPath()
    {
        return $this->_rootFilesPath;
    }

    public function setDefaultEditLanguage($defaultEditLanguage)
    {
        $this->_defaultEditLanguage = $defaultEditLanguage;
        return $this;
    }

    public function setDefaultInterfaceLanguage($defaultInterfaceLanguage)
    {
        $this->_defaultInterfaceLanguage = $defaultInterfaceLanguage;
        return $this;
    }

    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function setRootFilesPath($rootFilesPath)
    {
        $this->_rootFilesPath = $rootFilesPath;
        return $this;
    }

    public function setDestination($destination){
        $this->_destination = $destination;
        return $this;
    }

    public function getExtension(){
        return $this->_extension;
    }
    public function getData(){
        return $this->_data;
    }

    public function setExtension($extension){
        if (!strpos('.', $extension)){
            $extension = '.' . $extension;
        }
        $this->_extension = $extension;
        return $this;
    }
    public function setData($data){
        $this->_data = $data;
        return $this;
    }
    public function setOData(){
        if (null === $this->_oData){
            $this->_oData = new RequestsObject();
        }
        return $this;
    }

    public function setProperties($properties){
        $methods = get_class_methods(get_class());
        foreach($properties as $key => $value)
        {
            $tmp = 'set' . ucfirst(str_replace('_', '', $key));
            if (in_array($tmp, $methods)){
                $this->$tmp($value);
            }
        }
        return $this;
    }

    public function buildRequest($id, $data = array())
    {
        try
        {
            $db = Zend_Registry::get('db');
            if (empty($data)){
                $this->setOData();
                $data = $this->_oData->populate($id, $this->_defaultEditLanguage);
            }
            if (empty($this->_config) || !isset($this->_config->fields)){
                $this->_config = $this->_oData->loadModuleConfig()->getConfig();
            }
            $cfg = $this->_config->toArray();
            $this->_label = $data['REI_Label'];
            $oReT = new ReportTypeObject();
            $reType = $oReT->populate($data['RE_ReportType'], $this->_defaultEditLanguage);
            $module = Cible_FunctionsModules::getModuleNameByID($reType['RET_ModuleId']) . 'Object';
            $this->_oReport = new $module();
            $this->_query = $this->_oReport->setRequestOptions($reType)->getDefaultRequest();

            $whereClause = array();
            foreach($data['filterSet'] as $key => $criteria)
            {
                $where = array();
                foreach($criteria as $crit)
                {
                    $fieldId = $crit['filterSet'];
                    $obj = $cfg['fields'][$fieldId]['belongsTo'];
                    $opId = $crit['operators'];
                    $field = $cfg['fields'][$fieldId]['field'];
                    $col = '';
                    $addJoin = true;
                    if ($cfg['fields'][$fieldId]['column'] != 'joinOnly'){
                        $col = empty($cfg['fields'][$fieldId]['column']) ?
                            $this->_oReport->getColByFilter($field) : $cfg['fields'][$fieldId]['column'];
                    }else{
                        $addJoin = (bool)$crit['filterValue'];
                    }
                    $type = $cfg['fields'][$fieldId]['type'];
                    if ($type == 'date'){
                        $crit['filterValue'] = date('Y-m-d', strtotime($crit['filterValue']));
                    }
                    $type = ($type == 'numeric' || $type == 'date') ? 'numdate' : $type;
                    $op = $cfg['operators'][$type][$opId]['value'];
                    $tmp = new $obj();
                    $from = $this->_query->getPart('from');
                    $isJoined = array_key_exists($tmp->getDataTableName(), $from);
                    if (!$isJoined && $addJoin){
                        if ($tmp->getDataId() == $tmp->getForeignKey()){
                            $fkey = $this->_oReport->getForeignKey();
                            $tmp->setDataId($fkey);
                        }
                        $this->_query = $tmp->setQuery($this->_query)
                            ->setNoColumns(true, true)
                            ->setColumns(array())
                            ->joinForRequest(false, $this->_defaultEditLanguage);
                    }
                    if ((3 == $opId || 4 == $opId) && $type == 'varchar'){
                        $crit['filterValue'] = '%' . str_replace(' ', '%', $crit['filterValue']) . '%';
                    }
                    if (!empty($col)){
                        $where[] = $db->quoteInto($col .' '. $op .' ?', $crit['filterValue']);
                    }else{
                        $where[] = $tmp->getFilterCondition($field);
                    }
                }
                if (!empty($where)){
                    $whereClause[] = implode(' AND ', $where);
                }
            }
            if (!empty($whereClause)){
                $this->_query->where('(' . implode(') OR (', $whereClause) . ')');
            }

        }
        catch(Exception $exc)
        {
            return $exc->getMessage();
        }
    }

    public function findData()
    {
        return $this->_oReport->findData(array(), true);
    }

    public function getList()
    {
        $list = array();
        $this->setOData();
        $req = $this->_oData->getFiltersForModule($this->_defaultEditLanguage, $this->_forModule);
        foreach ($req as $value){
            $id = $value[$this->_oData->getDataId()];
            $this->buildRequest($id);
            $label = $value[$this->_oData->getTitleField()];
            try
            {
                $list[$id] = $label . ' (' . $this->countData() . ')';
            }
            catch(Exception $exc)
            {
                echo "<pre>";
                echo $this->_query;
                echo "</pre>";
                echo "<pre>";
                echo $exc->getMessage();
                echo $exc->getLine();
                echo "</pre>";
                echo "<pre>";
                echo $exc->getTraceAsString();
                echo "</pre>";
                exit;
            }

        }
        return $list;
    }
    public function output($filename, $lines)
    {
        switch($this->_destination)
        {
            case 'download':
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename={$filename}");
                echo $lines;
                break;

            default:
                break;
        }
    }

    public function getRefValuesField(){
       return $this->_oReport->getRefValuesField($this->_defaultInterfaceLanguage);
    }
    public function countData()
    {
        return $this->_oReport->countData();
    }

    public function getFilename()
    {
        return Cible_FunctionsGeneral::formatValueForUrl($this->_label) .'-' . date('Ymd');
    }
}