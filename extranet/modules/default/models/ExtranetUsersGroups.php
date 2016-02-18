<?php
class ExtranetUsersGroups extends Zend_Db_Table
{
    protected $_name = 'Extranet_UsersGroups';
    protected $_adminId = 0;
    protected $_groupId = 0;
    protected $_pageId = 0;

    public function getAdminId()
    {
        return $this->_adminId;
    }

    public function getGroupId()
    {
        return $this->_groupId;
    }

    public function getPageId()
    {
        return $this->_pageId;
    }

    public function setAdminId($adminId)
    {
        $this->_adminId = $adminId;
        return $this;
    }

    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }
    public function setPageId($pageId)
    {
        $this->_pageId = $pageId;
        return $this;
    }

    public function getFirstLevelsAdmin($option = 'data')
    {
        $select = $this->select()
            ->where('EUG_UserID = ?', $this->_adminId)
//            ->where('EUG_GroupID = 1');
            ->where('EUG_GroupID = 1 OR EUG_GroupID =2');

        $data = $this->fetchRow($select);
        if ($option == 'count'){
            $data  = count($data);
        }

        return $data;

    }

    public function getPermissionsPage($permission = '')
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from('Extranet_UsersGroups')
            ->join('Extranet_Groups','EG_ID = EUG_GroupID')
            ->join('Extranet_Groups_Pages_Permissions','EGPP_GroupID = EUG_GroupID')
            ->where('EUG_UserID = ?', $this->_adminId)
            ->where('EGPP_PageID = ?', $this->_pageId)
            ->where('EG_Status = "active"');


        if ($permission == "structure"){
            $select->where('EGPP_Structure = "Y"');
        }elseif ($permission == "data"){
            $select->where('EGPP_Data = "Y"');
        }

        $row = $this->fetchRow($select);

        if(count($row) == 0){
            return false;
        }else{
            return true;
        }

    }

    public function getGroups()
    {
        $select = $this->select();

        if (!empty($this->_adminId))
            $select->where('EUG_UserID = ?', $this->_adminId);

        return $this->fetchAll($select);
    }
}