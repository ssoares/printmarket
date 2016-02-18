<?php
/**
 * Cible Solutions - Vêtements SP
 * Retailer management. Data import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Retailer
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: CitiesObject.php 1372 2013-12-27 22:07:54Z ssoares $
 */

/**
 * Manage data for cities
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Retailer
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class CitiesObject extends DataObject
{
    protected $_dataClass   = 'CitiesData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();
    protected $_filter = false;

    public function setFilter ($value)
    {
        $this->_filter = $value;
    }

    /**
     * Get cities list for th selected state.
     *
     * @param int $stateId
     *
     * @return void
     */
    public function getCitiesDataByStates($stateId)
    {
        $select = $this->_db->select()
                    ->from($this->_oDataTableName)
                    ->joinLeft('States', 'C_StateID = S_ID', array())
                    ->order('C_Name');

        if ($this->_filter)
        {
            $select->distinct();
            $select->joinLeft('AddressData', 'A_CityId = C_ID', '');
            $select->joinLeft('RetailersData', 'R_AddressId = A_AddressId', '');
            $select->where('R_Status= 2');
        }

        if (!is_numeric($stateId))
            $select->where('S_Identifier = ?', $stateId);
        elseif((int) $stateId)
            $select->where('S_ID = ?', $stateId);

        $cities = $this->_db->fetchAll($select);

        return $cities;
    }

    protected $_query;

    /**
     * Fetch orders data to generate csv file.
     *
     * @param string $orderColumns The list of colums to set in the select statement.
     * @param string $status       The order status to export only available orders.
     * @param int    $id           Order id, allows to fetch data for an other part of the file.
     *
     * @return array
     */
    public function autocompleteSearch($value, $langId = 1, $limit = null, $state = 0)
    {
        $select = $this->_db->select()
                ->distinct()
                ->from($this->_oDataTableName,
                        'C_Name')
                ->where('C_StateId = ?', $state)
                ->where('C_Name like ?', $value . '%');

        if ($limit > 0)
            $select->limit($limit);

        $data = $this->_db->fetchAll($select);

        return $data;
    }
}