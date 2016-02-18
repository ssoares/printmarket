<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatisticController
 *
 * @author soaser
 */
class Newsletter_StatisticController extends Cible_Extranet_Controller_Module_Action
{
    protected $_moduleID = 8;
    protected $_typesNoCount = array('sendTo', 'subscribe', 'unsubscribe');
    public $nlObject;


    public function init()
    {
        parent::init();
        $this->view->language = Cible_FunctionsGeneral::getLanguageSuffix($this->_currentInterfaceLanguage);
        $this->view->headLink()->appendStylesheet($this->view->locateFile('newsletterStats.css'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.dataTables.min.js', 'datatable'));
        $this->view->headScript()->appendFile($this->view->locateFile('dataTables.fourButtonNavigation.js', 'datatable/plugins'));
        $this->view->headScript()->appendFile($this->view->locateFile('statisticAction.js'));

        $this->nlObject = new Cible_Newsletters_Mailings();
        $this->nlObject->setConfig($this->_config)->setParameters();
    }
    public function indexAction()
    {
        $releaseId = $this->_getParam('releaseId');
        $data   = array();
        $oNlLog = $this->nlObject;
        $oRelease = new NewsletterReleases();
        $oRelease->setLangId($this->_defaultEditLanguage);
        $data['newsletters']   = $oRelease->getList(true);
        $this->view->isLatest = false;
        if (!$releaseId){
            $this->view->isLatest = true;
            $releaseId = $oRelease->getLatestId();
        }
        if (!empty($releaseId))
        {
            $tmpData = $oRelease->populate($releaseId, true)->toArray();
            $oNlLog->setId($tmpData['NR_ExternalId']);
            $this->view->releaseId = $tmpData['NR_ExternalId'];
            $data['details'] = $oNlLog->getInfo()->getResults();
            $this->view->actionLabels = $oNlLog->getStatActions();
            $oNlLog->setData(array(
                'start_date' => $data['details']['scheduled_on'],
                'count' => 'true',
                ));
            $data['opened'] = $oNlLog->setData(array('action' => 'opened'))
                ->getLog()->getResults();
            $data['clickthru'] = $oNlLog->setData(array('action' => 'clickthru'))
                ->getLog()->getResults();
            $data['bounce'] = $oNlLog->setData(array('action' => 'bounce'))
                ->getLog()->getResults();
            $data['bounce_hb'] = $oNlLog->setData(array('action' => 'bounce_hb'))
                ->getLog()->getResults();
            $data['spam'] = $oNlLog->setData(array('action' => 'spam'))
                ->getLog()->getResults();

            $data['unsubscribe']  = $data['details']['unsubscribes'];
            $this->view->data = $data;
            $tmpHtml = array();
            $this->view->filters = array();
            foreach($this->view->actionLabels as $action => $value){
                $tmpHtml[$action] = $this->getHtmlTemplates($action);
            }
            $this->view->templates = $tmpHtml;
            $linkInfo = $oNlLog->getLinksLog()->getResults();
            usort($linkInfo, function ($a, $b) {
                return (int)$b['unique'] - (int)$a['unique'];
              });
            $this->view->linksInfo = $linkInfo;
        }else{
            $this->view->noData = true;
        }

    }

    public function getHtmlTemplates($action)
    {
        $data['limit']= 10;
        $data['offset'] = 0;
        $data['action'] = $action;
        $data['uniques'] = 'true';
        $data['count'] = 'true';
//        $logData = $this->nlObject->setData($data)
//            ->getLog()->getResults();
        $this->view->logData = array(); //$logData;
        $this->view->report = $action;
        switch($action)
        {
            case 'clickthru':
                if (!$this->_isXmlHttpRequest){
                    $this->_buildFilterList($action, $data);
                }
            case 'opened':
                $html = $this->view->render('statistic/openedclick.phtml');
                break;
            case 'bounce':
                $html = $this->view->render('statistic/'. $action .'.phtml');
                if (!$this->_isXmlHttpRequest){
                    $this->_buildFilterList($action, $data);
                }
                break;
            case 'unopened':
                $html = $this->view->render('statistic/'. $action .'.phtml');
                break;

            case 'spam':
                if (!$this->_isXmlHttpRequest){
                    $this->_buildFilterList($action, $data);
                }
            default:
                $html = $this->view->render('statistic/standard.phtml');
                break;
        }

        return $html;

    }

    private function _buildFilterList($action, $data)
    {
        $tmpValues[0] = Cible_Translation::getCibleText('newsletter_send_filter_selectOne');
        foreach($this->view->actionLabels[$action] as $key => $value)
        {
            if ($key != 0 || $key != 'all'){
                switch($action)
                {
                    case 'clickthru':
                        $data['action'] = $action;
                        $data['extra'] = $value;
                        break;
                    case 'spam':
                        $data['action'] = $action;
                        $data['extra'] = $key;
                        break;
                    case 'bounce':
                    default:
                        $data['action'] = $key;
                        break;
                }
                $tmp = $this->nlObject->setData($data)
                    ->getLog()->getResults();

                if ((int)$tmp['unique'] > 0){
                    $tmpValues[$key] = $this->view->actionLabels[$action][$key]
                        . ' (' . $tmp['unique'] . ')';
                }else{
                    $tmpValues[$key] = $this->view->actionLabels[$action][$key];
                }
            }
        }
        $this->view->filters[$action] = $tmpValues;
    }

    public function ajaxAction()
    {
        $this->disableLayout();
        $this->disableView();
        $params = $this->_getAllParams();
        if (!empty($params['releaseId'])){
            $releaseId = ($params['releaseId'] == 'undefined' || $params['releaseId'] == 'empty') ? 0 : $params['releaseId'];
            $this->nlObject->setId($releaseId);
        }
        if (!empty ($params['startDate']) && $params['startDate'] != 'undefined'){
            $tmp = Cible_FunctionsGeneral::dateToString($params['startDate'],
                Cible_FunctionsGeneral::DATE_SQL);
            $data['start_date'] = date('Y-m-d H:i:s', strtotime($tmp));
        }
        if (!empty ($params['endDate']) && $params['endDate'] != 'undefined'){
            $tmp = Cible_FunctionsGeneral::dateToString($params['endDate'],
                Cible_FunctionsGeneral::DATE_SQL);
            $data['end_date'] = date('Y-m-d H:i:s', strtotime($tmp));
        }
        if (isset($params['sEcho'])){
            $report = str_replace('tab-', '', $params['reportTab']);
            $data['limit']= empty($params['iDisplayLength']) ? 10 : (int)$params['iDisplayLength'];
//            $page = empty($params['page']) ? 1 : (int)$params['page'];
            $data['offset'] = $params['iDisplayStart']; //$page > 1 ? ($page - 1) * $data['limit'] :  0;
            $data['action'] = $report;
            $data['uniques'] = 'true';
            $actions = $this->nlObject->getStatActions();
            switch($report)
            {
                case array_key_exists($report, $actions['clickthru']):
                    $data['action'] = 'clickthru';
                    $data['extra'] = $actions['clickthru'][$report];
                    break;
                case array_key_exists($report, $actions['spam']):
                    $data['action'] = 'spam';
                    $data['extra'] = $report;
                    break;
                case array_key_exists($report, $actions['bounce']):
                default:
                    $data['action'] = $report;
                    break;
            }
            $this->nlObject->setData($data)->getLog();
            $logs = $this->_buildPluginData($report, $actions);
            $data['count'] = 'true';
            $logCount = $this->nlObject->setData($data)->getLog()->getResults();
            $return = array('sEcho' => $params['sEcho'],
                "iTotalRecords" => $logCount['unique'],
                "iTotalDisplayRecords" => $logCount['unique'],
                "aaData" => $logs
                );
            echo json_encode($return);
        }else{
            $report = $params['report'];
            echo $this->getHtmlTemplates($report);
        }
        exit;
    }

    private function _buildPluginData($action, $actions)
    {
        $dataFilter = array();
        $results = $this->nlObject->getResults();
        foreach($results as $log){
            $name = isset($log['l_GPLastName']) ? $log['l_GPLastName'] : '';
            $firstname = isset($log['l_GPFirstName']) ? $log['l_GPFirstName'] : '';
            switch($action)
            {
                case array_key_exists($action, $actions['clickthru']):
                case 'clickthru':
                case 'opened':
                    $log = array($log['time'], $log['total'],
                         $log['email'], $firstname, $name);
                    break;
                case array_key_exists($action, $actions['bounce']):
                case 'bounce':
                    $type = Cible_Translation::getCibleText('email_bounce_label_' . $log['action']);
                    $log = array($log['email'], $type,
                        $log['time'], $firstname, $name);
                    break;
                case 'unopened':
                    $log = array($log['email'], $firstname, $name);
                    break;

                default:
                    $log = array($log['time'], $log['email'], $firstname, $name);
                    break;
            }
            $dataFilter[] = $log;
        }

        return $dataFilter;
    }

    public function addAction(){}
    public function editAction(){}
    public function deleteAction(){}

}