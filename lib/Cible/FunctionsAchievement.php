<?php
    abstract class Cible_FunctionsAchievement
    {
       public static function getAllCustomersPositions(){
            $positions  = Zend_Registry::get("db");
            $select     = $positions->select()
                                    ->from('AchievementCustomers')
                                    ->order('AC_Position');
            
            return $positions->fetchAll($select);
        }
        
        
        public static function fillCustomersSelectPosition($Form, $PositionsArray, $Action, $ElementPosition = 0){
            $TotalPos = count($PositionsArray);
            if ($TotalPos > 0){
                $Cpt=0;
                foreach ($PositionsArray as $Pos){
                    if($Cpt == 0){
                        $Form->AC_Position->addMultiOption($Pos["AC_Position"], 'Première position');
                        if ($Cpt == $TotalPos-1 && $Action == "add"){
                            $Form->AC_Position->addMultiOption($Pos["AC_Position"]+1, 'Dernière position');
                        }
                    }
                    elseif($Cpt == $TotalPos-1){
                        if($Action == "add"){
                            $Form->AC_Position->addMultiOption($Pos["AC_Position"], $Cpt+1 . "ième position");        
                            $Form->AC_Position->addMultiOption($Pos["AC_Position"]+1, 'Dernière position');
                        }
                        else{
                            $Form->AC_Position->addMultiOption($Pos["AC_Position"], 'Dernière position');    
                        }
                    }
                    else{
                        $Form->AC_Position->addMultiOption($Pos["AC_Position"], $Cpt+1 . "ième position");
                    }
                    $Cpt++;
                }
            }
            else{
               $Form->AC_Position->addMultiOption('1', 'Première position'); 
            }
            return $Form;
        } 
        
        
        public static function getAllElementsPositions($achievementID){
            $positions  = Zend_Registry::get("db");
            $select     = $positions->select()
                                    ->from('AchievementData_Elements')
                                    ->where('ADE_AchievementDataID = ?', $achievementID)
                                    ->where('ADE_LanguageID = ?', Zend_Registry::get("languageID"))
                                    ->order('ADE_Position');
            
            return $positions->fetchAll($select);
        }
        
        public static function fillSelectPosition($Form, $PositionsArray, $Action, $ElementPosition = 0){
            $TotalPos = count($PositionsArray);
            if ($TotalPos > 0){
                $Cpt=0;
                foreach ($PositionsArray as $Pos){
                    if($Cpt == 0){
                        $Form->ADE_Position->addMultiOption($Pos["ADE_Position"], 'Première position');
                        if ($Cpt == $TotalPos-1 && $Action == "add"){
                            $Form->ADE_Position->addMultiOption($Pos["ADE_Position"]+1, 'Dernière position');
                        }
                    }
                    elseif($Cpt == $TotalPos-1){
                        if($Action == "add"){
                            //$Form->ADE_Position->addMultiOption($Pos["ADE_Position"], "En dessous de < ".$PositionsArray[$Cpt-1]["ADE_ElementID"]." >");
                            $Form->ADE_Position->addMultiOption($Pos["ADE_Position"], $Cpt+1 . "ième position");        
                            $Form->ADE_Position->addMultiOption($Pos["ADE_Position"]+1, 'Dernière position');
                        }
                        else{
                            $Form->ADE_Position->addMultiOption($Pos["ADE_Position"], 'Dernière position');    
                        }
                    }
                    else{
                        //$Form->ADE_Position->addMultiOption($Pos["ADE_Position"], "En dessous de < ".$PositionsArray[$Cpt-1]["ADE_ElementID"]." >");    
                        $Form->ADE_Position->addMultiOption($Pos["ADE_Position"], $Cpt+1 . "ième position");
                    }
                    $Cpt++;
                }
            }
            else{
               $Form->ADE_Position->addMultiOption('1', 'Première position'); 
            }
            return $Form;
        }
        
        
        public static function deleteAllAchievements($customerID)
        {
            $achievements = new AchievementData();
            $select = $achievements->select()
            ->where('AD_CustomerID = ?', $customerID);
            
            $achievementsDetails = $achievements->fetchAll($select);
            foreach ($achievementsDetails as $achievement){
                Cible_FunctionsAchievement::deleteAchievement($achievement['AD_ID']);
            }
        }
        
        public static function deleteAchievement($achievementID)
        {
            $achievementElements = new AchievementDataElements();
            $select = $achievementElements->select()
            ->where('ADE_AchievementDataID = ?', $achievementID);
            
            $achievementElementsDetails = $achievementElements->fetchRow($select);
            
            if (count($achievementElementsDetails) > 0)
                Cible_FunctionsAchievement::deleteAchievementElement($achievementID, $achievementElementsDetails['ADE_ElementID']);
            
            
            // delete achievement data
            $achievement = new AchievementData();
            $where = "AD_ID = $achievementID";
            $achievement->delete($where);
        }
        
        public static function deleteAchievementElement($achievementID, $elementID)
        {
            $elementDetails = new AchievementDataElements();
            $select = $elementDetails->select()
            ->where('ADE_AchievementDataID = ?', $achievementID)
            ->where('ADE_ElementID = ?', $elementID);
            
            $element = $elementDetails->fetchRow($select);
            $elementPosition = $element['ADE_Position'];
            
            
            // delete element data
            if ($element['ADE_ElementType'] == 'image'){
                // delete image data
                $imageElement = new AchievementDataElementsImages();
                $where = "ADEI_ID = $elementID";
                $imageElement->delete($where);
            }
            elseif($element['ADE_ElementType'] == 'texte'){
                // delete text data
                $textElement = new AchievementDataElementsTexts();
                $where = "ADET_ID = $elementID";
                $textElement->delete($where);
            }
            
            //delete element
            $where = "ADE_ElementID = $elementID";
            $elementDetails->delete($where);
            
            // update order
            $db = Zend_Registry::get("db"); 
            $where = "(ADE_Position > ".$elementPosition.") AND ADE_AchievementDataID = ".$achievementID;
            $db->update('AchievementData_Elements', array('ADE_Position'=> new Zend_Db_Expr('ADE_Position - 1')), $where); 
            
        }      
    }  
?>
