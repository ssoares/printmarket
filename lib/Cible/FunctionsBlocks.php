<?php
abstract class Cible_FunctionsBlocks
{
    public static function getBlockDetails($blockID){
        $block = new BlocksIndex();
        $select = $block->select()
                        ->setIntegrityCheck(false)
                        ->from('BlocksIndex')
                        ->join('Blocks','Blocks.B_ID = BlocksIndex.BI_BlockID')
                        ->where('BlocksIndex.BI_LanguageID = ?', Zend_Registry::get("languageID"))
                        ->where('BlocksIndex.BI_BlockID= ?', $blockID);

        return $block->fetchRow($select);
    }

    public static function getBlocksFromRelatedPage($options)
    {
        $oBlocks = new BlocksObject();
        $oBlocks->setProperties($options);
        $blocks = $oBlocks->getBlocksFromRelatedPage();

        return $blocks;
    }

    public static function getBlockDetailsByLangID($blockID, $langID){
        $blockIndex = new BlocksIndex();
        $select = $blockIndex->select()
                        ->setIntegrityCheck(false)
                        ->from('BlocksIndex')
                        ->join('Blocks','Blocks.B_ID = BlocksIndex.BI_BlockID')
                        ->where('BlocksIndex.BI_LanguageID = ?', $langID)
                        ->where('BlocksIndex.BI_BlockID= ?', $blockID);

        $block = $blockIndex->fetchRow($select);

        if( empty($block) ){

            $newrow = $blockIndex->createRow( array(
                'BI_BlockID' => $blockID,
                'BI_LanguageID' => $langID
            ));

            $newrow->save();

            $block = $blockIndex->fetchRow($select);
        }

        return $block;
    }

    public static function getBlockParameters($blockID){
        $blockParameters = new Parameters();
        $select = $blockParameters->select()
        ->where('P_BlockID = ?', $blockID)
        ->order('P_Number');
        return $blockParameters->fetchAll($select);
    }

    public static function getBlockParameter($blockID, $paramNumber){
        $blockData = "";
        if ($blockID > 0){
            $db = Zend_Registry::get("db");
            $select = $db->select()
                ->from('Parameters', array('P_Value'))
                ->where('P_BlockID = ?', $blockID)
                ->where('P_Number = ?', $paramNumber);

              $blockData = $db->fetchOne($select);
        }

        return $blockData;
    }

    public static function getAllPositions($pageID, $zoneID = 1){
        $positions  = Zend_Registry::get("db");
        $select     = $positions->select()
                                ->from('Blocks')
                                ->join('BlocksIndex','BlocksIndex.BI_BlockID = Blocks.B_ID')
                                ->where('BI_LanguageID = ?', Zend_Registry::get("languageID"))
                                ->where('B_PageID = ?', (int)$pageID)
                                ->where('B_ZoneID = ?', (int)$zoneID)
                                ->order('B_Position');

        return $positions->fetchAll($select);
    }

    public static function fillSelectPosition($Form, $PositionsArray, $Action, $blockPosition = 0){

        $TotalPos = count($PositionsArray);

        if($Action == 'update')
        {

             if ($TotalPos > 0){

                $i=0;
                foreach ($PositionsArray as $Pos){
                    if($i == 0){
                        $Form->B_Position->addMultiOption('-1', 'Première position');
                    } else {
                        $Form->B_Position->addMultiOption($Pos["B_ID"], str_replace('%TEXT%',$PositionsArray[$i-1]["BI_BlockTitle"],Cible_Translation::getCibleText("form_select_option_position_below")));
                    }
                    $i++;
                }

             } else {

               $Form->B_Position->addMultiOption('-1', 'Première position');

             }


        } else {

            if ($TotalPos > 0){
                $Cpt=0;
                foreach ($PositionsArray as $Pos){
                    if($Cpt == 0){
                        $Form->B_Position->addMultiOption($Pos["B_Position"], 'Première position');
                        if ($Cpt == $TotalPos-1 && $Action == "add"){
                            $Form->B_Position->addMultiOption($Pos["B_Position"]+1, 'Dernière position');
                        }
                    }
                    elseif($Cpt == $TotalPos-1){
                        if($Action == "add"){
                            $Form->B_Position->addMultiOption($Pos["B_Position"], str_replace('%TEXT%',$PositionsArray[$Cpt-1]["BI_BlockTitle"],Cible_Translation::getCibleText("form_select_option_position_below")));
                            $Form->B_Position->addMultiOption($Pos["B_Position"]+1, 'Dernière position');
                        }
                        else{
                            $Form->B_Position->addMultiOption($Pos["B_Position"], 'Dernière position');
                        }
                    }
                    else{
                        $Form->B_Position->addMultiOption($Pos["B_Position"], str_replace('%TEXT%',$PositionsArray[$Cpt-1]["BI_BlockTitle"],Cible_Translation::getCibleText("form_select_option_position_below")));
                    }
                    $Cpt++;
                }
            }
            else{
               $Form->B_Position->addMultiOption('1', 'Première position');
            }

        }


        return $Form;
    }

    public static function fillSelectZone($form, $numZone){

        for($i = 1; $i <= $numZone; $i++)
        {
            $form->B_ZoneID->addMultiOption($i, "Zone {$i}");
        }

        return $form;
    }

    public static function getBlocks()
    {
        $db = Zend_Registry::get("db");
        $select = $db->select()
            ->from('Blocks')
            ->join('Modules', 'Modules.M_ID = Blocks.B_ModuleID')
            ->join('Parameters','Parameters.P_BlockID = Blocks.B_ID', array('B_Action'=>'P_Value'))
            ->where('Parameters.P_Number  = ?', 999)
            ->where('Blocks.B_PageID = ?', Zend_Registry::get("pageID"))
            ->where('Blocks.B_Online = ?', 1)
            ->order('Blocks.B_Position ASC');

        $rows = $db->fetchAll($select);

        return $rows;
    }

    public static function getDuplicateData($blockId, $langId)
    {
        $oBlock = new BlocksObject();
        $duplucateData = (array)$oBlock->getDuplicateData($blockId, $langId);

        return $duplucateData;
    }
}
