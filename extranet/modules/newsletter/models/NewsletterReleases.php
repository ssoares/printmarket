<?php
class NewsletterReleases extends Zend_Db_Table
{
    protected $_name = 'Newsletter_Releases';
    protected $_query = null;
    protected $_config = array();
    protected $_status = array('incomplete' => 3, 'delivered' => 1, 'scheduled' => 2);
    protected $_action = '';
    protected $_langId = 0;

    public function getLangId()
    {
        return $this->_langId;
    }

    public function setLangId($langId)
    {
        $this->_langId = $langId;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setAction($action)
    {
        $this->_action = $action;
        return $this;
    }

    public function setConfig($config)
    {
        $this->_config = $config;
        return $this;
    }

    public function getQuery()
    {
        if (is_null($this->_query)){
            $this->defaultSelectStatement();
        }
        return $this->_query;
    }

    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

    public function defaultSelectStatement()
    {
        $this->_query = $this->select()
            ->setIntegrityCheck(false)
            ->from('Newsletter_Releases')
            ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID')
            ->join('Status', 'Newsletter_Releases.NR_Online = Status.S_ID')
            ->where('CI_LanguageID = ?', Cible_Controller_Action::getDefaultEditLanguage());
        return $this;
    }

    public function getReleaseData($id)
    {
        $this->_query = $this->select()
            ->setIntegrityCheck(false)
            ->from('Newsletter_Releases')
            ->join('Languages', 'L_ID = NR_LanguageID')
            ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID')
            ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID')
            ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID')
            ->where('NR_ID = ?', $id);

        return $this->_getDataByAction();
    }

    private function _getDataByAction()
    {
        switch($this->_action)
        {
            case 'sendMassMailing':
                $this->_query->where('CI_LanguageID = NR_LanguageID')
                    ->where('NMI_LanguageID = NR_LanguageID')
                    ->where('NR_Status <> 1');
                $newsletterData = $this->fetchAll($this->_query);
                break;

            default:
                $this->_query->where('CI_LanguageID = ?', $this->_langId)
                ->where('NMI_LanguageID = ?', $this->_langId);
                $newsletterData = $this->fetchRow($this->_query);
                break;
        }

        return $newsletterData;
    }

    public function checkExternalStatus()
    {
        if (is_null($this->_query)){
            $this->defaultSelectStatement();
        }

        $newsletters = $this->fetchAll($this->_query);
        $oMailing = new Cible_Newsletters_Mailings();
        $oMailing->setConfig($this->_config)->setParameters();
        foreach($newsletters as $values)
        {
            if ($values['NR_Status'] != 1 && !empty($values['NR_ExternalId']))
            {
                $info = $oMailing->setId($values['NR_ExternalId'])
                    ->getInfo()
                    ->getResults();
                switch($info['status'])
                {
                    case 'scheduled':
                        $where = 'NR_ID = ' . $values['NR_ID'];
                        $data = $this->_setUpdateData($info);
                        $this->update($data,$where);
                        break;
                    case 'delivered':
                        $where = 'NR_ID = ' . $values['NR_ID'];
                        $data = $this->_setUpdateData($info);
                        $this->update($data,$where);
                        $oList = new Cible_Newsletters_Lists();
                        $oList->setConfig($this->_config)->setParameters()
                            ->setId($info['sublist_id'])->deleteSublist();
                        break;
                    case 'incomplete':

                        break;

                    default:
                        break;
                }
            }
        }
    }

    private function _setUpdateData($info)
    {
        $data = array('NR_Status' => $this->_status[$info['status']],
            'NR_MailingDateTimeStart' => $info['scheduled_on'],
        );
        if (!empty($info['recipients'])){
            $data['NR_SendTo'] = $info['recipients'];
        }
        if (!empty($info['in_queue'])){
            $data['NR_TargetedTotal'] = $info['in_queue'];
        }

        return $data;
    }

    public function populate($id, $external = false)
    {
        $this->_query = $this->select();
        if ($external){
            $this->_query->where('NR_ExternalId = ?', $id);
        }else{
            $this->_query->where('NR_ID = ?', $id);
        }

        $releaseData = $this->fetchRow($this->_query);
        return $releaseData;
    }

    public function getList($forStats = false)
    {
        $this->defaultSelectStatement();
        if ($forStats){
            $this->_query->where('NR_Status = ?', 1);
        }
        $this->_query->order('NR_MailingDateTimeStart DESC');
        $data = $this->fetchAll($this->_query);
        foreach($data as $nl){
            $list[$nl['NR_ExternalId']] = $nl['NR_Title'];
        }
        return $list;
    }

    public function getLatestId()
    {
        $this->defaultSelectStatement();
        $this->_query
            ->order('NR_MailingDateTimeStart DESC')
            ->limit(1);
        $data = $this->fetchRow($this->_query);
        if ($data){
            $data = $data->toArray();
        }
        return (int)$data['NR_ExternalId'];

    }
}