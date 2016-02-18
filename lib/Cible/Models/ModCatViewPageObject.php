<?php

/**
 * Management of the tabel to retrieve page according to module and views data.
 *
 * @category  Cilbe
 * @package   Cible_Module
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ModCatViewPageObject.php 1159 2013-02-10 15:21:07Z ssoares $id
 */
class ModCatViewPageObject extends DataObject
{

    protected $_dataClass = 'ModCatViewPageData';
    protected $_indexClass = '';
    protected $_indexLanguageId = '';
    protected $_foreignKey = '';
    protected $_constraint      = '';
    protected $_data = array();
    protected $_query;
    protected $_exists = false;
    protected $_action;
    protected $_moduleCategory = array(2,7,8);
    protected $_excludeModule = array(1,5,18,20);
    protected $_blockId = null;

    public function setAction($action)
    {
        $this->_action = $action;

        return $this;
    }

    public function setModuleCategory($moduleCategory)
    {
        if (is_Array($moduleCategory))
            array_merge($this->_moduleCategory, $moduleCategory);
        else
            array_push($this->_moduleCategory, $moduleCategory);
    }
    public function setExcludeModule($excludeModule)
    {
        if (is_array($excludeModule))
            array_merge($this->_moduleCategory, $excludeModule);
        else
            array_push($this->_moduleCategory, $excludeModule);
    }

    public function deleteAll($data)
    {
        $categories = null;
        $tmpParam = explode(',',Cible_FunctionsBlocks::getBlockParameter($this->_blockId, 1));
        if (count($tmpParam) > 1){
            $data['MCVP_CategoryID'] = $tmpParam;
        }else{
            $data['MCVP_CategoryID'] = $tmpParam[0];
        }
        if (isset($data['Param1'])){
            $categories = $data['Param1'];
        }elseif(isset($data['MCVP_CategoryID'])){
            $categories = $data['MCVP_CategoryID'];
        }

        if (is_array($categories)){
            $tmp = $data;
            foreach ($categories as $value)
            {
                $tmp['MCVP_CategoryID'] = $value;
                $where = $this->_loadParams($tmp);
                $this->_db->delete($this->_oDataTableName, $where);
            }
        }else{
            $where = $this->_loadParams($data);
            $this->_db->delete($this->_oDataTableName, $where);
        }
    }

    public function manageData($data)
    {
        if (!empty($data))
        {
            switch ($this->_action)
            {
                case 'insert':
                    $data = $this->_setFields($data);
                    if (!in_array($data['MCVP_ModuleID'], $this->_excludeModule)){
                        $this->insert($data, 1);
                    }
                    break;
                case 'delete':
                    $where = $this->_loadParams($data);
                    $this->deleteAll($data);
                    break;

                default:
                    $this->_data['MCVP_PageID'] = $data['MCVP_PageID'];
                    $this->_loadParams($data);
                    if (!in_array($this->_data['MCVP_ModuleID'], $this->_excludeModule))
                    {
                        if ($this->_exists){
                            $this->update($this->_oDataTableName, $this->_data);
                        }else{
                            $tmpDel = $this->_data;
                            unset($tmpDel['MCVP_CategoryID']);
                            $this->deleteAll($tmpDel);
                            $this->insert($this->_data, 1);
                        }
                    }

                    break;
            }
        }
    }

    private function _loadParams($data)
    {
        if (isset($data['blockId'])){
            $this->_blockId = $data['blockId'];
        }
        $block = Cible_FunctionsBlocks::getBlockDetails($this->_blockId);
        $data['MCVP_ModuleID'] = (int)$block['B_ModuleID'];
        $this->_data['MCVP_ModuleID'] = (int)$block['B_ModuleID'];
        $viewName = Cible_FunctionsBlocks::getBlockParameter($this->_blockId, 999);
        $data['MCVP_ViewID'] = $this->_getViewId($viewName, (int)$block['B_ModuleID']);

        $exists = $this->findData($data);
        $this->_exists = count($exists) > 0 ? true : false;

        if (in_array($block['B_ModuleID'], $this->_moduleCategory)
            && empty($data['MCVP_CategoryID']))
        {
            $data['MCVP_CategoryID'] = (int)Cible_FunctionsBlocks::getBlockParameter($this->_blockId, 1);
            if (isset($data['Param1'])){
                $this->_data['MCVP_CategoryID'] = is_array($data['Param1']) ?
                   $data['Param1'] : (int)$data['Param1'];
            }
            $exists = $this->findData($this->_data);
            $this->_exists = count($exists) > 0 ? true : false;

        }
        elseif ($this->_exists && isset($this->_data['MCVP_CategoryID'])
            && $this->_data['MCVP_CategoryID'] == 0)
        {
            $tmp = $data;
            unset($tmp['MCVP_PageID']);
            $occurence = $this->findData($tmp);
            $this->_data['MCVP_CategoryID'] = count($occurence) - 1;
        }

        unset($data['blockId']);

        foreach ($this->_dataColumns as $key => $val){
            if (isset($data[$key])){
                $where[] = $this->_db->quoteInto($val . ' = ?', $data[$key]);
            }
        }
        if (empty($this->_action) && empty($this->_data['MCVP_ViewID']))
        {
            if (!isset($data['MCVP_CategoryID']))
                $where[] = 'MCVP_CategoryID = 0';

            $this->_data['MCVP_ViewID'] = (int)$this->_getViewId($data['Param999'], $block['B_ModuleID']);
        }

        $params = implode(' AND ', $where);

        return $params;
    }
    private function _setFields($data)
    {
        $data['MCVP_ViewID'] = $this->_getViewId($data['Param999'], $data['MCVP_ModuleID']);
        if (in_array($data['MCVP_ModuleID'], $this->_moduleCategory) && !empty($data['Param1'])){
            $data['MCVP_CategoryID'] = $data['Param1'];
        }elseif (isset($data['MCVP_CategoryID'])
            && $data['MCVP_CategoryID'] == 0){
            $tmp = $data;
            unset($tmp['MCVP_PageID']);
            $occurence = $this->findData($tmp);
            $data['MCVP_CategoryID'] = count($occurence);
        }

        return $data;
    }
    private function _getViewId($viewName, $moduleId)
    {
        $select = $this->_db->select()
            ->distinct()
            ->from('ModuleViews', array('MV_ID'))
//            ->join('ModuleViewsIndex', 'MVI_ModuleViewsID = MV_ID', array())
            ->where('ModuleViews.MV_Name = ?', $viewName)
            ->where('ModuleViews.MV_ModuleID = ?', $moduleId)
            ;

        $id = $this->_db->fetchOne($select);

        return $id;

    }

    public function insert($data, $langId)
    {
        $categories = null;
        if (isset($data['Param1'])){
            $categories = $data['Param1'];
        }elseif(isset($data['MCVP_CategoryID'])){
            $categories = $data['MCVP_CategoryID'];
        }
        if (is_array($categories)){
            $tmp = $data;
            foreach ($categories as $value)
            {
                $tmp['MCVP_CategoryID'] = $value;
                parent::insert($tmp, $langId);
            }
        }else{
            return parent::insert($data, $langId);
        }
    }

    public function update($table, $data)
    {
        $categories = null;
        if (isset($data['Param1'])){
            $categories = $data['Param1'];
        }elseif(isset($data['MCVP_CategoryID'])){
            $categories = $data['MCVP_CategoryID'];
        }
        if (is_array($categories)){
            $tmp = $data;
            foreach ($categories as $value)
            {
                $tmp['MCVP_CategoryID'] = $value;
                $exists = $this->findData($tmp);
                if (!empty($exists)){
                    $where = $this->_loadParams($tmp);
                    $this->_db->update($table, $tmp, $where);
                }else{
                    $this->insert($tmp, 1);
                }
            }
        }else{
            $where = $this->_loadParams($data);
            $this->_db->update($table, $data, $where);
        }
    }

}

