<?php
class ExtranetRoles extends Zend_Db_Table
{
    protected $_name = 'Extranet_Roles';

    public function populate()
    {
        $select = $this->select();
        $data = $this->fetchAll($select);

        return $data;
    }
}