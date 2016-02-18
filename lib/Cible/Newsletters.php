<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2012 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Manage newsletters data.
 *
 * @category
 * @package
 * @copyright Copyright (c)2012 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterObject.php 1473 2014-02-19 22:27:09Z ssoares $
 */
class Cible_Newsletters extends DataObject implements Cible_Newsletters_Interface
{
    /**
     * Base Url of the service API. The default service is Le externalTool API.
     */
    const API_URL = 'https://api.wbsrvc.com';

    protected $_component = '';
    protected $_url = '';
    protected $_groupsActions = array();

    /**
     * String to identify a user (= admin) in the externalTool system.
     * @var type string
     */
    protected $_userKey = "";
    /**
     * String requird to call the API (externalTool).
     * @var type string
     */
    protected $_apiKey = "";
    protected $_loginUserInfo = array();
    protected $_dataClass = 'NewsletterReleases';
    protected $_id;
    protected $_config = null;
    protected $_data = array();
    protected $_action = '';
    protected $_results = array();
    protected $_langId = 0;
    protected $_defaultList = 0;
    protected $_status = '';
    protected $_validStatus = array('active', 'subscribed');
    protected $_updated = false;

    public function getStatus()
    {
        return $this->_status;
    }

    public function getValidStatus()
    {
        return $this->_validStatus;
    }

    public function getUpdated()
    {
        return $this->_updated;
    }

    public function getDefaultList()
    {
        return $this->_defaultList;
    }

    public function setDefaultList($defaultList = null)
    {
        if (!is_null($defaultList)){
            $this->_defaultList = $defaultList;
        }else{
            $cfg = $this->_config->toArray();
            $this->_defaultList = $cfg['externalTool']['listOne'][$this->_langId];
        }

        return $this;
    }


    public function getLangId()
    {
        return $this->_langId;
    }

    public function setLangId($langId = null)
    {
        if (!is_null($langId)){
            $this->_langId = $langId;
        }elseif (empty($this->_langId) && !Zend_Registry::isRegistered('languageID')){
            $this->_langId = Cible_Controller_Action::getDefaultEditLanguage();
        }elseif (empty($this->_langId)){
            $this->_langId = Zend_Registry::get('languageID');
        }

        return $this;
    }

    public function getData(){
        return $this->_data;
    }

    public function getAction(){
        return $this->_action;
    }

    public function setData($data = array(), $replace = false){
        if (!empty($this->_data) && !$replace){
            $this->_data = array_merge($this->_data, $data);
        }else{
            $this->_data = $data;
        }
        return $this;
    }

    public function setAction($action){
        $this->_action = $action;
        return $this;
    }

    public function getResults($field = '')
    {
        if (!empty($field)){
            $results = $this->_results[$field];
        }else{
            $results = $this->_results;
        }

        return $results;
    }

    protected function setUrl($url = '')
    {
        $tmp = array(self::API_URL,
            $this->_component);
        $tmp[] = !empty($url)?$url : $this->_action;
        $this->_url = implode('/', $tmp) ;
        return $this;
    }

    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function setLoginUserInfo($loginUserInfo = array())
    {
        if (empty($loginUserInfo))
            $this->_loginUserInfo = $this->_config->externalTool->loginUserInfos->toArray();
        else
            $this->_loginUserInfo = $loginUserInfo;

        return $this;
    }

    public function setUserKey($userKey = '')
    {
        if (empty($userKey))
            $this->_userKey = $this->_config->externalTool->userKey;
        else
            $this->_userKey = $userKey;

        return $this;
    }

    public function setApiKey($apiKey = '')
    {
        if (empty($apiKey)){
            $this->_apiKey = $this->_config->externalTool->apiKey;
        }else{
            $this->_apiKey = $apiKey;
        }

        return $this;
    }

    public function setParameters()
    {
        if (empty($this->_langId)){
            $this->setLangId();
        }
        $this->setApiKey()->setUserKey()->setDefaultList();

        return $this;
    }

    public function addListId()
    {
        if (!isset($this->_data['list_id'])){
            $this->setData(array('list_id' => $this->_defaultList));
        }
    }

    /**
     * Retrieve the model path to render the current release
     * @return array
     */
    public function getModel($modelId)
    {
        //echo $modelId;
        //exit;
        $data = array();
        $oModels = new NewsletterModelsObject();

        $lang = Zend_Registry::get('languageID');
        if (empty($modelId))
            $model = $oModels->getDefault();
        else
            $model = $oModels->getAll($lang, true, $modelId);

        $data = explode('/', $model[0]['NM_Directory']);
        array_pop($data);
        $data = implode('/', $data);

        return $data;
    }


    public function process()
    {
        $data = array(
            'user_key' => $this->_userKey,
        );
        if (!empty($this->_data)){
            $data = array_merge($data, $this->_data);
        }
        $this->setUrl();
        $this->_curlCall($data);

        return $this;
    }

    protected function _curlCall($params)
    {
        $this->_results = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('apikey: ' . $this->_apiKey));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $jsonResult = curl_exec($ch);
        curl_close($ch);

        if ($jsonResult === false) {
            unset($jsonResult);
            echo 'Curl error: ' . curl_error($ch);
        }

        if (isset($jsonResult)){
            $this->_results = json_decode($jsonResult, true);
        }

        return $this->_results;

    }
}