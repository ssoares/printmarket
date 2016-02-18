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
 * Manage Courrielleur campaigns.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Newsletters_Campaigns extends Cible_Newsletters
{
    protected $_component = 'Campaign';
    protected $_groupsActions = array(
        'addCampaign' => array('Campaign', 'Create'),
        'listC' => array('Campaign', 'GetList'),
        'setInfoC' => array('Campaign', 'SetInfo'),
        );

    public function __construct($options = array())
    {
        parent::__construct($options);

    }

    public function addCampaign($name)
    {
        $this->_data = array('name' => $name);
        $this->_action = 'Create';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_results = $this->_results['data'];
        }

        return $this;
    }

    public function setCampaignInfo()
    {
        if (!$this->_id){
            throw new Cible_Newsletters_Exception('Id not defined');
        }

        $this->_data = array(
            'campaign_id' => $this->_id,
            'status' => 'ongoing',
        );
        $this->_action = 'SetInfo';
        $this->process();

        return $this;
    }
    public function campaignsList()
    {
        if ($this->_id > 0){
            $this->_data = array(
                'campaign_id' => $this->_id
            );
        }
        $this->_action = 'GetList';
        $this->process();

        return $this;
    }

}
