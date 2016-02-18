<?php
class ExtranetRolesResources extends Zend_Db_Table
{
    protected $_name = 'Extranet_RolesResources';
    protected $_id = 0;
    protected $_roleId = 0;
    protected $_orderBy = '';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getRoleId()
    {
        return $this->_roleId;
    }

    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    public function setRoleId($roleId)
    {
        $this->_roleId = $roleId;
        return $this;
    }

    public function setOrderBy($orderBy)
    {
        $this->_orderBy = $orderBy;
        return $this;
    }

    public function populate()
    {
        $select = $this->select()->setIntegrityCheck(false);
        if (!empty($this->_roleId)){
            $select->where('ERR_RoleID = ?', $this->_roleId);
        }
        if (!empty($this->_orderBy)){
            $select->order($this->_orderBy);
        }

       return $this->fetchAll($select);
    }
    public function getRelatedRole()
    {
        $select = $this->_db->select();
        $select->from($this->_name)
            ->joinLeft('Extranet_Roles', 'ER_ID = ERR_RoleID');
        if (!empty($this->_id)){
            $select->where('ERR_ID = ?', $this->_id);
        }
        if (!empty($this->_orderBy)){
            $select->order($this->_orderBy);
        }

        return $this->_db->fetchRow($select);

    }

    public function getRoleRessources()
    {
        $select = $this->_db->select();
        $colums = array(
            'ResourceName'=>'Extranet_Resources.ER_ControlName',
            'RoleName'=>'Extranet_Roles.ER_ControlName',
            'ERR_InheritedParentID','ERR_ID');

        $select->from($this->_name, $colums)
            ->join('Extranet_Resources', 'Extranet_Resources.ER_ID = ERR_ResourceID')
            ->join('Extranet_Roles', 'Extranet_Roles.ER_ID = ERR_RoleID');

         if (!empty($this->_id)){
            $select->where('ERR_ID = ?', $this->_id);
         }

        return $this->_db->fetchAll($select);
    }
}