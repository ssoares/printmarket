<?php
class ExtranetGroupsPagesPermissions extends Zend_Db_Table
{
    protected $_name = 'Extranet_Groups_Pages_Permissions';
    protected $_groupId = 0;
    protected $_pageId = 0;

    public function getGroupId()
    {
        return $this->_groupId;
    }

    public function getPageId()
    {
        return $this->_pageId;
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

    public function getPermission($permission = '')
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from('Extranet_Groups_Pages_Permissions')
                ->where('EGPP_GroupID = ?', $this->_groupId)
                ->where('EGPP_PageID = ?', $this->_pageId);

        if ($permission == "structure"){
            $select->where('EGPP_Structure = "Y"');
        }elseif ($permission == "data"){
            $select->where('EGPP_Data = "Y"');
        }

        $row = $this->fetchRow($select);
        if (count($row) == 0){
            return false;
        }else{
            return true;
        }
    }

}