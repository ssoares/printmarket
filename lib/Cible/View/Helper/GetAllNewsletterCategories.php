<?php
  class Cible_View_Helper_GetAllNewsletterCategories{
        function  getAllNewsletterCategories(){
            $categoriesSelect = new Categories();
            $select = $categoriesSelect->select()->setIntegrityCheck(false);
            $select->from('Categories') 
            ->join('CategoriesIndex', ' CI_CategoryID = C_ID')
            ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
            ->where('C_ModuleID = 8')
            ->order('CI_Title');
            
            $categoriesData = $categoriesSelect->fetchAll($select);
            return $categoriesData;
        }
  }
?>  