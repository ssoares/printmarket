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
 * @version   $Id: Email.php 1846 2016-01-15 15:49:59Z ssoares $
 */
class Cible_Notifications_Email extends Cible_Notifications
{
    const NEWACCOUNT = 'newAccount';
    const EDITRESEND = 'editResend';
    const EDITACCOUNT = 'editAccount';
    const WELCOME = 'welcome';
    const NEWORDER = 'newOrder';
    const CONFIRMORDER = 'confirmOrder';
    const REJECTORDER = 'rejectOrder';
    const NEWPWD = 'newPassword';
    const CONTACT = 'contact';
    protected $_renderView = "index/emailNotification.phtml";

    protected $_emailRenderData = array();
    protected $_data = array();

    public function __construct($options = array())
    {
        parent::__construct($options);
        if ($this->_isActive)
        {
            if (isset ($options['isHtml'])){
            $this->_isHtml = $options['isHtml'];
            }
            if (isset ($options['to'])){
                $this->addTo($options['to']);
            }
            if (empty($this->_to) && $this->_recipient == 'admin'){
                $this->addTo($this->_from);
            }elseif (empty($this->_to)
                && $this->_recipient == 'client'
                && !empty($this->_data['email']) ){
                $this->addTo($this->_data['email']);
            }
            $cfg = Zend_Registry::get('config');
            $logoPath = rtrim(Zend_Registry::get('absolute_web_root'), '/') .
                $this->_view->locateFile($cfg->clientLogo->src,
                    '/'.$this->_view->locale, 'front');
            $this->_emailRenderData['emailHeader'] = $this->_view->image($logoPath);

            $footer = $this->_view->getClientText("email_notification_footer", $this->_data['language']);
            $this->_emailRenderData['footer'] = str_replace('##SITE-NAME##', $this->_siteName, $footer);

            $this->setMethod();
            $method = $this->_method;
            if (in_array($method,get_class_methods($this))){
                $this->$method();
            }else{
                $this->standardData ();
            }
            $this->_view->assign('emailRenderData', $this->_emailRenderData);
            $this->_message = $this->_view->render($this->_renderView);
            if ($options['send']){
                $this->send();
            }
        }

    }
    /**
     * Set parameters and send an email.
     *
     * @param type $options Parameters to build the email
     *
     * @return void
     */
    public function process ($options = null)
    {
        self::__construct($options);
    }

    private function _newAccountClient()
    {
        $confirm_page = Zend_Registry::get('absolute_web_root') . "/"
                . Cible_FunctionsCategories::getPagePerCategoryView(
                        0,
                        'confirm_email',
                        $this->_moduleId,
                        $this->_data['language'])
                . "/email/{$this->_data['email']}/validateNumber/{$this->_data['validatedEmail']}";

        $this->_message = str_replace('##validated_email_link##', $confirm_page, $this->_message);
        $this->standardData();
    }

    private function _newAccountAdmin()
    {
        $siteDomain = rtrim(Zend_Registry::get('absolute_web_root'), '/');
        $this->_message = str_replace('##siteDomain##', $siteDomain, $this->_message);

        foreach ($this->_data as $key => $value)
        {
            $search = '##' . $key . '##';
            $this->_message = str_replace($search, $value, $this->_message);
        }

        $this->_emailRenderData['message'] = $this->_message;
    }
    private function _editResendClient()
    {
        $this->_newAccountClient();
    }

    private function _editAccountAdmin()
    {
        $states = Cible_FunctionsGeneral::getStatesByCountry($addressFact['A_CountryId']);
        foreach ($states as $value)
            $tmpStates[$value['ID']] = $value['Name'];

        $this->_view->assign('data', $this->_data['notifyAdmin']);
        $this->_view->assign('form', $this->_data['form']);
        $this->_view->assign('states', $tmpStates);
        $changesList = $this->_view->render('index/changesList.phtml');
        $this->_message = str_replace('##TABLE##', $changesList, $this->_message);

        $this->standardData();
    }

    private function _welcomeClient()
    {

    }
    private function _newOrderClient()
    {

    }
    private function _newOrderAdmin()
    {

    }
    private function _confirmOrderClient()
    {

    }
    private function _rejectOrderClient()
    {

    }
    private function _newPasswordClient()
    {
        $this->standardData();
    }
    private function _contactAdmin()
    {
        $this->_message = str_replace('##siteDomain##', $this->_siteName, $this->_message);

        foreach ($this->_data as $key => $value)
        {
            $search = '##' . $key . '##';
            $this->_message = str_replace($search, $value, $this->_message);
        }

        $this->_emailRenderData['message'] = $this->_message;
    }
    private function _sendListClient()
    {
        $this->standardData();
    }

    public function standardData()
    {
        $this->_message = str_replace('##siteDomain##', $this->_siteName, $this->_message);
        $this->_message = str_replace('##siteName##', $this->_siteName, $this->_message);

        foreach ($this->_data as $key => $value)
        {
            $search = '##' . $key . '##';
            $this->_message = str_replace($search, $value, $this->_message);
        }

        $this->_emailRenderData['message'] = $this->_message;
    }
}
