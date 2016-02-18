<?php

class MessagesCollection extends Zend_Db_Table
{
    /**
     * The database object instance
     *
     * @var Zend_Db
     */
    protected $_db;
    /**
     * Current language
     *
     * @var int
     */
    protected $_current_lang;
    /**
     * Block id
     *
     * @var int
     */
    protected $_blockID = null;
    /**
     * Parameters of the block
     *
     * @var array
     */
    protected $_blockParams = array();

    protected $_limit     = 1;


    /**
     * Fetch the parameter value
     *
     * @param int $param_name Number identifying the parameter
     *
     * @return string
     */
    public function getBlockParam($param_name)
    {
        return $this->_blockParams[$param_name];
    }

    /**
     * Return the parameters array
     *
     * @return array
     */
    public function getBlockParams()
    {
        return $this->_blockParams;
    }

    /**
     * Class constructor
     *
     * @param int $blockID Id of the block. Default value = null
     */
    public function __construct($params)
    {
        $this->_current_lang = Zend_Registry::get('languageID');
        $this->_db           = Zend_Registry::get('db');

        $this->setParameters($params);
    }

    public function setParameters($params = array())
    {
        foreach ($params as $property => $value)
        {
            if ($property == 'BlockID')
                $property = 'blockID';

            $methodName = 'set' . ucfirst($property);

            if (property_exists($this, '_' . $property)
                && method_exists($this, $methodName))
            {
                $this->$methodName($value);
            }
        }
    }

    public function setBlockID($value)
    {
        $this->_blockID = $value;
        $_params = Cible_FunctionsBlocks::getBlockParameters($value);

        foreach ($_params as $param)
        {
            $this->_blockParams[$param['P_Number']] = $param['P_Value'];
        }
    }


    public function setLimit($value)
    {
        $this->_limit = $value;
    }

    public function getDetails($id)
    {

    }

    /**
     * Get the list of the products for the current category
     *
     * @param int $limit
     *
     * @return array
     */
    public function getList()
    {
        $oData = new MessagesObject();

        $select = $oData->getAll(Zend_Registry::get('languageID'), false);
        $select->where('MA_Online = ?', 1);
        $select->order('MA_ID DESC');

        $data = $this->_db->fetchRow($select);

        return $data;
    }
}