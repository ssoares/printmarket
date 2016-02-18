<?php
  class Cible_View_Helper_GetAllSalutation{
        function  getAllSalutation(){
            $salutationsSelect = new Salutations();
            $select = $salutationsSelect->select()->setIntegrityCheck(false);
            $select->from('Salutations')
            ->join('Static_Texts', 'ST_Identifier = S_StaticTitle')
            ->where('ST_LangID = ?', Zend_Registry::get("languageID"))
            ->order('ST_Value');
            
            $salutationsData = $salutationsSelect->fetchAll($select);
            return $salutationsData;
        }
  }
?>
