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
 * Manage Courrielleur Mailings.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Newsletters_Mailings extends Cible_Newsletters
{
    protected $_component = 'Mailing';
    protected $_groupsActions = array();

    protected $_statActions = array(
        'opened' => array(),
        'unopened' => array(),
        'bounce' => array('all',
            'bounce_ac', // Changer l'adresse (0)
            'bounce_ar', // Réponse automatisée (0)
            'bounce_cr', // Essai/réponse (CR) (0)
            'bounce_df', // Problème de DNS (0)
            'bounce_hb', // Non-livrés permanents
            'bounce_mb', // Courriel bloqué (0)
            'bounce_fm', // Boîte de courriel pleine (0)
            'bounce_sb', // Non-livrés temporaires (0)
            'bounce_tr', // Tentatives de livraison (0)
            ),
        'unsubscribe' => array(),
        'spam' => array('aol',
            'bluetie',
            'comcast',
            'cox',
            'mailtrust',
            'msn',
            'rr',
            'spamcop',
            'tucows',
            'usanet',
            'unitedonline',
            'yahoo',
            ),
        'forward' => array(),
        'in_queue' => array(),
        'clickthru' => array(), // Liste des liens de l'infolettre à recup
    );

    public function getStatActions()
    {
        $this->_formatFiltersArray();
        return $this->_statActions;
    }

    public function addMailing($name)
    {
        $this->setData(array('name' => $name));
        $this->_action = 'Create';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;
    }

    public function getMailings()
    {
        if ($this->_id > 0){
            $this->_data['list_id'] = $this->_id;
        }
        $this->_action = 'GetList';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data']['mailings'];
        }

        return $this;
    }

    public function delMailing()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }
        $this->setData(array('mailing_id' => $this->_id));
        $this->_action = 'Delete';
        $this->process();

        return $this;
    }

    public function setMailingInfo()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }
        $this->setData(array('mailing_id' => $this->_id));
        $this->addListId();
        $this->_action = 'SetInfo';
        $this->process();

        return $this;
    }

    public function sendMailing()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('mailing_id' => $this->_id));
        $this->_action = 'Schedule';
        $this->process();

        return $this;
    }

    public function getHtmlMessage()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('mailing_id' => $this->_id));
        $this->_action = 'GetHtmlMessage';
        $this->process();

        return $this;
    }

    public function getLog()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }
        $tmp = array('mailing_id' => $this->_id);
        $this->setData($tmp);
        $this->_action = 'GetLog';
        $this->process();
        if ($this->_results['status'] == 'success'){
            if (isset($this->_results['data']['logs'])){
                $this->_results = $this->_results['data']['logs'];
            }else{
                $this->_results = $this->_results['data'];
            }
        }

        return $this;

    }

    public function getInfo()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('mailing_id' => $this->_id), true);
        $this->_action = 'GetInfo';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;

    }

    public function getLinks()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('mailing_id' => $this->_id), true);
        $this->_action = 'GetLinks';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data']['links'];
        }

        return $this;

    }

    public function getDataLinks()
    {
        $links = $this->getLinksLog()->getResults();
        $data[0] = Cible_Translation::getCibleText('newsletter_stats_label_clickthru');
        foreach($links as $link)
        {
            $id = (int)$link['id'];
            $data[$id] = $link['link_to'];
        }
        krsort($data );
        return $data;
    }

    public function getLinkInfo()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('link_id' => $this->_id), true);
        $this->_action = 'GetLinkInfo';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;
    }

    public function getLinksLog()
    {
        $this->setData(array('mailing_id' => $this->_id), true);
        $this->_action = 'GetLinksLog';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data']['links'];
        }

        return $this;
    }

    public function sendTestMailing()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->setData(array('mailing_id' => $this->_id));
        $this->_action = 'SendTestEmail';

        $this->process();

        return $this;
    }

    private function _formatFiltersArray()
    {
        foreach($this->_statActions as $key => $value)
        {
            switch($key)
            {
                case 'bounce':
                    $this->_statActions[$key] = $this->_getBounceLabels($value);
                    break;
                case 'spam':
                    $this->_statActions[$key] = $this->_getSpamLabels($value);
                    break;
                case 'clickthru':
                    $this->_statActions[$key] = $this->getDataLinks();
                    break;

                default:
                    $this->_statActions[$key] = Cible_Translation::getCibleText('newsletter_stats_label_' . $key);
                    break;
            }
        }
    }

    private function _getBounceLabels($filters = array())
    {
        $arrayLabels[0] = Cible_Translation::getCibleText('newsletter_stats_label_bounce');
        foreach($filters as $key){
            $arrayLabels[$key] = Cible_Translation::getCibleText('email_bounce_label_' . $key);
        }
        return $arrayLabels;
    }
    private function _getSpamLabels($filters = array())
    {
        $arrayLabels[0] = Cible_Translation::getCibleText('newsletter_stats_label_spam');
        foreach($filters as $key){
            $arrayLabels[$key] = Cible_Translation::getCibleText('email_spam_label_' . $key);
        }
        return $arrayLabels;
    }

}
