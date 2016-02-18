<?php

/**
 * LICENSE
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Newsletter
 * @copyright Copyright (c)2010 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterLog.php 1266 2013-08-27 14:35:36Z ssoares $
 */

/**
 * Manage the statistics reports for the nwesletter activities
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Newsletter
 * @copyright Copyright (c)2010 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterLog.php 1266 2013-08-27 14:35:36Z ssoares $
 */
class NewsletterLog extends LogObject
{
    const NB_DAY = 7;

    /**
     * Date corresponding to the limit to fetch latest activity data.
     *
     * @var type string
     */
    protected $_dateLimit = '';

    /**
     * Id of the module.
     *
     * @var type int
     */
    protected $_moduleId = 0;
    /**
     * Id of the release to filter data.
     *
     * @var type int
     */
    protected $_releaseId = null;
    /**
     * Id of the newsletter category to filter data.
     *
     * @var type int
     */
    protected $_categoryId = 0;
    /**
     * Id of the article to filter data.
     *
     * @var type int
     */
    protected $_articleId = 0;
    /**
     * Id of the reason of unsubscription to filter data.
     *
     * @var type int
     */
    protected $_reasonId = 0;
    /**
     * Starting date to define the beginning of the period of the statistic filter.
     *
     * @var type date|string
     */
    protected $_dateStart = "";
    /**
     * Ending date to define the end of the period of the statistic filter.
     *
     * @var type date|string
     */
    protected $_dateEnd = "";

    public function getReleaseId()
    {
        return $this->_releaseId;
    }

    public function setCategoryId($_categoryId)
    {
        $this->_categoryId = $_categoryId;
    }
    public function setReleaseId($_releaseId)
    {
        $this->_releaseId = $_releaseId;
    }

    public function getDateStart()
    {
        return $this->_dateStart;
    }
    public function getArticleId()
    {
        return $this->_articleId;
    }

    public function setArticleId($_articleId)
    {
        $this->_articleId = $_articleId;
    }

    public function getReasonId()
    {
        return $this->_reasonId;
    }

    public function setReasonId($_reasonId)
    {
        $this->_reasonId = $_reasonId;
    }

    public function setDateStart($_dateStart)
    {
        $oDate = new Zend_Date($_dateStart);
        $date = $oDate->toString('yyyy-MM-dd HH:mm:ss');
        $this->_dateStart = $date;
    }

    public function getDateEnd()
    {
        return $this->_dateEnd;
    }

    public function setDateEnd($_dateEnd)
    {
        $oDate = new Zend_Date($_dateEnd);
        $date = $oDate->toString('yyyy-MM-dd HH:mm:ss');
        $this->_dateEnd = $date;
    }


    public function __construct($options = array())
    {
        parent::__construct($options);

        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }

        parent::setModuleId($this->_moduleId);

        $now = Zend_Date::now();
        $date = $now->subDay(self::NB_DAY);
        $this->_dateLimit = $date->toString('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * Count the subscription and return the total.
     *
     * @return int
     */
    public function countSubscriptions()
    {
        (int) $total = 0;

        $select = $this->_db->select()
            ->from($this->_oDataTableName,
                'count(*), null as col')
            ->where('L_Action = "subscribe"')
            ->where('L_Datetime >= ?', $this->_dateLimit);

        $total = $this->_db->fetchOne($select);

        return $total;
    }

    /**
     * Count nomber of unsubscriptions and return the total
     *
     * @return int
     */
    public function countUnsubscribe()
    {
        (int) $total = 0;

        $select = $this->_db->select()
            ->from($this->_oDataTableName,
                'count(*), null as col')
            ->where('L_Action = "unsubscribe"')
            ->where('L_Datetime >= ?', $this->_dateLimit);

        $total = $this->_db->fetchOne($select);

        return $total;
    }

    /**
     * Retrieves the latest newsletter sent.
     *
     * @return array
     */
    public function getLastSending()
    {
        $data = array();

        $select = $this->_db->select()
            ->from('Newsletter_Releases', array('NR_ID', 'NR_MailingDateTimeEnd', 'NR_Title'))
//            ->where()
            ->order('NR_MailingDateTimeEnd Desc');

        $data = $this->_db->fetchRow($select);

        return $data;
    }

    /**
     * Retrieves the log lines for the releases.
     * If the release id is set then fetchs data for the current release.
     * Also allows to filter for an article.
     *
     * @param bool $sqlOnly
     * @param bool $articlesOnly
     *
     * @return array
     */
    public function getReleaseLog($sqlOnly = false, $articlesOnly = false)
    {
        $data = array();
        $log  = array();

        $select = parent::getAll(NULL, false);
        if (!$articlesOnly)
        {
            $select->where('L_Action != ?', 'subscribe');
            $select->where('L_Action != ?', 'unsubscribe');
        }

        if ($this->_releaseId > 0)
            $select->where ("L_Data like ?", 'releaseID|' . $this->_releaseId . '%');

        $select = $this->_addDateFilter($select);

        if ($sqlOnly)
            return $select;

        $data = $this->_db->fetchAll($select);

        foreach ($data as $record)
        {
            $prevValue = 0;
            $params = $record['L_Data'];
            $release = $this->getDataPairs($params);

            if (!empty($log[$release['releaseId']][$record['L_Action']]))
                $prevValue = $log[$release['releaseId']][$record['L_Action']];

            $log[$release['releaseId']][$record['L_Action']] = $prevValue + 1;
        }

        ksort($log);

        return $log;
    }

    public function getArticlesData($sqlOnly = true, $articlesOnly = true)
    {
        $log = array();
        $query = $this->_db->select()
            ->from('Newsletter_Articles', array('NA_ID', 'NA_Title'))
            ->where('NA_ReleaseID = ?' , $this->_releaseId)
            ->order('NA_ID');

        if ($this->_articleId > 0)
            $query->where('NA_ID = ?', $this->_articleId);

        try
        {

            $articles = $this->_db->fetchAll($query);

            foreach ($articles as $data)
            {
                $articlesData[$data['NA_ID']] = $data['NA_Title'];
            }

            $select = $this->getReleaseLog($sqlOnly, $articlesOnly);
            $select->where('L_Action = ?', 'details');
            $data = $this->_db->fetchAll($select);

            foreach ($data as $record)
            {
                $prevValue = 0;
                $params    = $record['L_Data'];
                $pairs     = $this->getDataPairs($params);

                if (!empty($log[$pairs['articleId']][$record['L_Action']]))
                    $prevValue = $log[$pairs['articleId']][$record['L_Action']];

                $log[$pairs['articleId']][$record['L_Action']] = $prevValue + 1;
                $log[$pairs['articleId']]['NA_Title'] = $articlesData[$pairs['articleId']];
                $log[$pairs['articleId']]['NA_ReleaseID'] = $this->_releaseId;
            }
            if (count ($log) == 0)
                $log[0]= array('details' => 0 ,'NA_Title' => '', 'NA_ReleaseID' => 0 );

            return $log;
        }
        catch (Exception $exc)
        {
            var_dump($query);
            echo $exc->getTraceAsString();
            exit;
        }
    }

    public function getSubscriptionLog($categoryId = null)
    {
        $data = array();
        $log  = array();
        $categories = $this->getCategoriesList();
        $select = parent::getAll(null, false);
        $select->where('L_Action = ?', 'subscribe');

        if ($categoryId > 0)
            $select->where ("L_Data like ?", 'categoryId|' . $categoryId . '%');

        if ($this->_releaseId > 0)
            $select->where ("L_Data like ?", 'releaseID|' . $this->_releaseId . '%');

        $select = $this->_addDateFilter($select);
        $data   = $this->_db->fetchAll($select);

        foreach ($data as $record)
        {
            $prevValue = 0;
            $params    = $record['L_Data'];
            $release   = $this->getDataPairs($params);

            if (!empty($log[$release['categoryId']][$record['L_Action']]))
                $prevValue = $log[$release['categoryId']][$record['L_Action']];

            $log[$release['categoryId']][$record['L_Action']] = $prevValue + 1;
            $log[$release['categoryId']]['CI_Title'] = $categories[$release['categoryId']]['CI_Title'];
        }

        ksort($log);

        return $log;
    }

    public function getUnsubscribeLog()
    {
        $data = array();
        $log  = array();
        $categories = $this->getCategoriesList();
        $select = parent::getAll(null, false);
        $select->where('L_Action = ?', 'unsubscribe');

        if ($this->_categoryId > 0)
            $select->where ("L_Data like ?", '%categoryId|' . $this->_categoryId . '||%');
        if (!is_null($this->_releaseId))
            $select->where ("L_Data like ?", 'releaseID|' . $this->_releaseId . '%');

        $select = $this->_addDateFilter($select);
        $data   = $this->_db->fetchAll($select);

        $nbReason  = 1;
        foreach ($data as $record)
        {
            $prevValue = 0;
            $params    = $record['L_Data'];
            $release   = $this->getDataPairs($params);

            $index = 0;
            if (count($release) > 1)
                $index = $release['releaseId'];

            if (!empty($log[$index][$record['L_Action']]))
                $prevValue = $log[$index][$record['L_Action']];

            $key = isset($release['other']) ? 'other' : 'unsubscrArg';
            if ($index == 0)
            {
                if (!empty($log[$index][$release[$key]]))
                {
                    $nbReason = ++$log[$index][$release[$key]];
                }

                    $log[$index][$release[$key]] = $nbReason;
                $log[$index][$record['L_Action']] = $prevValue + 1;
            }
            else
            {
                $log[$index][$record['L_Action']] = $prevValue + 1;
                $log[$index]['reason'] = $release[$key];
            }
        }

        ksort($log);

        return $log;
    }

    public function getViewersList($type = null)
    {
        $data = array();
        $log  = array();
        $log[0] = array('notLogged' => 0);
        $select = parent::getAll(null, false);
        $select->order('L_Datetime ASC');

        if ($type != 'sendTo')
        {
            if (null !== $type)
                $select->where ('L_Action = ?', $type);
            if ($this->_articleId > 0)
                $select->where ("L_Data like ?", '%articleId|' . $this->_articleId . '||%');
            if ($this->_categoryId > 0)
                $select->where ("L_Data like ?", '%categoryId|' . $this->_categoryId . '||%');
            if ($this->_releaseId > 0)
                $select->where ("L_Data like ?", 'releaseID|' . $this->_releaseId . '%');

            $select = $this->_addDateFilter($select);
            $data   = $this->_db->fetchAll($select);
        }
        else
        {
            $data = $this->getRecipients();
        }

        $oUser     = new GenericProfile();
        $prevValue = 1;
        $val       = 1;
        foreach ($data as $record)
        {
            $prevValue = 1;
            if (!empty($record['L_UserID']))
            {
                $user = $oUser->getMemberDetails($record['L_UserID']);

                if (!empty($log[$record['L_UserID']]['count']))
                    $prevValue = ++$log[$record['L_UserID']]['count'];

                $log[$record['L_UserID']] = array(
                    'firstName'  => $user['firstName'],
                    'lastName'   => $user['lastName'],
                    'L_Datetime' => $record['L_Datetime'],
                    'email'      => $user['email'],
                    'count'      => $prevValue,
                );
            }
            else
            {
                if (!empty($log[0]['notLogged']))
                    $val = ++$log[0]['notLogged'];

                $log[0] = array('notLogged' => $val);
            }

        }

        ksort($log);

        return $log;
    }

    public function getRecipients()
    {
        $data = array();
        $select = $this->_db->select()
            ->from('Newsletter_ReleasesMembers',
                array(
                    'L_UserID' => 'NRM_MemberID',
                    'L_Datetime' => 'NRM_DateTimeReceived'))
            ->where('NRM_ReleaseID = ?', $this->_releaseId)
            ;
        $data = $this->_db->fetchAll($select);

        return $data;
    }
    public function getNewslettersList()
    {
        $data = array();

        $select = $this->_db->select()
            ->from('Newsletter_Releases', array ('NR_ID', 'NR_Title', 'NR_SendTo', 'NR_TargetedTotal'))
            ->order('NR_Title ASC');

        $results = $this->_db->fetchAll($select);

        foreach ($results as $values)
        {
            foreach ($values as $key => $value)
                $data[$values['NR_ID']][$key] = $value;
        }

        ksort($data);

        return $data;
    }

    /**
     * @see parent::getDataPairs
     */
    public function getDataPairs($pairs)
    {
        return parent::getDataPairs($pairs);
    }

    public function getCategoriesList()
    {
        $categories = array();
        $data = Cible_FunctionsCategories::getRootCategoriesList($this->_moduleId);

        foreach ($data as $category)
        {
            $categories[$category['C_ID']] = $category;
        }

        return $categories;
    }

    public function getGlobalLog($data)
    {
        $render = array();
        $select = parent::getAll(null, false);
        $select->order('L_Datetime asc');
        if (!empty($data['userId']))
            $select->where('L_UserId = ?', $data['userId']);
//            $select->where('L_UserId = ?', 1);

        $results = $this->_db->fetchAll($select);

        foreach ($results as $log)
        {
            $tmpDetails = array();
            $tmpData = $this->getDataPairs($log['L_Data']);
            foreach ($tmpData as $key => $value)
            {
                $methods = get_class_methods($this);
                $tmpName = 'set' . ucfirst($key);
                if (array_search($tmpName, $methods))
                {
                    $this->$tmpName($value);
                    $paramMethod = '_getDetailsFor' . ucfirst($key);
                    $tmpDetails[$key] = $this->$paramMethod();
                    $this->$tmpName(0);

                    unset($tmpData[$key]);
                }
                else
                {
                    $tmpDetails[$key] = $value;
                }
            }

            $render[] = array(
                'L_Datetime' => $log['L_Datetime'],
                'L_Action'   => $log['L_Action'],
                'L_ModuleNm' => $data['module'],
                'L_Details' => $tmpDetails,
            );
        }

        return $render;
    }

    private function _addDateFilter(Zend_Db_Select $select)
    {
        if (!empty($this->_dateStart))
            $select->where ("L_Datetime >= ?", $this->_dateStart);
        if (!empty($this->_dateEnd))
            $select->where ("L_Datetime <= ?", $this->_dateEnd);
        return $select;
    }

    private function _getDetailsForReleaseId()
    {
        $data = array();
        $query = $this->_db->select()
            ->from('Newsletter_Releases', array ('NR_ID', 'NR_Title'))
            ->order('NR_Title ASC');

        if ($this->_releaseId > 0)
            $query->where('NR_ID = ?', $this->_releaseId);

        $releases = $this->_db->fetchAll($query);

        foreach ($releases as $data)
        {
            $releasesData[$data['NR_ID']] = $data['NR_Title'];
        }
        if (isset($releasesData[$this->_releaseId]) && $this->_releaseId > 0)
            $data = $releasesData[$this->_releaseId];

        return $data;
    }

    private function _getDetailsForArticleId()
    {
        $data = array();
        $query = $this->_db->select()
            ->from('Newsletter_Articles', array('NA_ID', 'NA_Title'))
//            ->where('NA_ReleaseID = ?' , $this->_releaseId)
            ->order('NA_ID');

        if ($this->_articleId > 0)
            $query->where('NA_ID = ?', $this->_articleId);

        $articles = $this->_db->fetchAll($query);

        foreach ($articles as $data)
        {
            $articlesData[$data['NA_ID']] = $data['NA_Title'];
        }

        if (isset($releasesData[$this->_releaseId]) && $this->_articleId > 0)
            $data = $articlesData[$this->_articleId];
        return $data;
    }

    private function _getDetailsForCategoryId()
    {
        $categories = array();
        $data = Cible_FunctionsCategories::getCategoryDetails($this->_categoryId);

        return $data['CI_Title'];
    }
}
