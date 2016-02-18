<?php
class ExtranetGroupsRolesResources extends Zend_Db_Table
{
    protected $_name = 'Extranet_Groups_RolesResources';
    protected $_groupId = 0;

    public function getGroupId()
    {
        return $this->_groupId;
    }

    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    public function populate()
    {
        $select = $this->select();
        if (!empty($this->_groupId)){
            $select->where('EGRRP_GroupID = ?', $this->_groupId);
        }

        return $this->fetchAll($select)->toArray();
    }
}