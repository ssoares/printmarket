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
class Cible_Controller_Action_Helper_SwitchDB extends Zend_Controller_Action_Helper_Abstract
{
    const CFG_EXT = '.ini';
    /**
     * The first adapter before change.
     * @var Zend_Db_Adapter_Pdo_Mysql
     */
    private $_initialDb = null;
    /**
     * The new adapter after change
     * @var Zend_Db_Adapter_Pdo_Mysql
     */
    private $_currentDb = null;
    /**
     * The entity name. It is the config file name too.
     * @var string
     */
    private $_entity = null;
    /**
     * The table name to retrieve the auto-increment value.
     * @var string
     */
    private $_tableName = '';
    /**
     * The current entity configuration.
     * @var Zend_Config
     */
    private $_config = '';
    /**
     * The current db configuration.
     * @var Zend_Config
     */
    private $_multidb = '';
    /**
     * The environnement
     * @var string development|sandbox|staging|production
     */
    private $_type = null;

    /**
     * Get the first db adapter
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function getInitialDb()
    {
        return $this->_initialDb;
    }

    /**
     * Get the new db adapter
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function getCurrentDb()
    {
        return $this->_currentDb;
    }

    /**
     * Set the first db adapter
     * @param Zend_Db_Adapter_Pdo_Mysql $initialDb
     * @return \Cible_Controller_Action_Helper_SwitchDB
     */
    public function setInitialDb(Zend_Db_Adapter_Pdo_Mysql $initialDb)
    {
        $this->_initialDb = $initialDb;
        return $this;
    }
    /**
     * Get the entity value
     * @return string
     */
    public function getEntity()
    {
        return $this->_entity;
    }
    /**
     * Get the type value
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }
    /**
     * Set the entity value
     * @param string $entity
     * @return \Cible_Controller_Action_Helper_SwitchDB
     */
    public function setEntity($entity)
    {
        $this->_entity = $entity;
        return $this;
    }
    /**
     * Set the type value
     * @param string $type
     * @return \Cible_Controller_Action_Helper_SwitchDB
     */
    public function setType($type)
    {
        $this->_type = $type;
        return $this;
    }

    /**
     * Set the new db adapter
     * @param type $currentDb
     * @return \Cible_Controller_Action_Helper_SwitchDB
     */
    public function setCurrentDb(Zend_Db_Adapter_Pdo_Mysql $currentDb)
    {
        $this->_currentDb = $currentDb;
        return $this;
    }
    /**
     * Set the class properties. If it does not exist then throw an exception.
     * @param array $options
     * @throws Zend_Controller_Action_Exception
     */
    public function setOptions(array $options)
    {
        foreach($options as $key => $value)
        {
            $property = '_' . $key;
            if (!array_key_exists($property, get_class_vars(get_class()))){
                throw new Zend_Controller_Action_Exception('Property ' . $key . ' does not exist.');
            }
            $this->$property = $value;
        }
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }

    public function direct()
    {
        return 'passe';
    }

    public function loadDbConfig()
    {
        $appPath = APPLICATION_PATH . '/application/';
        try{
            $configFile = 'config/' . $this->_entity . self::CFG_EXT;
            $cfg = new Zend_Config_Ini($appPath . $configFile, $this->_type);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
        $defaultCfg = new Zend_Config_Ini($appPath . "config/config.ini", 'general');
        $config = new Zend_Config(array(), true);
        $config->merge($defaultCfg);
        $config->merge($cfg);
        $config->readOnly();
        $entity = $this->_entity;
        $this->_config = $config;
        $this->_multidb = $this->_config->resources->multidb->$entity;
        $this->_currentDb = new Zend_Db_Adapter_Pdo_Mysql($this->_multidb);

        return $this;
    }

    public function getLastAutoIncrement($tableName = '')
    {
        if (empty($tableName) && !empty($this->_tableName)){
            $tableName = $this->_tableName;
        }
        if (empty($tableName)){
            throw new Zend_Controller_Action_Exception('$tableName is empty. '
                . 'Use the setter property or the parameter');
        }

        $query = $this->_currentDb->select()
            ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
            ->where( $this->_currentDb->quoteInto('TABLE_NAME = ?', $tableName))
            ->where('TABLE_SCHEMA = ?', $this->_multidb->dbname);

        return (int)$this->_currentDb->fetchOne($query);

    }

}