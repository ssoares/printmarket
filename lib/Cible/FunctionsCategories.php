<?php
abstract class Cible_FunctionsCategories
{
    public static function getRootCategoriesList($moduleID, $parent = 0){
        $db  = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Categories', array('C_ID'))
            ->joinInner('CategoriesIndex', 'CategoriesIndex.CI_CategoryID = Categories.C_ID', array('CI_Title','CI_WordingShowAllRecords'))
            ->where('CategoriesIndex.CI_LanguageID = ?', Zend_Registry::get('languageID'))
            ->where('Categories.C_ParentID = ?', $parent)
            ->where('Categories.C_ModuleID = ?', $moduleID);

        return $db->fetchAll($select);
    }

    public static function getCategoryDetails($categoryID = null, $lang = null){

        if($lang == null)
            $lang = Zend_Registry::get('languageID');

        $db  = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Categories', array('C_ID','C_PageID'))
            ->joinInner('CategoriesIndex', 'CategoriesIndex.CI_CategoryID = Categories.C_ID', array('CI_Title','CI_WordingShowAllRecords','CI_ReturnToList'))
            ->where('CategoriesIndex.CI_LanguageID = ?', $lang);

        if (!is_null($categoryID))
        {
            $select->where('Categories.C_ID = ?', $categoryID);
        $row = $db->fetchRow($select);

        if( !$row )
            $row = array();
        }
        else
        {
            $row = $db->fetchAll($select)->toArray();
        }
        return $row;
    }

    public static function getFilterCategories($moduleID, $parent = 0){
        $categories = self::getRootCategoriesList($moduleID, $parent);
        $choices = array('' => Cible_Translation::getCibleText('filter_empty_category'));

        foreach($categories as $category)
        {
            if(!isset($choices[$category['C_ID']]))
            {
                $choices[$category['C_ID']] = $category['CI_Title'];
            }
        }

        return $choices;
    }

    public static function getCategoryViews($module_id, $category_id = null){

        $db  = Zend_Registry::get("db");
        $select = $db->select()
            ->from('ModuleViews', array('MV_ID','MV_Name'))
            ->where('ModuleViews.MV_ModuleID = ?', $module_id);

        if($category_id){
            $select->joinRight('ModuleCategoryViewPage', 'ModuleViews.MV_ID = ModuleCategoryViewPage.MCVP_ViewID', array('MCVP_ID', 'MCVP_PageID'))
                   ->joinLeft('PagesIndex', 'PagesIndex.PI_PageID = ModuleCategoryViewPage.MCVP_PageID', array('PI_PageIndex'))
                   ->where('ModuleCategoryViewPage.MCVP_CategoryID = ?', $category_id);
        }

        $views = $db->fetchAll($select);

        if( !$views ){
            $select = $db->select()
                ->from('ModuleViews', array('MV_ID','MV_Name'))
                ->where('ModuleViews.MV_ModuleID = ?', $module_id);

            $views = $db->fetchAll($select);
        }

        return $views;
    }

    public static function getDefaultCategoryPerModuleID($moduleID=0){
        $db  = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Categories', array("C_ID"))
            ->where("C_ModuleID=?" , $moduleID)
            ->order("C_ID ASC");


        return $db->fetchOne($select);
    }



    public static function getPagePerCategoryView($category_id, $view_name, $module=0, $lang = null, $pageOnly = false){
        if (is_numeric($category_id)){
            if( is_null($lang) )
                $lang = Zend_Registry::get('languageID');

            $db  = Zend_Registry::get("db");
            $select = $db->select()
            ->from('ModuleCategoryViewPage', array())
            ->join('ModuleViews', 'ModuleViews.MV_ID = ModuleCategoryViewPage.MCVP_ViewID', array())
            ->join('ModuleViewsIndex', 'ModuleViewsIndex.MVI_ModuleViewsID = ModuleCategoryViewPage.MCVP_ViewID', array('MVI_ActionName'))
            ->join('PagesIndex', 'PagesIndex.PI_PageID = ModuleCategoryViewPage.MCVP_PageID', array('PI_PageIndex'))
            ->where('ModuleCategoryViewPage.MCVP_CategoryID = ?', $category_id)
            ->where('ModuleViews.MV_Name = ?', $view_name)
            ->where('ModuleViewsIndex.MVI_LanguageID = ?', $lang)
            ->where('PagesIndex.PI_LanguageID = ?', $lang)
            ->where('PagesIndex.PI_Status = 1');

            if($module <> 0)
                $select->where('MCVP_ModuleID = ?', $module);

            $row = $db->fetchRow($select);
            if(!$row)
                return '';
            if ($pageOnly)
                return $row['PI_PageIndex'];
            else
                return "{$row['PI_PageIndex']}/{$row['MVI_ActionName']}";
        }else{
            return '';
        }
    }

    public static function getRssItemsLimitPerCategory($categoryID){

        $db  = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Categories', array('C_RssItemsCount'))
            ->where('Categories.C_ID = ?', $categoryID);

        return $db->fetchOne($select);
    }
}