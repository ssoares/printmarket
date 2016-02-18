<?php
class ExtranetGroups extends Zend_Db_Table
{
    protected $_name = 'Extranet_Groups';
    protected $_langId = 0;
    protected $_id = 0;
    protected $_limitId = 2;

    public function getLangId()
    {
        return $this->_langId;
    }

    public function setLangId($langId)
    {
        $this->_langId = $langId;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getLimitId()
    {
        return $this->_limitId;
    }

    public function setLimitId($limitId)
    {
        $this->_limitId = $limitId;
        return $this;
    }

    public function getList($searchText = '', $listOrder = '')
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('Extranet_Groups')
            ->join('Extranet_GroupsIndex', 'Extranet_GroupsIndex.EGI_GroupID = Extranet_Groups.EG_ID')
            ->where('Extranet_GroupsIndex.EGI_LanguageID = ?', $this->_langId)
            ->where('EG_ID > ?', $this->_limitId);

        /* search */
        if ($searchText <> ""){
          $select->Where("Extranet_GroupsIndex.EGI_Name LIKE '%".$searchText."%'");
        }
        /* order */
        if ($listOrder <> ""){
          $select->order($listOrder);
        }
        $select->order('EGI_Name');
        return $this->fetchAll($select);
    }

    public function populate()
    {
        $select = $this->select()
            ->setIntegrityCheck(false)
            ->from('Extranet_Groups')
            ->join('Extranet_GroupsIndex', 'Extranet_Groups.EG_ID = Extranet_GroupsIndex.EGI_GroupID');
        if (!empty($this->_id)){
            $select->where("EG_ID = ?", $this->_id);
        }
        if (!empty($this->_langId)){
            $select->where("EGI_LanguageID = ?", $this->_langId);
        }

        return $this->fetchRow($select);
    }
}