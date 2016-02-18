<?php
  class Zend_View_Helper_GetAllNewsletterModels{
        function  getAllNewsletterModels(){
            $modelsSelect = new NewsletterModelsIndex();
            $select =$modelsSelect->select();
            $select->where('NMI_LanguageID = ?', Zend_Registry::get("languageID"))
            ->order('NMI_Title');
            
            $modelsData = $modelsSelect->fetchAll($select);
            return $modelsData;
        }
  }