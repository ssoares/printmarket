<?php
class ExtranetResources extends Zend_Db_Table
{
    protected $_name = 'Extranet_Resources';

    public function populate()
    {
        $select = $this->select();
        $data = $this->fetchAll($select);

        return $data;
    }
}