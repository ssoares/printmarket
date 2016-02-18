<?php
/**
 * Newsletter Profile data
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_NewsletterProfilesObject
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */

/**
 * Manages Newsletter Profile data.
 *
 * @category  Cible
 * @package   Cible_NewsletterProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */
class NewsletterProfilesObject extends DataObject
{

    protected $_dataClass   = 'NewsletterProfilesData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = 'NP_ProfileId';
    protected $_conditions = array('subscribed' => '(NP_Categories IS NOT NULL OR NP_Categories <> "" OR NP_Categories > 0)');

    public function getFilterCondition($field = '')
    {
        return $this->_conditions[$field];
    }
    public function findData($filters = array(), $useQry = false)
    {
        $data = parent::findData($filters);
        if (!empty($data))
        {
            $data = $data[0];
            $data['NP_Categories'] = explode(',', $data['NP_Categories']);
        }
        return $data;
    }

    public function save($id, $data, $langId)
    {
        $oGP = new GenericProfilesObject();
        $profile = $oGP->populate($id, 1);
        $data = array_merge($data, $profile);
        $oList = new Cible_Newsletters_Lists();
        $oList->setConfig(Zend_Registry::get('config'))
            ->setParameters()->setLoginUserInfo();
        $st = $oList->setData(array('record_id' => $data['NP_ExternalId']))
            ->checkStatus()
            ->getStatus();
        $validSt = $oList->getValidStatus();
        $new = false;
        if (!empty($data['NP_Categories']))
        {
            $post['data'] = array('GPFirstName' => $data['GP_FirstName'],
                'GPLastName' => $data['GP_LastName']
                );
            if (in_array($st, $validSt) && !empty($data['NP_ExternalId'])){
                $post['record_id'] = $data['NP_ExternalId'];
                $action = 'UpdateRecord';
            }else{
                $post['email'] = $data['GP_Email'];
                $action = 'SubscribeEmail';
                $new = true;
            }
            $oList->setData($post, $new)->setAction($action)->subscribe();
            if ($new && empty($data['NP_ExternalId'])){
                $data['NP_ExternalId'] = $oList->getResults();
            }
        }else{
            $post = array('email' => $data['GP_Email']);
            $oList->setData($post)->unsubscribe();
        }

        parent::save($id, $data, $langId);
    }

    public function delete($id)
    {
        $oGP = new GenericProfilesObject();
        $profile = $oGP->populate($id, 1);
        $oList = new Cible_Newsletters_Lists();
        $oList->setConfig(Zend_Registry::get('config'))
            ->setParameters()->setLoginUserInfo();
        $oList->setData(array('email' => $profile['GP_Email']))->unsubscribe();
        return parent::delete($id);
    }
}
