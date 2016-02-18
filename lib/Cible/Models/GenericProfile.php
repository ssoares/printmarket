<?php

/**
 * Cible Solutions - EDITH
 * Profile and account managament.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: GenericProfile.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Manage profiles data.
 *
 * @category  Cible
 * @package   Cible_Models
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class GenericProfile
{
    
    protected $_genericTable = 'GenericProfiles';
    protected $_genericFields = array(
        'member_id'  => 'GP_MemberID',
        'salutation' => 'GP_Salutation',
        'firstName'  => 'GP_FirstName',
        'lastName'   => 'GP_LastName',
        'email'      => 'GP_Email',
        'language'   => 'GP_Language'
    );
    protected $_db;

    /**
     * List of the table fields
     * 
     * @return array
     */
    public function getGenericFields()
    {
        return $this->_genericFields;
    }
    /**
     * Return the table name for queries
     * @return string
     */
    public function getGenericTable()
    {
        return $this->_genericTable;
    }
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
     * Return the keys of the columns. These are the name define by user.
     * 
     * @return array
     */
    public function getProfileProperties()
    {

        return array_keys($this->_genericFields);
    }

    /**
     * Return an oject to build queries.
     * 
     * @return Zend_Db_Table_Select
     */
    public function getSelectStatement()
    {
        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields);

        return $select;
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
            ->where('GP_MemberID = ?', $memberID);

        return $this->_db->fetchRow($select);
    }

    /**
     * Add a new user to the system and return its id.
     * 
     * @access public
     * @param array $data Data form the registration form.
     * 
     * @return int $id Id of the latest record.
     */
    public function addMember($data)
    {

        return $this->insert($data);
    }

    /**
     * Save data when member changes its profile informations.
     * 
     * @access public
     * @param int   $memberId Id of the member.
     * @param array $data     Data from post action.
     * 
     * @return void
     */
    public function updateMember($memberId, $data)
    {
        $this->save($memberId, $data);
    }
    
    /**
     * Delete a member profile.
     * 
     * @access public
     * @param type $memberID Id of the member to delete.
     */
    public function deleteMember($memberID)
    {
        $this->delete($memberID);
    }

    /**
     * Fetch users data according to the filters values.<br />
     * Filters are simple orWhere, we'll have to work on that
     * 
     * @param array $filters List of values to build filters.
     */
    public function findMembers($filters = array())
    {

        $select = $this->_db->select();
        $select->from($this->_genericTable, $this->_genericFields);

        foreach ($filters as $key => $value)
        {
            if (isset($this->_genericFields[$key]))
            {
                if (is_string($value ))
                    $select->where("{$this->_genericFields[$key]} like '%{$value}%'");
                elseif (is_integer($value))
                    $select->where($this->_genericFields[$key] . ' = ?',$value);
            }
        }

        return $this->_db->fetchAll($select);
    }

    /**
     * Save data
     * 
     * @access protected
     * @param type $memberId
     * @param type $data 
     * 
     * @return void
     */
    protected function save($memberId, $data)
    {
        $memberId = (int)$memberId;

        $columns = array();
        $keys = array_keys($this->_genericFields);

        foreach ($data as $col_key => $col_value)
        {
            if (in_array($col_key, $keys))
            {
                $columns[$this->_genericFields[$col_key]] = $col_value;
            }
        }
        if (!empty($columns))
        {
            $where = $this->_db->quoteInto('GP_MemberID = ?', $memberId);
            $this->_db->update($this->_genericTable, $columns, $where);
        }
    }

    /**
     * Insert data 
     * 
     * @access protected
     * @param array $data
     * 
     * @return int Last id in the db
     */
    protected function insert($data)
    {
        $columns = array();
        $keys = array_keys($this->_genericFields);

        foreach ($data as $col_key => $col_value)
        {
            if (in_array($col_key, $keys))
            {
                $columns[$this->_genericFields[$col_key]] = $col_value;
            }
        }

        if (empty($columns))
            Throw new Exception('GenericProfile properties are missing');

        $this->_db->insert($this->_genericTable, $columns);

        return $this->_db->lastInsertId();
    }

    /**
     * Delete data for given id.
     * 
     * @access protected
     * @param  int $memberID 
     * 
     * @return void
     */
    protected function delete($memberID)
    {
        $where = "GP_MemberID = $memberID";
        $this->_db->delete($this->_genericTable, $where);

        $where = "MP_GenericProfileMemberID = $memberID";
        $this->_db->delete('MemberProfiles', $where);

        $where = "NP_ProfileId = $memberID";
        $this->_db->delete('NewsletterProfiles', $where);
    }

}