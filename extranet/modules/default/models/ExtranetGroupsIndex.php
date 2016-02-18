<?php
class ExtranetGroupsIndex extends Zend_Db_Table
{
    protected $_name = 'Extranet_GroupsIndex';
    protected $_titleField = 'EGI_Name';
    protected $_groupName = '';
    protected $_groupId = 0;

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function getGroupName()
    {
        return $this->_groupName;
    }

    public function getGroupId()
    {
        return $this->_groupId;
    }

    public function setTitleField($titleField)
    {
        $this->_titleField = $titleField;
        return $this;
    }

    public function setGroupName($groupName)
    {
        $this->_groupName = $groupName;
        return $this;
    }

    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    public function recordExists()
    {
        $findGroup = new ExtranetGroupsIndex();
            $select = $findGroup->select()
            ->where('EGI_Name = ?', $this->_groupName);
        if ($this->_groupId > 0){
            $select->where('EGI_GroupID <> ?', $this->_groupId);
        }
        $findGroupData = $findGroup->fetchAll($select);

        return $findGroupData->count();
    }
}