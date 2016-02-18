<?php

class GroupeObject extends DataObject
{

    protected $_dataClass = 'GroupeData';
    protected $_indexClass = 'GroupeIndex';
    protected $_indexLanguageId = 'GI_LanguageID';
    protected $_foreignKey = '';

    public function groupsList()
    {
        $list = array();
        $data = $this->getAll(Zend_Registry::get('languageID'));

        foreach ($data as $value)
            $list[$value['G_ID']] = $value['GI_Name'];

        return $list;
    }
}
