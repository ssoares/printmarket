<?php
/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of SwitchDB
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Controller_Action_Helper_WriteFile extends Zend_Controller_Action_Helper_Abstract
{
    protected $_extension = '.fpd';
    protected $_data = array();
    protected $_separator = '';
    protected $_message = '';
    protected $_rootFilesPath = '';
    protected $_exportPath = '';
    protected $_addSubFolder = false;
    protected $_moduleTitle = '';
    protected $_mode = 'w';
    protected $_destination = 'local';
    protected $_folder = 'export';
    protected $_config = 'export';
    protected $_encoding = '';

    public function getEncoding(){
        return $this->_encoding;
    }

    public function setEncoding($encoding){
        $this->_encoding = $encoding;
        return $this;
    }

    public function setConfig($config){
        $this->_config = $config;
        return $this;
    }

    public function getFolder(){
        return $this->_folder;
    }

    public function setFolder($folder){
        $this->_folder = $folder;
        return $this;
    }

    public function setDestination($destination){
        $this->_destination = $destination;
        return $this;
    }

    public function getExtension(){
        return $this->_extension;
    }
    public function getData(){
        return $this->_data;
    }
    public function getSeparator(){
        return $this->_separator;
    }
    public function getMessage(){
        return $this->_message;
    }
    public function getRootFilesPath(){
        return $this->_rootFilesPath;
    }
    public function getExportPath(){
        return $this->_exportPath;
    }
    public function getAddSubFolder(){
        return $this->_addSubFolder;
    }
    public function getModuleTitle(){
        return $this->_moduleTitle;
    }
    public function setExtension($extension){
        if (!strpos('.', $extension)){
            $extension = '.' . $extension;
        }
        $this->_extension = $extension;
        return $this;
    }
    public function setData($data){
        $this->_data = $data;
        return $this;
    }
    public function setSeparator($separator){
        $this->_separator = $separator;
        return $this;
    }
    public function setMessage($message){
        $this->_message = $message;
        return $this;
    }
    public function setExportPath($exportPath = ''){
        if (empty($exportPath)){
            $exportPath = '';
//            if (ISCRONJOB){
//                $exportPath = Zend_Registry::get('serverDocumentRoot');
//            }
            $exportPath .= $this->_rootFilesPath;
            if ($this->_addSubFolder
                && !preg_match('/'.$this->_moduleTitle.'/', $exportPath)){
                $exportPath .= $this->_moduleTitle . '/';
            }
            $exportPath .= $this->_folder . "/";
        }
        $this->_exportPath = $exportPath;
        if(!is_dir($this->_exportPath)){
            mkdir($this->_exportPath, 0755, true);
        }
        return $this;
    }
    public function setRootFilesPath($rootFilesPath){
        $this->_rootFilesPath = $rootFilesPath;
        return $this;
    }
    public function setAddSubFolder($addSubFolder){
        $this->_addSubFolder = $addSubFolder;
        return $this;
    }

    public function setModuleTitle($moduleTitle){
        $this->_moduleTitle = $moduleTitle;
        return $this;
    }
    public function setMode($mode){
        $this->_mode = $mode;
        return $this;
    }
    public function setProperties($properties){
        $methods = get_class_methods(get_class());
        foreach($properties as $key => $value)
        {
            $tmp = 'set' . ucfirst(str_replace('_', '', $key));
            if (in_array($tmp, $methods)){
                $this->$tmp($value);
            }
        }
        return $this;
    }

    public function direct($data, $name = '')
    {
        try
        {
            if(empty($name)){
                $name = $this->_moduleTitle;
            }
            $lines = '';
            if (empty($this->_exportPath)){ $this->setExportPath();}

            foreach($data as $id => $dataFile)
            {
                $nbLines = 0;
                if (is_array(current($dataFile))){
                    foreach($dataFile as $values){
                        $lines .= implode($this->_separator, $values) . "\r\n";
                        ++$nbLines;
                    }
                }else{
                    $lines .= implode($this->_separator, $dataFile) . "\r\n";
                    $nbLines = 1;
                }

                $fileName = $name . '_' . $id . $this->_extension;
                $this->output($fileName, $lines);
            }
            if (empty($fileName)){
                $fileName = $name . '_0' . $this->_extension;
            }

            return $this->_exportPath . $fileName;
        }
        catch(Exception $exc)
        {
            return $exc->getMessage();
        }
    }

    public function fdp($data, $name = '')
    {
        try
        {
            if(empty($name)){
                $name = $this->_moduleTitle;
            }
            $lines = '';
            if (empty($this->_exportPath)){ $this->setExportPath();}
            foreach($data as $id => $dataFile)
            {
                $nbLines = 0;
                foreach($dataFile as $values){
                    $lines .= $values . "\r\n";
                    ++$nbLines;
                }

                $fileName = $name . '_' . $id . $this->_extension;
                $this->output($fileName, $lines);
            }

            return $this->_exportPath . $fileName;
        }
        catch(Exception $exc)
        {
            return $exc->getMessage();
        }
    }

    public function output($filename, $lines)
    {
        if (!empty($this->_encoding)){
            $lines = mb_convert_encoding($lines, strtoupper($this->_encoding),
                mb_detect_encoding($lines, "UTF-8, ISO-8859-1, ISO-8859-15", true));
        }
        switch($this->_destination)
        {
            case 'receipts':
                $this->setExportPath();
                include_once 'tcpdf.php';
                $pdf = new TCPDF('P', 'mm', 'LETTER');
                $pdf->SetCellPadding(5);
                $pdf->SetFontSize(12);
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $sslFolder = 'file://../../ssl/';
                $crt = $this->_config->info->crt . '.crt';
                $signing_cert = $sslFolder . $crt;
                $private_key = $sslFolder . 'donna.key';
//                $signing_cert = 'file://../../localtmp/testSSL.crt';
                $pdf->setSignature($signing_cert, $private_key,
                    '', '', 1, array(), '');
                $pdf->AddPage();
                $pdf->writeHTML($lines);
                // output the file
                $pdf->Output($this->_exportPath . $filename, 'F');
                break;
            case 'download':
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment;filename={$filename}");
                echo $lines;
                break;

            default:
                $file = $this->_exportPath . $filename;
                $fh = fopen($file, $this->_mode);
                if(!$fh){
                    $string = 'Cannot open ' . $file;
                }elseif(!fwrite($fh, $lines)){
                    $string = "Error while writing data";
                }
                fclose($fh);
                break;
        }
    }

    public function deleteFile($filename)
    {
        if (empty($this->_exportPath)){ $this->setExportPath();}
        unlink($this->_exportPath . $filename);
    }

}