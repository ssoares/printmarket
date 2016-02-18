<?php
class ExtranetRolesResourcesPermissions extends Zend_Db_Table
{
    protected $_name = 'Extranet_RolesResourcesPermissions';
    protected $_roleResourceId = 0;

    public function getRoleResourceId()
    {
        return $this->_roleResourceId;
    }

    public function setRoleResourceId($roleResourceId)
    {
        $this->_roleResourceId = $roleResourceId;
        return $this;
    }

    public function populate()
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->join('Extranet_Permissions', 'EP_ID = ERRP_PermissionID');
        if (!empty($this->_roleResourceId)){
            $select->where('ERRP_RoleResourceID = ?', $this->_roleResourceId);
        }

        return $this->_db->fetchAll($select);
    }
}