<?php
/**
 * Cible Solutions
 * Parameters management
 *
 * @category  Cible_Models
 * @package   Cible_Modules_Pages
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ParametersObject.php 1851 2016-01-15 22:56:34Z ssoares $
 */

/**
 * Manage data for blocks parameters
 *
 * @category  Cible_Models
 * @package   Cible_Modules_Pages
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ParametersObject extends DataObject
{
    protected $_dataClass  = 'Parameters';
    protected $_constraint = 'P_Number';
    protected $_foreignKey = '';
    protected $_blockId    = 0;
    protected $_siteOrigin = '';

    public function setSiteOrigin($siteOrigin)
    {
        $this->_siteOrigin = $siteOrigin;
        return $this;
    }

    public function setBlockId($blockId)
    {
        $this->_blockId = $blockId;
        return $this;
    }

    public function insert($data, $langId)
    {
        $this->_insert($data, $langId);
    }

    private function _insert($data, $langId)
    {
        foreach ($data as $key => $param)
        {
            if (preg_match('/^Param/', $key))
            {
                $tmp[$this->_dataId] = $this->_blockId;
                $tmp['P_Number'] = str_replace('Param', '', $key);
                if (is_array($param)){
                    $param = implode(',', $param);
                }
                $tmp['P_Value'] = $param;
                parent::insert($tmp, $langId);
            }
        }

    }

    public function save($id, $data, $langId)
    {

        return parent::save($id, $data, $langId);
    }

    public function getData()
    {
        if (!empty($this->_siteOrigin))
        {
            $defaultAdapter = $this->_db;
            $dbs = Zend_Registry::get('dbs');
            $this->_db = $dbs->getDb($this->_siteOrigin);
        }
        $this->_query = $this->getAll(1, false, $this->_blockId);
        $data = $this->_db->fetchAll($this->_query);
        if (!empty($defaultAdapter))
                    $this->_db = $defaultAdapter;

        foreach ($data as $param){
            $test = explode(',', $param['P_Value']);
            if (count($test) > 1 && is_numeric($test[0])){
                $param['P_Value'] = explode(',', $param['P_Value']);
            }
            $parameters['Param' . $param['P_Number']] = $param['P_Value'];
        }

        return $parameters;
    }
}