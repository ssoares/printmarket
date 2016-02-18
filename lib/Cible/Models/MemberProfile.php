<?php

/**
 * Cible Solutions - EDITH
 * Profile and account managament.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: MemberProfile.php 1018 2012-08-10 20:16:23Z freynolds $MP_Password
 */

/**
 * Manage profiles data. This part is for the data more specifics.
 * It depends on the project and have to be modified if necessary.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class MemberProfile extends GenericProfile
{

    protected $_table = 'MemberProfiles';
    protected $_fields = array(
        'company' => 'MP_CompanyName',
//        'functionCompany' => 'MP_CompanyRole',
        'address' => 'MP_AddressId',
//        'addrBill'        => 'MP_BillingAddrId',
 //       'addrShip'        => 'MP_ShippingAddrId',
//        'hasAccount'      => 'MP_hasAccount',
 //       'accountNum'       => 'MP_AccountNumber' ,
//        'noProvTax'       => 'MP_NoProvTax',
//        'noFedTax'        => 'MP_NoFedTax',
        'password' => 'MP_Password',
        'hash' => 'MP_Hash',
        'validatedEmail' => 'MP_ValidateEmail',
        'status' => 'MP_Status'
    );
    protected $_db;

    /**
     * Class constructor.
     * Instantiates the db object for queries.
     *
     * @param array $options
     *
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_db = Zend_Registry::get('db');
    }

    /**
     * List of the table fields
     *
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    /**
     * Return the table name for queries
     *
     * @return string
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Return the keys of the columns. These are the name define by user.<br />
     * This array contains generic and specifics column names.
     *
     * @return array
     */
    public function getProfileProperties()
    {
        return array_merge(parent::getProfileProperties(), array_keys($this->_fields));
    }

    /**
     * Fetch data from database according to the user id.
     *
     * @param int $memberID The member id.
     *
     * @return array
     */
    public function getMemberDetails($memberID)
    {
        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields)
            ->join($this->_table, 'GP_MemberID = MP_GenericProfileMemberID', $this->_fields)
            ->where('GP_MemberID = ?', $memberID);

        return $this->_db->fetchRow($select);
    }

    /**
     * Fetch data for authencication process.
     *
     * @param array $authenticationColumns
     *
     * @return array
     */
    public function authenticateMember($authenticationColumns)
    {
        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields)
            ->join($this->_table, 'GP_MemberID = MP_GenericProfileMemberID', $this->_fields);

        $columns = array_merge($this->_genericFields, $this->_fields);
        foreach ($authenticationColumns as $key => $value)
        {
            if (isset($columns[$key]))
                $select->where("{$columns[$key]} = ?", $value);
        }

        return $this->_db->fetchRow($select);
    }
    /**
     * Fetch users data according to the filters values.<br />
     * Only return one record, the filters array must contain user id or the
     * last record wil be returned.
     *
     * @param array $filters List of values to build filters.
     */
    public function findMember($filters = array())
    {

        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields)
            ->join($this->_table, 'GP_MemberID = MP_GenericProfileMemberID', $this->_fields);

        $columns = array_merge($this->_genericFields, $this->_fields);
        foreach ($filters as $key => $value)
        {
            if (isset($columns[$key]))
                $select->where("{$columns[$key]} like '%{$value}%'");
        }

        return $this->_db->fetchRow($select);
    }

    /**
     * Fetch users data according to the filters values.
     *
     * @param array $filters List of values to build filters.
     */
    public function findMembers($filters = array())
    {

        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields)
            ->join($this->_table, 'GP_MemberID = MP_GenericProfileMemberID', $this->_fields);

        $columns = array_merge($this->_genericFields, $this->_fields);
        foreach ($filters as $key => $value)
        {
            if (isset($columns[$key]))
                $select->where("{$columns[$key]} like '%{$value}%'");
        }

        return $this->_db->fetchAll($select);
    }

    /**
     * Return an oject to build queries.
     *
     * @return Zend_Db_Table_Select
     */
    public function getSelectStatement()
    {
        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields)
            ->join($this->_table, 'GP_MemberID = MP_GenericProfileMemberID', $this->_fields);

        return $select;
    }

    /**
     * Add a new user to the system and return its id.
     *
     * @access protected
     * @param array $data Data form the registration form.
     *
     * @return int $id Id of the latest record.
     */
    protected function insert($data)
    {
        $id = parent::insert($data);

        $columns = array();
        $keys = array_keys($this->_fields);

        foreach ($data as $col_key => $col_value)
        {
            if (in_array($col_key, $keys))
            {
                $columns[$this->_fields[$col_key]] = $col_value;
            }
        }

        $columns['MP_GenericProfileMemberID'] = $id;

        return $this->_db->insert($this->_table, $columns);
    }

    /**
     * Save data when member changes its profile informations.
     *
     * @access protected
     * @param int   $memberId Id of the member.
     * @param array $data     Data from post action.
     *
     * @return void
     */
    protected function save($memberId, $data)
    {

        parent::save($memberId, $data);

        $columns = array();
        $keys = array_keys($this->_fields);

        foreach ($data as $col_key => $col_value)
        {
            if (in_array($col_key, $keys))
            {
                $columns[$this->_fields[$col_key]] = $col_value;
            }
        }

        if (!$this->findMembers(array(
                'member_id' => $memberId
            )))
        {
            $columns['MP_GenericProfileMemberID'] = $memberId;
            $this->_db->insert($this->_table, $columns);
        }
        else
        {
            $where = $this->_db->quoteInto('MP_GenericProfileMemberID = ?', $memberId);
            $this->_db->update($this->_table, $columns, $where);
        }
    }

    /**
     * Delete a member profile.
     *
     * @access public
     * @param type $memberID Id of the member to delete.
     */
    protected function delete($memberID)
    {
        //parent::delete($memberID);

        $where = "MP_GenericProfileMemberID = $memberID";
        $this->_db->delete($this->_table, $where);
    }

    /**
     * Allows to add values of taxes for orders to the customer data.
     *
     * @param array $memberData
     *
     * @return array
     */
    public function addTaxRate(array $memberData)
    {
        $data = array();
        $memberId = $memberData['member_id'];
        $addrId = $memberData['addrBill'];

        $oAddres = new AddressObject();
        $oTaxes = new TaxesObject();

        $stateId = $oAddres->getStateId($addrId);
        $taxRate = $oTaxes->getTaxData($stateId);

        $memberData['taxProv'] = $taxRate['TP_Rate'];
        $memberData['taxCode'] = $taxRate['TZ_GroupName'];

        return $memberData;
    }
}