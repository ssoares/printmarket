<?php
class ExtranetUsers extends Zend_Db_Table
{
    protected $_name = 'Extranet_Users';
    protected $_id = 0;
    protected $_includeAll = false;

    public function getIncludeAll()
    {
        return $this->_includeAllall;
    }

    public function setIncludeAll($all)
    {
        $this->_includeAll = $all;
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

    public function getAdminEqualOrOver($adminGroupID, $getData = false)
    {
        $dbConfig = $this->getAdapter()->getConfig();
        $select = $this->select();
        $columns = array("EU_ID","EU_LName","EU_FName",'EU_Email', 'EU_DefaultSite', 'EU_Disabled');
        if ($dbConfig['dbname'] == 'cible_admin'){
            $columns = array("EU_ID","EU_LName","EU_FName",'EU_Email');
        }
        $select->from('Extranet_Users',$columns)
                ->joinLeft('Extranet_UsersGroups','EU_ID=EUG_UserID AND EUG_GroupID >= '. $adminGroupID,array(""))
//                ->where("EUG_GroupID >= ?", $adminGroupID)
                ->group("EU_Email");
        if (!$this->_includeAll){
            $select->where('EU_Disabled = ?', 0);
        }
        if ($getData){
            $data = $this->fetchAll($select)->toArray();
        }else{
            $data = $select;
        }

        return $data;
    }

    public function populate()
    {
        $select = $this->select();
//        if (!$this->_includeAll){
//            $select->where('EU_Disabled = ?', 0);
//        }
        if (!empty($this->_id)){
            $select->where("EU_ID = ?", $this->_id);
            $data = $this->fetchRow($select);
        }else{
            $data = $this->fetchAll($select)->toArray();
        }

        return $data;
    }

    /**
     * Tests if the current data already exist for the id and langId.
     *
     * @param array   $tmpArray Data for the current element.
     * @param int     $langId   Id of the language to process search. Required
     *                          only if search done in index table too.
     * @param boolean $data     Defines if the language id has to be set in the
     *                          where clause. If false, then we are only
     *                          testing the data table and not the index table.
     *                          Default = true.
     *
     * @return int $exist Number of records
     */
    public function recordExists($email)
    {
        $select = $this->select()
            ->from('Extranet_Users', 'EU_ID')
            ->where("EU_Email = ?", $email);

        $exist = $this->_db->fetchOne($select);

        return $exist;
    }

}