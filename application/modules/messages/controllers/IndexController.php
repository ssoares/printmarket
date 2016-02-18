<?php
/**
 * Cible Solutions - Vêtements SP
 * Messages management.
 *
 * @category  Modules
 * @package   Messages
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: IndexController.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Messages controller.
 * Manage actions for messages.
 *
 * @category  Modules
 * @package   Messages
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class Messages_IndexController extends Cible_Controller_Action
{
    protected $_moduleID      = 21;
    protected $_defaultAction = 'index';

    public function init()
    {
        parent::init();
        $this->view->headScript()->appendFile($this->view->locateFile('modalWindow.js'));
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('messages.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('messages.css'), 'all');
    }
    public function indexAction()
    {
        $oData = new MessagesCollection($this->view->params);
        $data = $oData->getList();

        $this->view->display = true;
        $this->view->popupWidth = $oData->getBlockParam(2);

        if (!empty($_COOKIE['messages']))
        {
            $this->view->display = false;
        }
        elseif ($data['MA_Online'] > 0)
        {
            $path = Zend_Registry::get('web_root') . '/';
            setcookie('messages', time(), time() + (60 * 60 * $data['MA_Timeout']), $path);
            $this->view->assign('message', $data);

        }
        else
            $this->view->display = false;
    }

}