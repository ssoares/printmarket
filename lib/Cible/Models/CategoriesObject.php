<?php

class CategoriesObject extends DataObject
{

    protected $_dataClass = 'Categories';
    protected $_dataId = 'C_ID';
    protected $_dataColumns = array(
        'ModuleID' => 'C_ModuleID',
        'ShowInRss' => 'C_ShowInRss',
        'RssItemsCount' => 'C_RssItemsCount',
        'C_ParentID' => 'C_ParentID' 
    );
    protected $_indexClass = 'CategoriesIndex';
    protected $_indexId = 'CI_CategoryID';
    protected $_indexLanguageId = 'CI_LanguageID';
    protected $_indexColumns = array(
        'Title' => 'CI_Title',
        'WordingShowAllRecords' => 'CI_WordingShowAllRecords',
        'CI_ReturnToList' => 'CI_ReturnToList'
    );

    public function saveTeamCat($data, $langId)
    {
        $cat = $this->findData($data);
        if (!empty($cat)){
            $id = $cat[0][$this->_dataId];
            $this->save($id, $data, $langId);
        }else{
            $id = $this->insert($data, $langId);
        }

        return $id;
    }
}