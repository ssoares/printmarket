<?php

class Forms_IndexController extends Cible_Controller_Action {

    protected $_moduleID = 11;

    public function init() {
//        $this->_isSecured = false;
        parent::init();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('contact-form.css'), 'all');
    }

    public function formscontactAction() {
        $blockParamEmail = Cible_FunctionsBlocks::getBlockParameter($this->_getParam('BlockID'), '1');
        if (isset($blockParamEmail))
            $mailTo = $blockParamEmail;
        $info = (bool)$this->_config->info->alternative ? 'alt' : 'info';
        $this->view->info = $this->_config->$info->toArray();
        $obj = new ContactObject();
        $options = array('action' => $this->_request->getRequestUri(),
            'disabledFieldsStatus' => true,
            'object' => $obj
            );
        $form = new FormContact($options);
        $this->view->assign('form', $form);
        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            if (array_key_exists('submit', $formData)) {
                if ($form->isValid($formData)) {
                    $formData = $form->getValues();
                    // send the mail
                    $data = array(
                        'firstName' => $formData['CD_Surname'],
                        'lastName' => $formData['CD_Name'],
                        'email' => $formData['CD_Email'],
                        'comments' => $formData['CD_Comments'],
                        'language' => Zend_Registry::get('languageID'),
                    );
                    $options = array(
                        'send' => true,
                        'isHtml' => true,
                        'moduleId' => $this->_moduleID,
                        'event' => 'contact',
                        'type' => 'email',
                        'recipient' => 'admin',
                        'data' => $data
                    );
                    if (!empty($mailTo)){
                        $options['to'] = $mailTo;
                    }

                    if (!empty($formData['CD_File'])){
                        $options['attachment'] = array('/' .$formData['CD_File']);
                    }
                    if (!empty($formData['CD_Subject'])){
                        $options['title'] = $formData['CD_Subject'];
                    }
                    new Cible_Notifications_Email($options);
                    $obj->insert($formData, $this->view->languageId);
                    $oGP = new GenericProfilesObject();
                    $oGP->saveFromContact($data);
                    $this->view->assign('inscriptionValidate', true);
                }
            }
        }
    }

    public function thankYouAction() {

    }

    public function formsnewsletterfooterAction() {
        $subscribeUrl = $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView(2, 'subscribe', 8, $this->view->languageId, false);

        //$form = new FormNewsletterFooter(array('action' => $this->_request->getRequestUri(), 'disabledFieldsStatus' => true));
        $form = new FormNewsletterFooter(array('action' => $subscribeUrl, 'disabledFieldsStatus' => true));
        $this->view->assign('form', $form);

        /*
        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();

            if (array_key_exists('newslettersubmit', $formData)) {
                if ($form->isValid($formData)) {
                    // send the mail
                    $data = array(
                        'name' => '',//$formData['name'],
                        'email' => $formData['newsletteremail'],
                        'language' => Zend_Registry::get('languageID'),
                    );
                    $options = array(
                        'send' => true,
                        'isHtml' => true,
                        'moduleId' => $this->_moduleID,
                        'event' => 'newslettersignup',
                        'type' => 'email',
                        'recipient' => 'admin',
                        'data' => $data
                    );
                    if (!empty($mailTo))
                        $options['to'] = $mailTo;

                    $oNotification = new Cible_Notifications_Email($options);

                    $this->view->assign('inscriptionValidate', true);
                }
            }
        }*/
    }

}
