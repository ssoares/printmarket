<?php
    class Cible_View_Helper_getAllWebSiteLanguage extends Zend_View_Helper_Abstract
    {
        public function getAllWebSiteLanguage(){
            $languageSelect = new Languages();
            $select = $languageSelect->select();
            
            $languageData = $languageSelect->fetchAll($select);
            return $languageData;    
        }
    }