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
 * Manage Courrielleur Lists.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Newsletters_Lists extends Cible_Newsletters
{
    protected $_component = 'List';
    protected $_groupsActions = array(
        'membersLst' => array('List', 'Show'),
        'memberInfo' => array('List', 'GetRecord'),
        'listFields' => array('List', 'GetFields'),
        'listFilters' => array('List', 'GetSublists'),
        'addFilter' => array('List', 'CreateSublist'),
        'delFilter' => array('List', 'DeleteSublist'),
        'subscribe' => array('List', 'SubscribeEmail'),
        'unsubscribe' => array('List', 'UnsubscribeEmail'),
        );

    public function __construct($options = array())
    {
        parent::__construct($options);

    }

    public function subLists()
    {
        $this->setData(array('list_id' => $this->_id));
        $this->_action = 'GetSublists';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data']['sublists'];
        }

        return $this;
    }

    /**
     * Add a new sublist to the list as a filter.
     * @param sring $name The name of the filter list.
     * @param sring $query The where part for the query to filter list.
     * i.e : '(`id` = "2" OR `id` = "8" OR `id` = "6" OR `id` = "7")'
     * @return \NewsletterObject
     */
    public function addSubList()
    {
        $this->setData();
        $this->addListId();
        $this->_action = 'CreateSublist';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = (int)$this->_results['data'];
        }
        return $this;
    }

    public function getInfo()
    {
        $this->addListId();
        $this->_action = 'GetInfo';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;

    }

    public function deleteSubList()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }
        $this->setData(array('sublist_id' => $this->_id), true);
        $this->_action = 'DeleteSublist';
        $this->process();

        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;
    }

    public function subscribeToExternal($formData)
    {
        $langId = Zend_Registry::get('languageID');
        $nlId = $this->_config->externalTool->listOne->$langId;
        $infos = array('GPFirstName' => $formData['firstName'], 'GPLastName' => $formData['lastName']);
        if (!empty($formData['salutation'])){
            $infos['GPSalutation'] = Cible_FunctionsGeneral::getSalutations($formData['salutation']);
        }
        $data = array(
            'user_key' => $this->_userKey,
            'list_id' => $nlId,
            'data' => $infos
        );

        if (in_array($this->_status, $this->_validStatus)
            && !empty($formData['externalId'])){
            $this->setUrl('UpdateRecord');
            $data['record_id'] = $formData['externalId'];
            $this->_updated = true;
        }else{
            $this->setUrl('SubscribeEmail');
            $data['email'] = $formData['email'];
        }
        $this->_curlCall($data);
        if ($this->_results['status'] == 'success')
        {
            $newsletterProfile = new NewsletterProfile();
            if (!$this->_updated){
                $formData['externalId'] = $this->_results['data'];
            }
            $formData['newsletter_categories'] = $nlId;
            if (!empty($_SERVER['REMOTE_ADDR'])){
                $formData['addressIp'] = $_SERVER['REMOTE_ADDR'];
            }
            $formData['NP_TypeID'] = 20;
            $formData['NP_SubscriptionDate'] = date('Y-m-d H:i:s',time());
            $newsletterProfile->updateMember($formData['member_id'], $formData);
        }
    }

    public function unsubscribeToExternal($formData)
    {
        $langId = $this->_langId;
        $nlId = $this->_config->externalTool->listOne->$langId;
        $data = array(
            'user_key' => $this->_userKey,
            'list_id' => $nlId,
            'email' => $formData['email'],
        );
        $this->setUrl('UnsubscribeEmail');
        $this->_curlCall($data);
        if ($this->_results['status'] == 'success')
        {
            $newsletterProfile = new NewsletterProfile();
            $formData['newsletter_categories'] = 0;
            $newsletterProfile->updateMember($formData['member_id'], $formData);
        }
    }

    public function subscribe()
    {
        $this->addListId();
        $this->process();
        if ($this->_results['status'] == 'success')
        {
            $this->_results = $this->_results['data'];
        }
    }


    public function unsubscribe()
    {
        $this->addListId();
        $this->_action = 'UnsubscribeEmail';
        $this->process();
        if ($this->_results['status'] == 'success')
        {
            $this->_results = $this->_results['data'];
        }
    }

    public function checkSubscription($formData)
    {
        $langId = Zend_Registry::get('languageID');
        $nlId = $this->_config->externalTool->listOne->$langId;
        $data = array(
            'user_key' => $this->_userKey,
            'list_id' => $nlId,
            'record_id' => $formData['externalId'],
        );
        $this->setUrl('GetRecord');
        $this->_curlCall($data);
        $newsletterProfile = new NewsletterProfile();
        if ($this->_results['status'] == 'success')
        {
            $this->_status = $this->_results['data']['status'];
            if (!in_array($this->_status, $this->_validStatus)
                && $formData['newsletter_categories'] != 0)
            {
                $formData['newsletter_categories'] = 0;
                $newsletterProfile->updateMember($formData['member_id'], $formData);
                $this->_updated = true;
            }
            elseif (in_array($this->_status, $this->_validStatus)
                && empty($formData['newsletter_categories']))
            {
                $formData['newsletter_categories'] = $nlId;
                $newsletterProfile->updateMember($formData['member_id'], $formData);
                $this->_updated = true;
            }
        }
        elseif ($this->_results['status'] == 'failed')
        {
            $formData['newsletter_categories'] = 0;
            $newsletterProfile->updateMember($formData['member_id'], $formData);
        }

        return $this;
    }

    public function checkStatus()
    {
        $this->addListId();
        $this->_action = 'GetRecord';
        $this->process();
        if ($this->_results['status'] == 'success')
        {
            $this->_status = $this->_results['data']['status'];
            $this->_results = $this->_results['data'];
        }
        elseif ($this->_results['status'] == 'failed')
        {
        }

        return $this;
    }


}
