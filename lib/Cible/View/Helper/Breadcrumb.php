<?php
    class Cible_View_Helper_Breadcrumb extends Zend_View_Helper_Abstract
    {
        public function breadcrumb(){
            
            $pageId = Zend_Registry::get('pageID');
            
            return Cible_FunctionsPages::buildClientBreadcrumb($pageId,1);
        }
    }