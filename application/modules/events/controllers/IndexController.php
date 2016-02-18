<?php


class Events_IndexController extends Cible_Controller_Action
{

    protected $_showBlockTitle = false;
    protected $_moduleID = 7;
    protected $_moduleTitle = 'events';

    /**
     * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
     *
     * This function return the sitemap specific for this module
     *
     * @access public
     *
     * @return a string containing xml sitemap
     */

     public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('events.css'),'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('events.css'),'all');
        $this->view->assign('otherData', false);
    }

    public function siteMapAction()
    {
        $eventsRob = new EventsRobots();
        $dataXml = $eventsRob->getXMLFile($this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);
    }

    public function detailssidelistAction()
    {

        $_blockID = $this->_request->getParam('BlockID');
        $id = $this->_request->getParam('ID');

        $events = new EventsCollection($_blockID);

        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($events->getBlockParam('1'), 'listall');
        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($events->getBlockParam('1'), 'details');

        $this->view->assign('listall_page', $listall_page);

        $this->view->assign('details_page', $details_page);

        $this->view->assign('events', $events->getOtherEvents($events->getBlockParam('2'), $id));
    }

    public function detailsAction()
    {
        $downloadCal = $this->_getParam('calendar');

        if (!empty($_SERVER['HTTP_REFERER']))
        {
            $this->view->assign('pagePrecedente', $_SERVER['HTTP_REFERER']);
        }
        else
        {
            $this->view->assign('pagePrecedente', '');
        }
        $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($this->_request->getPathInfo());
        $id = 0;

        $events = new EventsCollection();
        if ($titleUrl != "")
        {
            $id = $events->getIdByName($titleUrl);
        }
        if($id==""){
            $id = $this->_request->getParam('ID');
        }

        $data = $events->getDetails($id);
        if (empty($data))
        {
            $otherData = (bool)$this->_hasContent($events, $id);
            if ($otherData)
                $this->view->assign('otherData', true);
        }
        if (!empty($downloadCal))
        {
            $this->downloaCalendarAction ($data);
        }
        $_blockID = $this->_request->getParam('BlockID');
        $events = new EventsCollection($_blockID);
        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($events->getBlockParam('1'), 'listall');
        $this->view->assign('listall_page', $listall_page);

        $this->view->assign('events', $data);
    }

    public function downloaCalendarAction($data)
    {
        $this->disableLayout();
        $this->disableView();
        $calendar = new qCal();
        $todo = new qCal_Component_Vevent(array(
            'class' => 'private',
            'dtstart' => $data[0]['dates'][0]['EDR_StartDate'],
            'dtend' => $data[0]['dates'][0]['EDR_EndDate'],
            'description' => '',
            'summary' => Cible_FunctionsGeneral::html2text($data[0]['EI_Title']),
            'location' => Cible_FunctionsGeneral::html2text($data[0]['EI_BriefText']),
            'priority' => 1,
        ));
        $todo->attach(new qCal_Component_Valarm(array(
            'action' => 'audio',
            'trigger' => $data[0]['dates'][0]['EDR_StartDate'],
        )));

        $calendar->attach($todo);
        $iCalData = $calendar->render();

        header ('Content-type: text/calendar');
        header('Cache-Control: public');
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=cal.ics');
        echo trim($iCalData);
        exit;
    }

    public function homepagelistAction()
    {
        $_blockID = $this->_request->getParam('BlockID');

        $events = new EventsCollection($_blockID);

        $listall_page = Cible_FunctionsCategories::getPagePerCategoryView($events->getBlockParam('1'), 'listall', 7);
        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($events->getBlockParam('1'), 'details', 7);

        $data = $events->getList($events->getBlockParam('2'));
        if (empty($data))
        {
            $otherData = (bool)$this->_hasContent($events);
            if ($otherData)
                $this->view->assign('otherData', true);
        }
        $this->view->assign('listall_page', $listall_page);
        $this->view->assign('details_page', $details_page);
        $this->view->assign('events', $data);


    }

    public function homepagelist2Action()
    {
        $this->homepagelistAction();
    }

    public function homepagelist3Action()
    {
        $this->homepagelistAction();
    }

     public function listallAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $eventsObject = new EventsCollection($_blockID);
        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($eventsObject->getBlockParam('1'), 'details', $this->_moduleID, null, true);
        $this->view->assign('details_page', $details_page);
        $dbs = Zend_Registry::get('dbs');
        $list = Zend_Registry::get('sitesList');
        $defaultAdapter = $dbs->getDb();
        $eventsArray = array();
        foreach ($list as $key => $value)
        {
            $dbAdapter = $dbs->getDb($value);

            Zend_Registry::set('db', $dbAdapter);
            $eventsObjectTmp = new EventsCollection($_blockID);
            array_push($eventsArray, $eventsObjectTmp->getList(NULL,$value));
        }
        Zend_Registry::set('db', $defaultAdapter);
        $events = array();
        foreach ($eventsArray as $key1 => $value1)
        {
            foreach ($value1 as $key2 => $value2)
            {
                $date_stringURL = '';
                $startDate = new Zend_Date($value2['EDR_StartDate'], null, 'fr_CA');
                $endDate = new Zend_Date($value2['EDR_EndDate'], null, 'fr_CA');
                $date_stringURL = sprintf("%d-%d-%d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH), $startDate->get(Zend_Date::YEAR));

                $detail_page = $this->view->pagePerCategoryViewMultiSite(array(
                    'categoryId' => $value2['ED_CategoryID'],
                    'viewName' => 'details',
                    'module' => $this->_moduleID,
                    'site' => $value2['site']));
                $value2['URL'] = $this->_config->domainNames->$value2['site'] . $detail_page . "/" . $date_stringURL . "/" . $value2['EI_ValUrl'];

                array_push($events,$value2);
            }
        }

        function my_sort($v1,$v2)
        {
            return strtotime($v1['EDR_StartDate']) - strtotime($v2['EDR_StartDate']);
        }
        usort($events, "my_sort");
        if (empty($data))
        {
            $otherData = (bool)$this->_hasContent($eventsObject);
            if ($otherData)
                $this->view->assign('otherData', true);
        }
        $paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Array($events));
        $paginator->setItemCountPerPage($eventsObject->getBlockParam('2'));
        $paginator->setCurrentPageNumber($this->_request->getParam('page'));

        $this->view->assign('paginator', $paginator);
    }

    public function my_sort($a,$b)
    {
        if ($a==$b) return 0;
        return ($a<$b)?-1:1;
    }

    public function calendrierAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $this->view->BlockID = $_blockID;
        $events1 = new EventsCollection($_blockID);


        if ($this->_isXmlHttpRequest)
        {
            $_year = $this->_request->getParam('Year');
            $_month = $this->_request->getParam('Month');
            $eventsObject = new EventsCollection($_blockID);

            $dbs = Zend_Registry::get('dbs');
            $list = Zend_Registry::get('sitesList');
            $defaultAdapter = $dbs->getDb();
            $eventsArray = array();
            foreach ($list as $key => $value){
                $dbAdapter = $dbs->getDb($value);
                Zend_Registry::set('db', $dbAdapter);
                $eventsObjectTmp = new EventsCollection($_blockID);
                array_push($eventsArray, $eventsObjectTmp->getListYearMonth($_year, $_month, null,$value));
            }
            Zend_Registry::set('db', $defaultAdapter);
            $events = array();
            foreach ($eventsArray as $key1 => $value1)
            {
                foreach ($value1 as $key2 => $value2)
                {
                    array_push($events,$value2);
                }
            }

            function my_sort($v1,$v2)
            {
                return strtotime($v1['EDR_StartDate']) - strtotime($v2['EDR_StartDate']);
            }
            usort($events, "my_sort");


            $responseObject = array();
            $resultObject = array();
            foreach ($events as $key => $result)
            {
                foreach ($result['dates'] as $keydate1 => $row1)
                {
                    $date_string = '';
                    $date_stringURL = '';
                    $resultObject['EventID'] = $result['ED_ID'];
                    $resultObject['Title'] = strip_tags(1);
                    $resultObject['Description'] = strip_tags($result['EI_BriefText']);
                    $resultObject['StartDate'] = $row1['EDR_StartDate'];
                    $resultObject['EndDate'] = $row1['EDR_EndDate'];
                    $resultObject['site'] = $result['site'];
                    foreach ($result['dates'] as $keydate => $row)
                    {
                        $date_stringURL = '';
                        $startDate = new Zend_Date($row['EDR_StartDate'], null, 'fr_CA');
                        $endDate = new Zend_Date($row['EDR_EndDate'], null, 'fr_CA');
                        $date_stringURL = sprintf("%d-%d-%d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH), $startDate->get(Zend_Date::YEAR));

                        if (!empty($date_string))
                            $date_string .= ' et ';

                        if ($startDate->get(Zend_Date::MONTH) == $endDate->get(Zend_Date::MONTH) && $startDate->get(Zend_Date::YEAR) == $endDate->get(Zend_Date::YEAR))
                        {
                            if ($startDate->get(Zend_Date::DAY) != $endDate->get(Zend_Date::DAY))
                                $date_string .= sprintf("%d-%d %s %d", $startDate->get(Zend_Date::DAY), $endDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR));
                            else
                                $date_string .= sprintf("%d %s %d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR));
                        }
                        else
                            $date_string .= sprintf("%d %s %d au %d %s %d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR), $endDate->get(Zend_Date::DAY), $endDate->get(Zend_Date::MONTH_NAME), $endDate->get(Zend_Date::YEAR));
                    }

                    $detail_page = $this->view->pagePerCategoryViewMultiSite(array('categoryId' => $result['ED_CategoryID'],'viewName'=>'details','module'=>7,'site'=>$result['site']));
                    $resultObject['URL'] = $this->_config->domainNames->$result['site'] . "/" . $detail_page . "/" . $date_stringURL . "/" . $result['EI_ValUrl'];




                    $resultObject['DateComplete'] = $date_string;
                    $resultObject['CellsIds'] = "";


                    array_push($responseObject, $resultObject);
                }
            }
            $this->getHelper('viewRenderer')->setNoRender();
            echo json_encode($responseObject);
        }
    }

    public function listall2Action()
    {
        $this->listallAction();
    }

    public function calendrierpetitAction()
    {

        $_blockID = $this->_request->getParam('BlockID');
        $this->view->BlockID = $_blockID;

        if ($this->_isXmlHttpRequest)
        {
            $_year = $this->_request->getParam('Year');
            $_month = $this->_request->getParam('Month');


            $eventsObject = new EventsCollection($_blockID);

            $events = $eventsObject->getListYearMonth($_year, $_month, null);
            $details_page = Cible_FunctionsCategories::getPagePerCategoryView($eventsObject->getBlockParam('1'), 'details');
            $detail_page = $this->view->baseUrl() . '/' . $details_page . "/";

            $responseObject = array();
            $resultObject = array();

            foreach ($events as $key => $result)
            {
                //$date = new Zend_Date($result['EDR_StartDate'],null, (Zend_Registry::get('languageSuffix') == 'fr' ? 'fr_CA' : 'en_CA'));
                //$date_string_url = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_SQL,'-');
                $resultObject['EventID'] = $result['ED_ID'];
                $resultObject['Title'] = strip_tags($result['EI_Title']);
                $resultObject['Description'] = strip_tags($result['EI_BriefText']);
                //$resultObject['URL'] =  $this->baseUrl() . '/' . $this->details_page . "/"  . $date_string_url . "/" . $event['EI_ValUrl'];

                $date_string = '';
                $strd = '';

                foreach ($result['dates'] as $keydate => $row)
                {
                    //$resultObject['StartDate'] = $row['EDR_StartDate'];
                    //$resultObject['EndDate'] = $row['EDR_EndDate'];

                    $startDate = new Zend_Date($row['EDR_StartDate'], null, 'fr_CA');
                    $endDate = new Zend_Date($row['EDR_EndDate'], null, 'fr_CA');
                    $date_stringURL = sprintf("%d-%d-%d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH), $startDate->get(Zend_Date::YEAR));


                    if (!empty($date_string))
                        $date_string .= ' et ';

                    if ($startDate->get(Zend_Date::MONTH) == $endDate->get(Zend_Date::MONTH) && $startDate->get(Zend_Date::YEAR) == $endDate->get(Zend_Date::YEAR))
                    {
                        if ($startDate->get(Zend_Date::DAY) != $endDate->get(Zend_Date::DAY))
                            $date_string .= sprintf("%d-%d %s %d", $startDate->get(Zend_Date::DAY), $endDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR));
                        else
                            $date_string .= sprintf("%d %s %d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR));
                    }
                    else
                        $date_string .= sprintf("%d %s %d au %d %s %d", $startDate->get(Zend_Date::DAY), $startDate->get(Zend_Date::MONTH_NAME), $startDate->get(Zend_Date::YEAR), $endDate->get(Zend_Date::DAY), $endDate->get(Zend_Date::MONTH_NAME), $endDate->get(Zend_Date::YEAR));

                    //list($a, $m, $j) = explode("-", $row['EDR_StartDate']);
                    //$resultObject['CellId'] = $m . $j . $a;

                    $arrayDates = $this->getDays($row['EDR_StartDate'], $row['EDR_EndDate']);

                    if ($strd != "")
                        $strd .= "|";

                    $strd .= implode("|", $arrayDates);

                }
                $resultObject['URL'] = $detail_page . $date_stringURL . "/" . $result['EI_ValUrl'];
                $resultObject['DateComplete'] = $date_string;
                $resultObject['CellsIds'] = $strd;

                array_push($responseObject, $resultObject);
            }

            $this->getHelper('viewRenderer')->setNoRender();
            echo json_encode($responseObject);
        }
    }

    public function getDays($sStartDate, $sEndDate)
    {
        // Firstly, format the provided dates.
        // This function works best with YYYY-MM-DD
        // but other date formats will work thanks
        // to strtotime().
        $sStartDate = gmdate("Y-m-d", strtotime($sStartDate));
        $sEndDate = gmdate("Y-m-d", strtotime($sEndDate));

        // Start the variable off with the start date
        $aDays[] = gmdate("mdY", strtotime($sStartDate));

        // Set a 'temp' variable, sCurrentDate, with
        // the start date - before beginning the loop
        $sCurrentDate = $sStartDate;

        // While the current date is less than the end date
        while ($sCurrentDate < $sEndDate)
        {
            $tmp = $sCurrentDate;
            // Add a day to the current date
            $sCurrentDate = gmdate("Y-m-d", strtotime("+1 day", strtotime($tmp)));
            $sCurrentDateStr = gmdate("mdY", strtotime("+1 day", strtotime($tmp)));

            // Add this new day to the aDays array
            $aDays[] = $sCurrentDateStr;
        }

        // Once the loop has finished, return the
        // array of days.
        return $aDays;
    }

    private function _hasContent($obj, $id = null)
    {
        $data = array();
        $langs = Cible_FunctionsGeneral::getAllLanguage();
        $lang = $this->view->languageId;
        foreach ($langs as $lg)
        {
            if ($lg['L_ID'] != $lang)
            {
                $obj->setCurrentLang($lg['L_ID']);
                if (is_null($id))
                    $data = $obj->getList($obj->getBlockParam('2'));
                else
                    $data = $obj->getDetails($id);
            }
        }

        $nbValues = count($data);

        return $nbValues;
    }
}
