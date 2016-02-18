<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Manage log .
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: Log.php 1788 2015-06-18 20:07:52Z ssoares $
 */
class Cible_Log
{

    const DURATION = 90;

    protected $_oLog;
    protected $_logged = false;
    protected $_user = array();
    protected $_userId = 0;
    protected $_userEmail = '';
    protected $_history = array();
    protected $_data = array();
    protected $_moduleId = 0;
    protected $_action = '';
    protected $_event = 'allLog';
    protected $_typeOutput = '';
    protected $_recipient = '';
    protected $_module = '';
    protected $_logIfExists = false;

    public function setEvent($event)
    {
        $this->_event = $event;
        return $this;
    }

    public function setTypeOutput($typeOutput)
    {
        $this->_typeOutput = $typeOutput;
        return $this;
    }

    public function setRecipient($recipient)
    {
        $this->_recipient = $recipient;
        return $this;
    }

    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

    public function setUser($user = null)
    {
        if (!is_null($user)){
            $this->_user = $user;
        }elseif (Zend_Auth::getInstance()->hasIdentity()
            && preg_match('/extranet/', FRONTEND)){
            $identity = Zend_Auth::getInstance()->getIdentity();
            $user['member_id'] = $identity->EU_ID;
            $user['email'] = $identity->EU_Email;
        }elseif (is_null($user) && Zend_Registry::isRegistered('user')){
            $user = Zend_Registry::get('user');
        }

        $this->_user = $user;
        return $this;
    }

    public function setLogged($logged = false)
    {
        if (!$logged && !empty($this->_user))
            $logged = true;

        $this->_logged = $logged;
        return $this;
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    public function setUserEmail($userEmail)
    {
        $this->_userEmail = $userEmail;
        return $this;
    }

    public function setHistory($history)
    {
        if (isset($_COOKIE[$history]))
            $this->_history = json_decode($_COOKIE[$history], true);
        return $this;
    }

    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    public function setModuleId($moduleId)
    {
        $this->_moduleId = $moduleId;
        return $this;
    }
    public function setLogIfExists($logIfExists)
    {
        $this->_logIfExists = $logIfExists;
        return $this;
    }


    public function __construct($options = array())
    {
        $this->_oLog = new LogObject();
        $this->setProperties($options);
        if (empty($this->_user)){
            $this->setUser();
        }
        if (!empty($this->_user))
        {
            $this->setLogged();
            $this->setUserId($this->_user['member_id']);
            if (!empty($this->_user['email']))
                $this->setUserEmail($this->_user['email']);
        }
    }

    /**
     * Set the properties of he class.
     *
     * @param array $otpions Properties to build the notification.
     *
     * @return void
     */
    public function setProperties($options = array())
    {
        foreach ($options as $property => $value)
        {
            $methodName = 'set' . ucfirst($property);

            if (property_exists($this, '_' . $property)
                && method_exists($this, $methodName))
            {
                $this->$methodName($value);
            }
        }
    }

    public function log(array $options = array())
    {
        if (isset($options['data']) && empty($this->_data)){
            $this->setData($options['data']);
        }
        if (!empty($this->_data['L_Data'])){
            $this->_cleanEmptyFields($this->_data['L_Data']);
        }
        if (empty($this->_data['L_ModuleId']) && $this->_moduleId > 0){
            $this->_data['L_ModuleID'] = $this->_moduleId;
        }
        $this->setHistory('history');
        // User is connected ?
        if ($this->_logged)
            $this->_processLoggedIn();
        else
            $this->_processLoggedOut();

        // create / update Cookie
        if (SESSIONNAME == 'application'){
            $this->manageCookieHistory('history');
        }
        if (!empty($this->_data['L_Data']))
        {
            if ($this->_logIfExists)
                $exists = false;
            else{
                $this->_oLog->setModuleId($this->_moduleId);
                $exists = $this->_oLog->findRecords($this->_data);
            }
        // log data
        if (!$exists)
            $this->_oLog->writeData($this->_data);
        }
    }

    public function manageCookieHistory($name)
    {
        $path = Zend_Registry::get('web_root') . '/';
        $duration = time() + (60 * 60 * 24 * self::DURATION);
        $cookie = array('dateCreate' => date('m/d/Y h:i:s', time()));

        if (empty($this->_history) || empty($this->_userEmail))
            $this->_userId = session_id();
        elseif (count($this->_user))
        {
            $this->_userId = $this->_user['member_id'];
            $cookie['email'] = $this->_userEmail;
        }

        $cookie['id'] = $this->_userId;

        setcookie($name, json_encode($cookie), $duration, $path);
    }

    /**
     * @todo Set the values to activate the pop-up and activate it.
     *
     */
    public function askForLoggin()
    {
        $session = new Zend_Session_Namespace();
        $okLogin = isset($session->askForConnect)? $session->askForConnect: false;

        if ($okLogin)
        {
//            $profile = new MemberProfilesObject();
//            $account = $profile->findData(array(
//                'email' => $this->_userEmail,
//                'member_id' => $this->_userId
//                )
//            );
            if (!empty($account) )
            {
                // Define data to display
                // = todo with the popup not here
                // Display pop up for login
                // if don't want to be connected set info for not be asking again
                var_dump('Je demande une connexion');
            }

            $session->askForConnect = false;
            // If accept to connect and is connected
            // Redo process with logged status = true
            $this->log();
        }
    }

    private function _cleanEmptyFields(array $data)
    {
        foreach ($data as $key => $value)
        {
            if (empty($value) || count($value) == 0)
                unset($this->_data['L_Data'][$key]);
        }
    }

    private function _processLoggedIn()
    {
        if (!empty($this->_history))
        {
            // Was the user connected before ?
            // Get user email
            if (!empty($this->_history['email']))
            {
                // Set data for the cookie
                $this->_userEmail = $this->_history['email'];
                $this->_data['L_UserID'] = $this->_userId;
            }
            else
            {
                // Get user id (last session id)
                $previousId = $this->_history['id'];
                if ($previousId == session_id())
                {
                    // update log data with the current sessionId where we find the old one
                    $this->_oLog->updateIds($previousId, $this->_userId);
                    $this->_data['L_UserID'] = $this->_userId;
                }

            }
        }
        elseif (empty($this->_userId))
            $this->_data['L_UserID'] = session_id();
        else
            $this->_data['L_UserID'] = $this->_userId;

    }

    private function _processLoggedOut()
    {
        // Ask for connection
        $this->askForLoggin();
        // A previous cookie has been setted?
        if (!empty($this->_history))
        {
            // Get user id
            $this->_userId = $this->_history['id'];
            // Get id (last session id)
            $previousId = $this->_history['id'];
            if ($previousId != session_id())
                $this->_userId = session_id();

            $this->_data['L_UserID'] = $this->_userId;
        }
        elseif (empty($this->_userId))
            $this->_data['L_UserID'] = session_id();
        else
            $this->_data['L_UserID'] = $this->_userId;
    }

    public function deleteByUser()
    {
        if (!empty($this->_userId))
        {
            $where = array('L_UserID' => $this->_userId);
            $this->_oLog->setWhere($where);
            $this->_oLog->deleteWhere();
        }
    }

    public function toScreen()
    {
        $data = array(
            'userId' => $this->_userId,
            'moduleId' => $this->_moduleId,
            'action' => $this->_action,
            'module' => $this->_module,
            'language' => Zend_Registry::get('languageID'),
        );
        if (empty($this->_typeOutput))
            $this->_typeOutput = 'screen';

        $options = array(
            'send' => true,
            'isHtml' => true,
            'moduleId' => $this->_moduleId,
            'event' => $this->_event,
            'type' => $this->_typeOutput,
            'recipient' => $this->_recipient,
            'data' => $data
        );

        $oNotification = new Cible_Notifications_Screen($options);

        return $oNotification->getMessage();
    }

}
