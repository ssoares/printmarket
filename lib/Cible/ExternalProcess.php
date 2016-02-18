<?php
/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

require_once 'Zend/File/Transfer/Exception.php';

/**
 * Transfert files and call url to process data on remote server.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_ExternalProcess
{
    protected $_config;
    protected $_file;
    protected $_status = false;
    protected $_response = false;
    protected $_process = "";
    protected $_log = "";
    protected $_module = "";
    protected $_action = "";
    protected $_mode = "a";
    protected $_remoteFile = "";
    protected $_keys = array();
    protected $_passwordKey = "password";
    protected $_fileKey = "filename";
    protected $_actionKey = "qaction";
    protected $_timeout = 600;
    protected $_errors = array();
    protected $ch;
    protected $_object;
    protected $_downloadFolder;
    protected $_fingerPrint = '';
    protected $_timestamp = null;
    protected $_redirectUrl = '';
    protected $_supportRequired = false;
    protected $_key = '';
    protected $_processList = array(12 => 'eaccept', 13 => 'gps');
    protected $_messages = array();

    public function getMessages($key = 0, $replace = array()){
        if ($key > 0){
            $msg = $this->_messages[$key];
            foreach($replace as $key => $value){
                $msg = str_replace($key, $value, $msg);
            }
        }else{
            $msg = $this->_messages;
        }

        return $msg;
    }

    public function setMessages(array $messages){
        $this->_messages = $messages;
        return $this;
    }

    public function initProcess($key)
    {
        $this->setProcess($this->_processList[$key]);
        return $this;
    }
    public function getSupportRequired(){
        return $this->_supportRequired;
    }

    public function setSupportRequired($supportRequired)
    {
        $this->_supportRequired = (bool)$supportRequired;
        return $this;
    }

    public function getRedirectUrl(){
        return $this->_redirectUrl;
    }
    public function setRedirectUrl($redirectUrl){
        $this->_redirectUrl = $redirectUrl;
        return $this;
    }
    public function getTimestamp(){
        return $this->_timestamp;
    }
    public function getFingerPrint(){
        return $this->_fingerPrint;
    }
    public function setTimestamp()
    {
        $this->_timestamp = (int) floor(microtime(true) * 1000);
        return $this;
    }
    public function setFingerPrint($fingerPrint = null)
    {
        if(empty($this->_data)){
            $priority = Zend_Log::EMERG;
            $string = '31001 : No data loaded. Cannot define a new fingerprint';
            $this->_log->log($string, $priority);
            throw new Cible_ExternalProcess_Exception($string, 31001);
        }
        if(is_null($fingerPrint)){
            $data = implode('^', $this->_data);
            $decodedKey = base64_decode($this->_key, true);
            $this->_fingerPrint = strtoupper(hash_hmac("sha256", $data,
                    $decodedKey));
        }else{
            $this->_fingerPrint = $fingerPrint;
        }
        return $this;
    }

    public function getObject()
    {
        return $this->_object;
    }

    public function setObject($object)
    {
        if (is_string($object)){
            $name = ucfirst($this->_module) . 'Object';
            $this->_object = new $name();
        }else{
            $this->_object = $object;
        }
        return $this;
    }

    public function getCh()
    {
        return $this->ch;
    }

    public function initCh($url = '')
    {
        if (empty($url)){
            $url = $this->_urlPage;
        }
        if (empty($url)){
            throw new Cible_ExternalProcess_Exception('No Url as default option.');
        }
        $this->ch = curl_init($url);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->_timeout);
        if (ISDEV){
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        }
        if ($this->_user && $this->_pwd){
            curl_setopt($this->ch, CURLOPT_USERPWD, $this->_user. ':' .$this->_pwd);
        }
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getMode()
    {
        return $this->_mode;
    }

    public function getLog()
    {
        return $this->_log;
    }

    public function getConfig()
    {
        return $this->_config;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getResponse()
    {
        $response = is_numeric($this->_response) ?
            (int)$this->_response : $this->_response;

        return $response;
    }

    public function getProcess()
    {
        return $this->_process;
    }

    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    public function setResponse($response)
    {
        $this->_response = $response;
        return $this;
    }

    public function setProcess($process = "")
    {
        if (!empty($process)){
            $this->_process = $process;
        }elseif(in_array('getName', get_class_methods($this->_object))){
            $this->_process = $this->_action . ucfirst($this->_object->getName());
        }else{
            $this->_process = $this->_action . ucfirst($this->_module);
        }
        return $this;
    }

    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    public function setMode($mode)
    {
        $this->_mode = $mode;
        return $this;
    }

    public function setLog($log = null)
    {
        if (!is_null($log)){
            $this->_log = $log;
        }else{
            $orderLogPath = Zend_Registry::get('logPath'). "/";
            !empty($this->_module)?$orderLogPath . $this->_module . '/' : '';
            // Log file name
            $suffix      = date('Ym');
            $logFileName = 'log_' . $suffix . '.txt';
            $file     = $orderLogPath . $logFileName;
            $stream = new Zend_Log_Writer_Stream($file, $this->_mode);
            $this->_log = new Zend_Log($stream);
        }
        return $this;
    }

    public function setRemoteFolder($remoteFolder = '')
    {
        if (!$this->_module){
            throw new Zend_File_Transfer_Exception('Module is not set');
        }
        $root[] = $this->_config->services->root;
        if (in_array('getFolder', get_class_methods($this->_object))){
            $folder = $this->_object->getFolder();
            $root[] = $folder . '/';
        }else{
            $root[] = $this->_module . '/';
        }
        $this->_remoteFile = implode('/', $root);

        return $this;
    }
    public function setRemoteFile($remoteFile = '')
    {
        if (!$this->_module || !$this->_file){
            throw new Zend_File_Transfer_Exception('Module and/or file not set');
        }
        $root[] = $this->_config->services->root;
        if (in_array('getFilename', get_class_methods($this->_object))){
            $folder = $this->_object->getFileName();
            $root[] = $folder . '/';
        }else{
            $root[] = $this->_module . '/';
        }
        $this->_remoteFile = implode('/', $root) . basename($this->_file);

        return $this;
    }

    public function setDownloadFolder($folder = '')
    {
        $this->_downloadFolder = $folder;
        return $this;
    }

    public function getDownloadFolder()
    {
        return $this->_downloadFolder;
    }

    public function __construct($config, $key = null)
    {
        if (!$config){
            throw new Zend_File_Transfer_Exception('The config data are required');
        }
        $this->_config = $config;
        if (!is_null($key)){
            $this->initProcess($key);
            $objectName = 'Cible_ExternalProcess_' . ucfirst($this->_process);
            $this->_object = new $objectName($this->_config);
        }
    }

    public function sanitizeKey($key)
    {
        return strtr($key, '-_,', '+/=');;
    }

    public function transferFile()
    {
        $ftpParameters = $this->_config->ftpTransfer;
        // set up basic connection
        $connId = ftp_connect($ftpParameters->host, $ftpParameters->port);
        // login with username and password
        if (ftp_login($connId, $ftpParameters->user, $ftpParameters->password))
        {
            $priority = Zend_Log::INFO;
            $string = 'Ftp connection status: succeed';
            $this->_log->log($string, $priority);
            $fp = fopen($this->_file, 'r');
            $this->setRemoteFile();
            if (!ftp_chdir($connId, dirname($this->_remoteFile))){
                ftp_mkdir($connId, dirname($this->_remoteFile));
            }
            // try to upload $file
            if (ftp_fput($connId, $this->_remoteFile, $fp, FTP_BINARY)){
                $this->_status = true;
                $string = 'Ftp transfer status: succeed';
                $this->_log->log($string, $priority);
            }
        }else{
            $priority = Zend_Log::WARN;
            $string = 'Ftp connection status: failed';
            $this->_log->log($string, $priority);
        }

        ftp_close($connId);

        return $this;
    }

    public function run()
    {
        if (!$this->_object && !empty($this->_module)){
            $this->setObject($this->_module);
        }else{
            $this->_object = $this;
        }
        if (!$this->_process){
            $this->setProcess();
        }
        $this->initCh();
        switch($this->_process)
        {
            case 'upload':
                $this->setUploadOpt();
                $this->_process();
                break;

            default:
                $this->_process();
                break;
        }

        return $this;
    }

    private function downloadFiles()
    {
        $nbFiles = $nbSuccess = $nbFailed = 0;
        $ftpParameters = $this->_config->ftpTransfer;
        // set up basic connection
        $connId = ftp_connect($ftpParameters->host, $ftpParameters->port);
        // login with username and password
        if (ftp_login($connId, $ftpParameters->user, $ftpParameters->password))
        {
            $priority = Zend_Log::INFO;
            $start = microtime(true);
            $string = 'Process ' . $this->_process;
            $string .= ' : start time ' . date('Y-m-d H:i:s') . "\r\n";
            $string .= 'Ftp connection status: succeed';
            $this->_log->log($string, $priority);
            $this->setRemoteFolder();
            if (!ftp_chdir($connId, $this->_remoteFile)){
                $priority = Zend_Log::ERR;
                $string = 'The folder does not exist' . $this->_remoteFile;
                $this->_log->log($string, $priority);
            }else{
                $contents = ftp_nlist($connId, ".");
                foreach($contents as $file)
                {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $local = $this->_downloadFolder . basename($file);
                    if (ftp_get($connId, $local, $file, FTP_BINARY)){
                        $nbSuccess++;
                        ftp_delete($connId, $file);
                    }else{
                        $nbFailed++;
                    }
                    $nbFiles++;
                }
                $time = microtime(true) - $start;
                $string .= $nbFiles . ' to download for update. ' . PHP_EOL;
                $string .= $nbSuccess . ' downloaded and ';
                $string .= $nbFailed . ' failed. ' . PHP_EOL;
                $string .= 'Completed in ' . number_format($time * 1000, 2) . 'ms';
                $this->_log->log($string, $priority);
            }
        }else{
            $priority = Zend_Log::WARN;
            $string = 'Ftp connection status: failed';
            $this->_log->log($string, $priority);
        }

        ftp_close($connId);

        $this->_response = $nbFiles;

        return $this;
    }

    private function _process()
    {
        if ($this->_status){
            $this->_errors = $this->_object->getCodesErrors();
            $priority = Zend_Log::INFO;
            $start = microtime(true);
            $string = 'Process ' . $this->_process . ' call to ' . $this->_urlPage;
            $this->_log->log($string, $priority);
            $this->_response = curl_exec($this->ch);
            $info = curl_getinfo($this->ch);
            $time = microtime(true) - $start;
            if(floor($info['http_code'] / 100) >= 4) {
                $infoStr = json_encode($info);
                $this->_log->log('Completed in ' . number_format($time * 1000, 2) . 'ms', Zend_Log::ERR);
                $this->_log->log('Got response: ' . $this->_response, Zend_Log::ERR);
                $this->_log->log('Info : ' . $infoStr, Zend_Log::ERR);
//                throw new Cible_ExternalProcess_Exception ($infoStr);
            }else{
                $message = trim($this->_response);
                if ($this->_response < 0){ //  || !is_numeric($this->_response)
                    $priority = Zend_Log::ERR;
                    $message .= ' : ' . $this->_errors[(int)$this->_response];
                }
                $this->_log->log('Got response: ' . $message, $priority);
                $this->_log->log('Completed in ' . number_format($time * 1000, 2) . 'ms', $priority);
            }
        }
    }

    public function __destruct()
    {
        if ($this->ch){
            curl_close($this->ch);
        }
    }
}
