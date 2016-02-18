<?php

class Profile_IndexController extends Cible_Extranet_Controller_Module_Action
{
    protected $_separ = '||';

    public function indexAction()
    {
        $searchResult = Cible_FunctionsIndexation::indexationSearch('');
    }

    public function listAction()
    {

        $searchfor = $this->_request->getParam('searchfor');
        $filters = '';

        $profile = new GenericProfile();
        $member = new MemberProfile();
        $select = $member->getSelectStatement();
//        $oRetailer = new RetailersObject();
//        $selectRetailer = $oRetailer->getAll(null, false);
//        $selectRetailer->joinRight(
//            $profile->getGenericTable(),
//            'R_GenericProfileId = GP_MemberID',
//            array(
//                'lastName' => 'GP_LastName',
//                'firstName' => 'GP_FirstName',
//                'email' => 'GP_Email')
//        );
//
//        $select = $selectRetailer->joinRight(
//                $member->getTable(),
//                'GP_MemberID = MP_GenericProfileMemberID',
//                array(
//                    'member_id' => 'MP_GenericProfileMemberID',
//                    'MP_Status' => 'MP_Status')
//        );

        $tables = array(
            'GenericProfiles' => array('GP_LastName', 'GP_FirstName', 'GP_Email'),
        );

        $field_list = array(
            'lastName' => array('width' => '150px'),
            'firstName' => array('width' => '150px'),
            'email' => array('width' => '300px'),
//            'status'  => array('width' => '150px')
        );

        $options = array(
            'commands' => array(
                $this->view->link($this->view->url(array('controller' => 'index', 'action' => 'add')), $this->view->getCibleText('button_add_profile'), array('class' => 'action_submit add'))
            ),
            'disable-export-to-excel' => '',
            'filters' => array(
//                'filter_1' => array(
//                    'label' => 'Liste des détaillants',
//                    'default_value' => null,
//                    'associatedTo' => 'GP_MemberID',
//                    'equalTo' => 'R_GenericProfileId',
//                    'choices' => array(
//                        '' => "--> A",
//                        '1' => "--> Affichés sur le site"
//                    )
//                ),
                'filter_2' => array(
                    'label' => 'Liste des détaillants',
                    'default_value' => null,
                    'associatedTo' => 'MP_Status',
                    'choices' => array(
                        '' => 'Désactivé',
                        '0' => 'Email non validé',
                        '1' => 'À valider',
                        '2' => 'Activé'
                    )
                )
            ),
            'action_panel' => array(
                'width' => '50',
                'actions' => array(
                    'edit' => array(
                        'label' => $this->view->getCibleText('menu_submenu_action_edit'),
                        //'url' => "{$this->view->baseUrl()}/profile/index/edit/ID/%ID%",
                        'url' => $this->view->url(array('action' => 'edit', 'ID' => "-ID-")),
                        'findReplace' => array(
                            'search' => '-ID-',
                            'replace' => 'member_id'
                        )
                    ),
                    'delete' => array(
                        'label' => $this->view->getCibleText('menu_submenu_action_delete'),
                        //'url' => "{$this->view->baseUrl()}/profile/index/delete/ID/%ID%",
                        'url' => $this->view->url(array('action' => 'delete', 'ID' => '-ID-')),
                        'findReplace' => array(
                            'search' => '-ID-',
                            'replace' => 'member_id'
                        )
                    )
                )
            )
        );

        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
        $this->view->assign('mylist', $mylist);
    }

    public function addAction()
    {
        /*         * ********************************* */
        // variable
        $baseDir = $this->view->baseUrl();

        $returnModule = $this->_request->getParam('returnModule');
        $returnAction = $this->_request->getParam('returnAction');

        $this->view->assign('returnModule', $returnModule);
        $this->view->assign('returnAction', $returnAction);

        $config = Zend_Registry::get('config');
        $current_state = $config->address->default->states;
        $currentCity = '';

        $this->view->assign('selectedState', $current_state);

        if (!$this->_request->isPost() && !$this->_request->getParam('email'))
        {
            $form = new FormProfileVerification();
            $this->view->assign('form', $form);
            $this->renderScript('index/add-verification.phtml');
        }
        else if ($this->_request->isPost() && isset($_POST['email_verification']))
        {

            $formData = $this->_request->getPost();

            $form = new FormProfileVerification();
            $form->populate($formData);

            if (!$form->isValid($formData))
            {
                $this->view->assign('form', $form);
                $this->renderScript('index/add-verification.phtml');
                return;
            }

            $email = $form->getValue('email_verification');

            $profile = new GenericProfile();
            $member = $profile->findMembers(array('email' => $email));

            if (empty($member))
            {
                $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array('module' => 'profile', 'controller' => 'index', 'action' => 'add', 'email' => $email))));
            }
            else
            {
                $this->view->assign('member_id', $member[0]['member_id']);
                $this->view->assign('member_email', $email);
                $this->renderScript('index/add-email-found.phtml');
            }
        }
        else
        {

            if ($returnModule <> '' && $returnAction <> '')
                $cancelUrl = $this->view->url(array('module' => $returnModule, 'action' => $returnAction, 'returnModule' => null, 'returnAction' => null, 'email' => null));
            else
                $cancelUrl = $this->view->url(array('action' => 'list', 'email' => null));

            /*             * ********************************* */
            // variable
            $newsletterCategories = $this->view->GetAllNewsletterCategories();
            $newsletterCategories = $newsletterCategories->toArray();

            $this->view->assign('newsletterCategories', $newsletterCategories);

            /*             * ********************************* */
            // form
            $form = new FormProfile(array(
                    'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'langId' => $this->_defaultInterfaceLanguage
                ));

            if ($form->getSubForm('membersForm'))
            {
                $form->getSubForm('membersForm')->getElement("password")->setRequired(true);
                $form->getSubForm('membersForm')->getElement("password")->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => "Veuillez entrer un mot de passe")));
            }
//            if ($this->_request->isPost())
//            {
//                $statePost = '';
//                $cityPost = '';
//
//                foreach ($_POST as $subForm => $post)
//                {
//                    if (is_array($post) && array_key_exists('A_StateId', $post))
//                    {
//                        $state = $form->getsubForm($subForm)->getElement('A_StateId');
//                        $statePost .= $post['A_StateId'];
//                        $statePost .= ';';
//
//                        if (array_key_exists('A_CityId', $post))
//                        {
//                            $city = $form->getsubForm($subForm)->getElement('A_CityId');
//                            $cityPost .= $post['A_CityId'];
//                            $cityPost .= ';';
//                        }
//
//                        if (isset($post['A_CountryId']) && !empty($post['A_CountryId']))
//                        {
//                            $states = Cible_FunctionsGeneral::getStatesByCountry($post['A_CountryId']);
//                            foreach ($states as $_state)
//                                $state->addMultiOption($_state['ID'], $_state['Name']);
//                        }
//
//                        if (isset($post['A_StateId']) && !empty($post['A_StateId']))
//                        {
//                            $cities = Cible_FunctionsGeneral::getCities(null, null, $post['A_StateId']);
//                            foreach ($cities as $_city)
//                                $city->addMultiOption($_city['id'], $_city['name']);
//                        }
//                    }
//                }
//                $currentCity = (empty($cityPost)) ? substr($currentCity, 0, -1) : substr($cityPost, 0, -1);
//                $current_state = (empty($cstatePost)) ? substr($current_state, 0, -1) : substr($statePost, 0, -1);
//            }

            if ($this->_request->isPost())
            {
                $data = $this->_request->getPost();
                $currentCity  = $data['retailerForm']['A_CityId'];
                $current_state  = $data['membersForm']['addressFact']['A_StateId'] . $this->_separ;
                $current_state .= $data['membersForm']['addressShipping']['A_StateId'] . $this->_separ;
                $current_state .= $data['retailerForm']['A_StateId'] ;

        //            ksort($data);
        //            $tmpPostAddr   = $this->_statesCitiesList($data, $form);
        //            $currentCity   = substr($tmpPostAddr['currentCity'], 0, strlen($tmpPostAddr['currentCity'])-1);
        //            $current_state = substr($tmpPostAddr['currentState'], 0, strlen($tmpPostAddr['currentState'])-1);
            }
            $this->view->assign('selectedCity', $currentCity);
            $this->view->assign('selectedState', $current_state);

            if ($form->getSubForm('membersForm'))
            {
                $memberForm = $form->getSubForm('membersForm');
                $state = $memberForm->getElement('state');
            }
            $this->view->assign('form', $form);

            if ($this->_request->isPost())
            {

                $formData = $this->_request->getPost();
//                    if( $formData['membersForm']['isRetailer'] == 1 ) {
//                        $form->getSubForm('membersForm')->getElement("validatorID")->removeValidator('greaterThan');
//                    }
                if ($form->getSubForm('membersForm'))
                {
                    $oAddress = new AddressObject();
                    // Get the addresses data to insert
                    $addressFact       = $formData['membersForm']['addressFact'];
                    $addressShipping   = $formData['membersForm']['addressShipping'];
                    $addressRetailer   = $formData['retailerForm'];
                    $addressRetailerEn = $formData['retailerFormEn'];
                    //Remove data form form if the shipping address is the same as bill
                    if ($addressShipping['duplicate'] == 1)
                    {
                        $subFormShip = $form->getSubForm('addressShipping');
                        foreach ($subFormShip as $key => $value)
                        {
                            $value->clearValidators()->setRequired(false);
                        }
                        unset($formData['addressShipping']);
                    }
                    //If customer doesn't want to add data on website, set to false the field name
                    if ($addressRetailer['isDistributeur'] == 1)
                        $form->getSubForm('retailerForm')->getElement('AI_Name')->clearValidators()->setRequired(false);

                    $form->removeSubForm('addressShipping');
                }

                if ($form->isValid($formData))
                {
                    $memberID = '';
                    //remove addresses
                    unset($formData['addressFact'], $formData['addressShipping'], $formData['addressDetaillant']);

                    if (array_key_exists('genericForm', $formData))
                    {
                        $genericForm = $formData['genericForm'];
                        $genericProfil = new GenericProfile();

                        $members = $genericProfil->findMembers(array('email' => $genericForm['email']));

                        if (count($members) == 0)
                        {
                            $memberID = $genericProfil->addMember($genericForm);
                        }
                        else
                        {
                            $form->getSubForm('genericForm')->getElement('email')->addError($this->view->getCibleText('validation_message_used_email'));
                            $cancelUrl = '';
                        }
                    }

                    if (array_key_exists('newsletterForm', $formData))
                    {
                        $newsletterForm = $formData['newsletterForm'];
                        $newsletterProfile = new NewsletterProfile();

                        $cat = '';
                        foreach ($newsletterForm as $item => $val)
                        {
                            if ($val == 1)
                            {
                                if ($cat == '')
                                    $cat = str_replace('chkNewsletter', '', $item);
                                else
                                    $cat .= "," . str_replace('chkNewsletter', '', $item);
                            }
                        }

                        if ($cat <> '')
                            $newsletterProfile->updateMember($memberID, array('newsletter_categories' => $cat));
                    }
                    //get the last data to merge in the billing address
                    if ($form->getSubForm('membersForm'))
                    {
                        $addressFact['A_Fax']        = $formData['membersForm']['A_Fax'];
                        $addressFact['AI_FirstTel']  = $formData['membersForm']['AI_FirstTel'];
                        $addressFact['AI_SecondTel'] = $formData['membersForm']['AI_SecondTel'];
                        $addressFact['AI_WebSite']   = $formData['membersForm']['AI_WebSite'];

                        $langId = $this->_defaultInterfaceLanguage;
                        $idBillAddr = $oAddress->insert($addressFact, $langId);

                        if ($addressShipping['duplicate'] == 1)
                        {
                            $addressFact['A_Duplicate'] = $idBillAddr;
                            $idShipAddr = $oAddress->insert($addressFact, $langId);
                        }
                        else
                        {
                            $addressShipping['A_Duplicate'] = 0;
                            $idShipAddr = $oAddress->insert($addressShipping, $langId);
                        }

                        if ($addressRetailer['isDistributeur'] == 2)
                        {
                            $idRetailAddr = $oAddress->insert($addressRetailer, 1);
                            $oAddress->save($idRetailAddr, $addressRetailerEn, 2);
                            $retailerData = array(
                                'R_GenericProfileId' => $memberID,
                                'R_AddressId' => $idRetailAddr,
                                'R_Status' => $addressRetailer['isDistributeur']
                            );

                            $oRetailer->insert($retailerData, $langId);
                        }
                    }

                    if (array_key_exists('membersForm', $formData))
                    {
                        $membersForm = $formData['membersForm'];
                        $membersForm['addrBill'] = $idBillAddr;
                        $membersForm['addrShip'] = $idShipAddr;

                        $memberProfil = new MemberProfile();

                        if ($membersForm['status'] < 1)
                            $membersForm['validatedEmail'] = Cible_FunctionsGeneral::generatePassword();

                        if (isset($membersForm['password']))
                            $membersForm['password'] = md5($membersForm['password']);

                        foreach ($membersForm as $key => $val)
                        {
                            $memberProfil->updateMember($memberID, $membersForm);
                        }

                        if ($returnModule <> '' && $returnAction <> '')
                            $cancelUrl = $this->view->url(array('module' => $returnModule, 'action' => $returnAction, 'returnModule' => null, 'returnAction' => null, 'order' => 'lastName', 'order-direction' => 'ASC', 'page' => null));
                        else
                            $cancelUrl = $this->view->url(array('action' => 'list', 'returnModule' => null, 'returnAction' => null, 'order' => 'lastName', 'order-direction' => 'ASC', 'page' => null));
                    }


                    if ($cancelUrl <> '')
                        $this->_redirect(str_replace($baseDir, '', $cancelUrl));
                }

//                    else {
//                        if( $form->getSubForm('membersForm')->getElement("isRetailer")->isChecked() ) {
//                            $this->view->headStyle()->appendStyle('dd.validatedBy { display: none }');
//                        }
//                    }
            } else
            {
                $email = $this->_request->getParam('email');

                $form->populate(array('email' => $email));
            }
        }
    }

    public function editAction()
    {
        /*         * ********************************* */
        // variable
        $webAddr = array();
        $addressFields = array();
        $memberID = $this->_request->getParam('ID');
        $baseDir = $this->view->baseUrl();
        $billAddr = array();
        $shipAddr = array();
        $current_state = '';
        $currentCity = '';

        $returnModule = $this->_request->getParam('returnModule');
        $returnAction = $this->_request->getParam('returnAction');
        if ($returnModule <> '' && $returnAction <> '')
            $cancelUrl = $this->view->url(array('module' => $returnModule, 'action' => $returnAction, 'ID' => null, 'returnModule' => null, 'returnAction' => null));
        else
            $cancelUrl = $this->view->url(array('action' => 'list', 'ID' => null));

        $this->view->assign('memberID', $memberID);
        /*         * ********************************* */
        // newsletter categories
        $newsletterProfil = new NewsletterProfile();
        $newsletterMemberDetails = $newsletterProfil->getMemberDetails($memberID);
        //$this->view->assign('newsletterMemberDetails',$newsletterMemberDetails);

        $newsletterCategories = $this->view->GetAllNewsletterCategories();
        $newsletterCategories = $newsletterCategories->toArray();

        if (is_array($newsletterMemberDetails) && array_key_exists('newsletter_categories', $newsletterMemberDetails) && $newsletterMemberDetails['newsletter_categories'] <> "")
        {
            $memberNewsletterCategories = $newsletterMemberDetails['newsletter_categories'];
            $memberNewsletterCategories = explode(',', $memberNewsletterCategories);

            $memberCat = array();
            foreach ($memberNewsletterCategories as $memberCategory)
            {
                $memberCat["chkNewsletter{$memberCategory}"] = 1;
            }
        }
        else
            $memberCat = array();

        $this->view->assign('memberNewsletterCategories', $memberCat);
        $this->view->assign('newsletterCategories', $newsletterCategories);

        // Form
        $form = new FormProfile(array(
                'baseDir' => $baseDir,
                'cancelUrl' => $cancelUrl,
                'langId' => $this->_defaultInterfaceLanguage,
                'mode' => 'edit'
            ));

        $this->view->assign('form', $form);

        $genericProfil = new GenericProfile();
        $genericMemberDetails = $genericProfil->getMemberDetails($memberID);
        $this->view->assign('genericMemberDetails', $genericMemberDetails);

        $membersProfil = new MemberProfile();
        $lang = $this->_defaultInterfaceLanguage;
        $membersDetails = $membersProfil->getMemberDetails($memberID);
        //Retailers data
        if ($form->getSubForm('membersForm'))
        {
//            $oRetailers = new RetailersObject();
            $oAddress = new AddressObject();

//            $retailerData = $oRetailers->getAll(null, true, $memberID);
//            if (count($retailerData))
//                $retailerAddr = $oAddress->getAll(null, true, $retailerData[0]['R_AddressId']);

            if (!empty($membersDetails['addrBill']))
                $billAddr = $oAddress->populate($membersDetails['addrBill'], $lang);
            if (!empty($membersDetails['addrShip']))
                $shipAddr = $oAddress->populate($membersDetails['addrShip'], $lang);
//            $oRetailer = new RetailersObject();
//            $onWeb = $oRetailer->getRetailerInfos($membersDetails['member_id'], $lang);

//            if ($onWeb && !empty($onWeb['R_AddressId']))
//            {
//                $webAddrFr = $oAddress->populate($onWeb['R_AddressId'], 1);
//                $webAddrEn = $oAddress->populate($onWeb['R_AddressId'], 2);
//                $webAddr['isDistributeur-1'] = $onWeb['R_Status'];
//                $form->getSubForm('retailerForm')->getElement('isDistributeur')->setValue($onWeb['R_Status']);
//            }

            if (!empty($billAddr))
            {
                $membersDetails['addressFact'] = $billAddr;
//                $membersDetails['AI_FirstTel'] = $billAddr['AI_FirstTel'];
//                $membersDetails['AI_SecondTel'] = $billAddr['AI_SecondTel'];
//                $membersDetails['A_Fax'] = $billAddr['A_Fax'];
            }

            if (isset($shipAddr['A_Duplicate']) && !$shipAddr['A_Duplicate'])
                $shipAddr['duplicate'] = 0;
            if (!empty($shipAddr))
            $membersDetails['addressShipping'] = $shipAddr;
//            $membersDetails['retailerForm']    = $webAddrFr;
//            $membersDetails['retailerFormEn']  = $webAddrEn;

            $memberForm = $form->getSubForm('membersForm');
            $stateBill = $memberForm->getSubForm('addressFact')->getElement('A_StateId');
            $stateShip = $memberForm->getSubForm('addressShipping')->getElement('A_StateId');

            $countries = Cible_FunctionsGeneral::getCountries();

            if (count($webAddr) > 0)
            {
                $membersDetails['AI_WebSite'] = $billAddr['AI_WebSite'];
                $current_state = $billAddr['A_StateId'] . $this->_separ . $shipAddr['A_StateId'] . $this->_separ . $webAddrFr['A_StateId'] . $this->_separ;
                $currentCity = $billAddr['A_CityId'] . $this->_separ . $shipAddr['A_CityId'] . $this->_separ . $webAddrFr['A_CityId'] . $this->_separ;
            }
            elseif (!empty($billAddr) && !empty($shipAddr))
            {
                $current_state = $billAddr['A_StateId'] . $this->_separ . $shipAddr['A_StateId'] . $this->_separ;
                $currentCity = $billAddr['A_CityId'] . $this->_separ . $shipAddr['A_CityId'] . $this->_separ;
            }
        }

        if ($this->_request->isPost())
        {
            $data = $this->_request->getPost();
            $currentCity  = $data['retailerForm']['A_CityId'];
            $current_state  = $data['membersForm']['addressFact']['A_StateId'] . $this->_separ;
            $current_state .= $data['membersForm']['addressShipping']['A_StateId'] . $this->_separ;
            $current_state .= $data['retailerForm']['A_StateId'] ;

//            ksort($data);
//            $tmpPostAddr   = $this->_statesCitiesList($data, $form);
//            $currentCity   = substr($tmpPostAddr['currentCity'], 0, strlen($tmpPostAddr['currentCity'])-1);
//            $current_state = substr($tmpPostAddr['currentState'], 0, strlen($tmpPostAddr['currentState'])-1);
                    }

        $this->view->assign('selectedCity', $currentCity);
        $this->view->assign('selectedState', $current_state);

        $addressFields = array_unique($addressFields);

//            if( $memberForm->getElement("isRetailer")->isChecked() ) {
//                $this->view->headStyle()->appendStyle('dd.validatedBy { display: none }');
//            }

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($form->getSubForm('membersForm'))
            {
                $oAddress = new AddressObject();
                // Get the addresses data to insert
                $addressFact       = $formData['membersForm']['addressFact'];
                $addressShipping   = $formData['membersForm']['addressShipping'];
//                $addressRetailer   = $formData['retailerForm'];
//                $addressRetailerEn = $formData['retailerFormEn'];
                //Remove data form form if the shipping address is the same as bill
                if ($addressShipping['duplicate'] == 1)
                {
                    $subFormShip = $form->getSubForm('addressShipping');
                    foreach ($subFormShip as $key => $value)
                    {
                        $value->clearValidators()->setRequired(false);
                    }
                    unset($formData['addressShipping']);
                }
                //If customer doesn't want to add data on website, set to false the field name
//                if ($addressRetailer['isDistributeur'] == 1)
//                {
//                    $form->getSubForm('retailerForm')->getElement('AI_Name')->clearValidators()->setRequired(false);
//                    $form->getSubForm('retailerFormEn')->getElement('AI_Name')->clearValidators()->setRequired(false);
//                }
//                $form->removeSubForm('addressShipping');
                }
            if ($form->isValid($formData))
            {
                if (array_key_exists('genericForm', $formData))
                {
                    $genericForm = $formData['genericForm'];
                    $genericProfil = new GenericProfile();
                    $langId = $genericForm['language'];
                    $members = $genericProfil->findMembers(array('email' => $genericForm['email']));

                    if ((count($members) == 1 and $members[0]['member_id'] == $memberID) or (count($members) == 0))
                    {
                        $genericProfil->updateMember($memberID, $genericForm);
                    }
                    else
                    {
                        $form->getSubForm('genericForm')->getElement('email')->addError($this->view->getCibleText('validation_message_used_email'));
                        $cancelUrl = '';
                    }
                }

                if (array_key_exists('newsletterForm', $formData))
                {
                    $newsletterForm = $formData['newsletterForm'];
                    $newsletterProfile = new NewsletterProfile();

                    $cat = "";
                    foreach ($newsletterForm as $item => $val)
                    {
                        if ($val == 1)
                        {
                            if ($cat == "")
                                $cat = str_replace('chkNewsletter', '', $item);
                            else
                                $cat .= "," . str_replace('chkNewsletter', '', $item);
                        }
                    }

                    $newsletterProfile->updateMember($memberID, array('newsletter_categories' => $cat));
                }

                if (array_key_exists('membersForm', $formData))
                {
                    $membersForm = $formData['membersForm'];
                    $langId = $this->_defaultInterfaceLanguage;
                    if (!empty($membersForm['password']))
                        $membersForm['password'] = md5($membersForm['password']);
                    else
                        $membersForm['password'] = $membersDetails['password'];

                    $memberProfil = new MemberProfile();

                    //get the last data to merge in the billing address

//                    $addressFact['A_Fax'] = $membersForm['A_Fax'];
//                    $addressFact['AI_FirstTel'] = $membersForm['AI_FirstTel'];
//                    $addressFact['AI_SecondTel'] = $membersForm['AI_SecondTel'];
//                    $addressFact['AI_WebSite'] = $membersForm['AI_WebSite'];
                    if (empty($billAddr['A_AddressId']))
                    {
                        $billAddr['A_AddressId'] = $oAddress->insert ($addressFact, $langId);
                        $membersForm['addressFact']['addrBill'] = $billAddr['A_AddressId'];
                        $membersForm['addrBill'] = $billAddr['A_AddressId'];
                    }
                    else
                    $oAddress->save($billAddr['A_AddressId'], $addressFact, $langId);

                    if ($addressShipping['duplicate'] == 1)
                    {
                        $addressFact['A_Duplicate'] = $billAddr['A_AddressId'];
                        if (empty ($shipAddr['A_AddressId']))
                        {
                            $shipAddrId = $oAddress->insert($addressFact, $langId);
                            $membersForm['addressShipping']['addrShip'] = $shipAddrId;
                            $membersForm['addrShip'] = $shipAddrId;
                        }
                        else
                        $oAddress->save($shipAddr['A_AddressId'], $addressFact, $langId);
                    }
                    else
                    {
                        $addressShipping['A_Duplicate'] = 0;
                        if (empty ($shipAddr['A_AddressId']))
                        {
                            $shipAddrId = $oAddress->insert($addressShipping, $langId);
                            $membersForm['addressShipping']['addrShip'] = $shipAddrId;
                            $membersForm['addrShip'] = $shipAddrId;
                        }
                        else
                        $oAddress->save($shipAddr['A_AddressId'], $addressShipping, $langId);
                    }

                    $memberProfil->updateMember($memberID, $membersForm);
                    //If customer doesn't want to add data on website, set to false the field name
//                    switch ($addressRetailer['isDistributeur'])
//                    {
//                        case 1:
//                            if (!empty($onWeb))
//                            {
//                                $retailerData = array(
//                                    'R_Status' => $addressRetailer['isDistributeur']);
//                                $oRetailer->save($onWeb['R_ID'], $retailerData, $langId);
//                            }
//                            break;
//                        case 2:
//                            if (!empty($webAddrFr))
//                            {
//                                $retailerData = array(
//                                    'R_Status' => $addressRetailer['isDistributeur']);
//                                $oAddress->save($webAddrFr['A_AddressId'], $addressRetailer, 1);
//                                $oAddress->save($webAddrFr['A_AddressId'], $addressRetailerEn, 2);
//                                $oRetailer->save($onWeb['R_ID'], $retailerData, $langId);
//                            }
//                            else
//                            {
//                                $addressId = $oAddress->insert($addressRetailer, 1);
//                                $oAddress->save($addressId, $addressRetailerEn, 2);
//                                $retailerData = array(
//                                    'R_GenericProfileId' => $memberID,
//                                    'R_AddressId' => $addressId,
//                                    'R_Status' => $addressRetailer['isDistributeur']
//                                );
//                                $oRetailer->insert($retailerData, $langId);
//                            }
//                            break;
//                        default:
//                            break;
//                    }
                            }

                if ($cancelUrl <> '')
                    $this->_redirect(str_replace($baseDir, '', $cancelUrl));
            }
        }
        else
        {

            $this->view->assign('membersDetails', $membersDetails);
            if ($genericMemberDetails)
                $form->populate($genericMemberDetails);
            if ($newsletterMemberDetails)
                $form->populate($newsletterMemberDetails);
            if ($membersDetails)
                $form->populate($membersDetails);
            if ($memberCat)
                $form->populate($memberCat);

//                if( $form->getSubForm('membersForm')->getElement("isRetailer")->isChecked() ) {
//                    $this->view->headStyle()->appendStyle('dd.validatedBy { display: none }');
//                }

            $this->view->assign('form', $form);
        }
    }

    public function deleteAction()
    {
        /*         * ********************************* */
        // variables
        $memberID = $this->_request->getParam('ID');
        $baseDir = $this->view->baseUrl();

        $returnModule = $this->_request->getParam('returnModule');
        $returnAction = $this->_request->getParam('returnAction');
        if ($returnModule <> '' && $returnAction <> '')
            $cancelUrl = $this->view->url(array('module' => $returnModule, 'action' => $returnAction, 'ID' => null, 'returnModule' => null, 'returnAction' => null));
        else
            $cancelUrl = $this->view->url(array('action' => 'list'));

        $genericProfil = new GenericProfile();
        $memberProfil  = new MemberProfile();
        $memberData    = $memberProfil->getMemberDetails($memberID);
        $genericMemberDetails = $genericProfil->getMemberDetails($memberID);


        $this->view->assign('genericMemberDetails', $genericMemberDetails);

        if ($this->_request->isPost())
        {
            $del = $this->_request->getPost('delete');
            if ($del)
            {
                $profile = new GenericProfile();
                $profile->deleteMember($memberID);
//                $retailer = new RetailersObject();
//                $retailer->deleteMember($memberID);
//                $address  = new AddressObject();
//                $address->delete($memberData['addrBill']);
//                $address->delete($memberData['addrShip']);

                if ($returnModule <> '' && $returnAction <> '')
                    $cancelUrl = $this->view->url(array('module' => $returnModule, 'action' => $returnAction, 'returnModule' => null, 'returnAction' => null, 'order' => 'lastName', 'order-direction' => 'ASC', 'page' => null));
                else
                    $cancelUrl = $this->view->url(array('action' => 'list', 'returnModule' => null, 'returnAction' => null, 'order' => 'lastName', 'order-direction' => 'ASC', 'page' => null));
            }
            $this->_redirect(str_replace($baseDir, '', $cancelUrl));
        }
    }

    public function toExcelAction()
    {
        $this->filename = 'Profil.xlsx';

//        $this->type = 'Excel5';
//        $this->type = 'CSV';

        $searchfor = $this->_request->getParam('searchfor');

        $profile = new GenericProfile();

        $this->select = $profile->getSelectStatement();

        $this->tables = array(
            'GenericProfiles' => array('GP_LastName', 'GP_FirstName', 'GP_Email')
        );


        $this->fields = array(
            'lastName' => array('width' => '', 'label' => ''),
            'firstName' => array('width' => '', 'label' => ''),
            'email' => array('width' => '', 'label' => '')
        );

        $this->filters = array(
        );

        parent::toExcelAction();
    }

    public function ajaxAction()
    {
        /*         * ********************************* */
        // variables
        $action = $this->_getParam('actionAjax');

        /*         * ********************************* */
        // Dissociate a person of a newsletter
        if ($action == 'dissociateMemberNewsletter')
        {
            $memberID = $this->_getParam('memberID');
            $newsletterCategoryID = $this->_getParam('newsletterCategoryID');
            $memberNewsletterCategories = $this->_getParam('memberNewslettersCat');

            $memberNewsletterCategories = explode(',', $memberNewsletterCategories);
            $newsletterCategories = $this->view->GetAllNewsletterCategories();
            $newsletterCategories = $newsletterCategories->toArray();

            foreach ($memberNewsletterCategories as $memberCategory)
            {
                $i = 0;
                foreach ($newsletterCategories as $category)
                {
                    if ($memberCategory == $category['CI_CategoryID'])
                    {
                        array_splice($newsletterCategories, $i, 1);
                    }
                    $i++;
                }
            }

            echo json_encode($newsletterCategories);
        }
        elseif ($action == 'associateMemberNewsletter')
        {
            $memberID = $this->_getParam('memberID');
            $newsletterCategoryID = $this->_getParam('newsletterCategoryID');
            $memberNewslettersCat = $this->_getParam('memberNewslettersCat');

            $categorySelect = new CategoriesIndex();
            $select = $categorySelect->select()
                ->where("CI_CategoryID IN ($memberNewslettersCat)")
                ->where('CI_LanguageID = ?', Zend_Registry::get('languageID'))
                ->order('CI_Title');

            $categoryData = $categorySelect->fetchAll($select);

            //$this->view->dump($categoryData->toArray());

            echo json_encode($categoryData->toArray());
        }






        $this->getHelper('viewRenderer')->setNoRender();
    }

    /**
     * Transforms data of the posted form in one array
     *
     * @param array $formData Data to save.
     *
     * @return array
     */
    protected function _mergeFormData(array $formData)
    {
        (array) $tmpArray = array();

        foreach ($formData as $key => $data)
        {
            if (is_array($data))
            {
                $tmpArray = array_merge($tmpArray, $data);
            }
            else
                $tmpArray[$key] = $data;
        }

        return $tmpArray;
    }

}