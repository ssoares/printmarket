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
 * Build and dispatch the messages for specific events.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: Screen.php 1846 2016-01-15 15:49:59Z ssoares $
 */
class Cible_Notifications_Screen extends Cible_Notifications
{
    protected $_data = array();
    protected $_pageRange = 5;
    protected $_itemPerPage = 10;
    protected $_renderView = "index/logNotification.phtml";

    public function setPageRange($pageRange)
    {
        $this->_pageRange = $pageRange;
    }

    public function setItemPerPage($itemPerPage)
    {
        $this->_itemPerPage = $itemPerPage;
    }

    public function __construct($options = null)
    {
        parent::__construct($options);

        $this->_view->language = Cible_FunctionsGeneral::getLanguageSuffix($this->_data['language']);
        $this->_view->headScript()->appendFile($this->_view->locateFile('log.js', null, 'back'));
        $this->_view->headLink()->appendStylesheet($this->_view->locateFile('log.css'));
        $this->_view->headScript()->appendFile($this->_view->locateFile('jquery.dataTables.min.js', 'datatable', 'back'));
        $this->_view->headScript()->appendFile($this->_view->locateFile('dataTables.fourButtonNavigation.js', 'datatable/plugins', 'back'));

        $method = '_' . $this->_event . ucfirst($this->_recipient);
        $this->$method();

    }

    private function _allLogAdmin()
    {
        $profileLogName = ucfirst($this->_data['module']) . 'Log';
        $oProfileLog = new $profileLogName(array('moduleId' => $this->_data['moduleId']));

        $results = $oProfileLog->getGlobalLog($this->_data);
        $this->_view->assign('logRenderData', $results);
        $this->_message = $this->_view->render('index/logNotification.phtml');
    }

    private function _searchLogClient()
    {
        $profileLogName = ucfirst($this->_data['module']) . 'Log';
        $oProfileLog = new $profileLogName(array('moduleId' => $this->_data['moduleId']));

        $results = $oProfileLog->getGlobalLog($this->_data);
        $adapter = new Zend_Paginator_Adapter_Array($results);
        $paginator = new Zend_Paginator($adapter);

        $paginator->setItemCountPerPage($this->_itemPerPage);
        $paginator->setCurrentPageNumber($this->_view->request->getParam('page'));
        $paginator->setPageRange($this->_pageRange);

        $this->_view->assign('logRenderData', $paginator);
        $this->_message = $this->_view->render($this->_renderView);
    }
}
