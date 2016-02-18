<?php
/**
 * Log
 * Management of activities for each module.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: LogObject.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Manage data from log tables.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: LogObject.php 1788 2015-06-18 20:07:52Z ssoares $
 */
class LogObject extends DataObject implements Cible_Log_Interface
{

    protected $_dataClass   = 'LogData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = '';

    protected $pairSepar = '|';
    protected $separator = '||';
    protected $_moduleId = 0;
    protected $_userId = 0;
    protected $_where = '';

    /**
     * Set the module id which we look for data.
     *
     * @param type $_moduleId Id of the module
     *
     * @return void
     */
    public function setModuleId($_moduleId)
    {
        $this->_moduleId = $_moduleId;
    }

    /**
     * Set the user id which we look for data.
     *
     * @param type $_userId Id of the module
     *
     * @return void
     */
    public function setUserId($_userId)
    {
        $this->_userId = $_userId;
    }

    public function setWhere(array $where)
    {
        foreach ($where as $key => $value)
        {
            $tmp[] = $this->_db->quoteInto($key . ' = ?', $value);
        }

        $whereStr = implode(' AND ', $tmp);
        $this->_where = $whereStr;
    }

    /**
     * Insert Data into the log table.
     *
     * @param array $data Data to insert int he table
     *
     * @return void
     */
    public function writeData(array $data = array())
    {
        $isValid = true;
        if (is_array($data['L_Data']))
            $data['L_Data'] = $this->toStringData($data['L_Data']);

        if (!empty($data['L_Data']))
            $isValid = $this->_isCorrectFormat($data['L_Data']);
        if ($isValid)
            $this->insert($data, 1);
        else
            throw new Exception("Wrong format: please check the values for the L_Data field");
    }
    /**
     * Tests if data are already recorded in the log table.
     * According the result, it allows to define if data will be inseted.
     *
     * @param array $data Data to insert in the table.
     *
     * @return boolean
     */
    public function findRecords(array $data = array())
    {
        $exist = (bool) false;
        $result = 0;
        if (is_array($data['L_Data']))
            $data['L_Data'] = $this->toStringData($data['L_Data']);

        if (!empty($data['L_UserID']))
        {
            $select = $this->_db->select()
                ->from($this->_oDataTableName, 'count(*)')
                ->where('L_ModuleID = ?', empty($data['L_ModuleID']) ? $this->_moduleId : $data['L_ModuleID'])
                ->where('L_UserID = ?', $data['L_UserID'])
                ->where('L_Action = ?', $data['L_Action'])
                ->where('L_Data = ?', $data['L_Data']);

            $result = $this->_db->fetchOne($select);
        }

        if ($result > 0 )
            $exist = true;

        return $exist;

    }

    /**
     * @see DataObject::getAll()
     *
     * @param type $langId
     * @param type $array
     * @param type $id
     *
     * @return Zend_Db_Select
     */
    public function getAll($langId = null, $array = true, $id = null)
    {
        $select = parent::getAll($langId, false, $id);

        if ($select instanceof Zend_Db_Select && $this->_moduleId > 0)
            $select->where('L_ModuleID = ?', $this->_moduleId);
        if ($select instanceof Zend_Db_Select && $this->_userId > 0)
            $select->where('L_UserID = ?', $this->_userId);

        if ($array)
            if (is_null($id))
                return $this->_db->fetchAll($select);
            else
                return $this->_db->fetchRow($select);
        else
            return $select;
    }

    /**
     * Transforms an array into a string for the log informations.
     *
     * @param array $data An associative array with the values to log.
     *
     * @return string
     */
    public function toStringData(array $data = array())
    {
        $string = "";

        if (count($data) > 0)
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                    $value = implode('-', $value);

                $string .= $key . $this->pairSepar . $value . $this->separator;
            }
        else
            throw new Exception('Empty array : no data to build information to insert in the log');

        return $string;
    }

    /**
     * Tests if the string containing log informations is correctly formated. <br>
     * In the pair <b>param|value</b> param must be a string and value can't be empty.<br>
     * The string must finish with the defined separator (default = ||)
     *
     * @param string $string Informatins to insert in the log.
     *
     * @return boolean
     */
    protected function _isCorrectFormat($string = "")
    {
        (bool) $valid = false;

        $lastChar = substr($string, -2);
        if ($lastChar == $this->separator)
            $hasLast = true;

        $tmpPairs = $this->explodeData($string);
        $pairs    = $this->getDataPairs($tmpPairs);

        foreach ($pairs as $key => $value)
        {
            if ($key != 'other')
            {
                $keyIsString = is_string($key);
                $notEmpty    = empty($value);

                if (!$keyIsString && $notEmpty)
                    break;
            }
        }

        if($hasLast && $keyIsString && !$notEmpty)
            $valid = true;

        return $valid;
    }

    public function explodeData($string)
    {
        $tmpPairs = array();

        $tmpPairs = explode($this->separator, $string);
        $last = count($tmpPairs) - 1;
        unset($tmpPairs[$last]);

        return $tmpPairs;
    }

    /**
     * Creates an array with the parameters from the log table.
     * Explodes the string (or array) into pairs of param => value.
     *
     * @param string|array $pairs The parameters saved in the log data column
     * @return array
     */
    public function getDataPairs($pairs)
    {
        $data = array();

        if (is_string($pairs))
            $pairs = $this->explodeData ($pairs);

        foreach ($pairs as $value)
        {
            $tmpVal = explode($this->pairSepar, $value);

            if (strlen($tmpVal[0]) > 0)
                $data[$tmpVal[0]] = $tmpVal[1];
        }

        return $data;
    }

    public function getGlobalLog($data)
    {

    }

    public function updateIds ($oldId, $newId)
    {
        $where = $this->_db->quoteInto('L_UserID = ?', $oldId);
        $this->_db->update($this->_oDataTableName, array('L_UserID' => $newId), $where);
    }

    public function deleteWhere()
    {
        if (!empty($this->_where))
            $this->_db->delete($this->_oDataTableName, $this->_where);
    }
}
