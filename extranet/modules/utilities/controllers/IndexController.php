<?php
    class Utilities_IndexController extends Cible_Extranet_Controller_Action{
        function googleAnalyticsAction(){
             $this->view->assign('username', $this->_config->googleAnalytics->username);
             $this->view->assign('password', $this->_config->googleAnalytics->password);
        }

        function dictionnaryExportAction(){
            $this->disableLayout();
            $this->disableView();

            $select = $this->_db->select();
            $select->from('Static_Texts')
                   ->order('ST_LangID');

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->setCellValue('A1', "Identifier");
            $objPHPExcel->getActiveSheet()->setCellValue('B1', "LangID");
            $objPHPExcel->getActiveSheet()->setCellValue('C1', "Value");
            $objPHPExcel->getActiveSheet()->setCellValue('D1', "Type");

            $items = $this->_db->fetchAll( $select );
            $item_count = count($items);

            for($i = 0; $i < $item_count; $i++){
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $i, $items[$i]['ST_Identifier']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $i, $items[$i]['ST_LangID']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $i,  $items[$i]['ST_Value']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $i, $items[$i]['ST_Type']);
            }

            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            header("Content-Disposition: attachment;filename=dictionnary.xlsx");

            // output the file
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
        }
    }