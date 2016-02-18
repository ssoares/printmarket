<?php
class NewsletterReleasesMembers extends Zend_Db_Table
{
    protected $_name = 'Newsletter_ReleasesMembers';
    protected $_query = null;
    protected $_config = array();
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

    public function getData($id)
    {
        $select = $this->select()
                ->from($this->info('name'),
                    array(
                        'sentTo' => 'count(NRM_ReleaseID)',
                        'sentOnDate' => 'DATE(NRM_DateTimeReceived)',
                        'sentOnTime' => 'TIME_FORMAT(NRM_DateTimeReceived, "%H:%i")')
                )
                ->where('NRM_ReleaseID = ?', $id)
                ->group(array('sentOnDate'))
                ->order('NRM_DateTimeReceived ASC');

        return $this->fetchAll($select)->toArray();
    }

}