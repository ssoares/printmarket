<?php
    
    class FormOrder extends Cible_Form
    {
        protected $_mode = 'edit';

        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            parent::__construct($options);
            $baseDir = $this->getView()->baseUrl();

            if( !empty($options['mode']) && $options['mode'] == 'edit')
                $this->_mode = 'edit';
            else
                $this->_mode = 'add';

            $langId = Zend_Registry::get('languageID');
            $this->setAttrib('id', 'accountManagement');
            $this->setAttrib('class', 'step3');

//            $addressParams = array(
//                "fieldsValue" => array(),
//                "display"   => array(),
//                "required" => array(),
//            );

             //Hidden fields for the state and cities id
            $selectedState = new Zend_Form_Element_Hidden('selectedState');
            $selectedState->removeDecorator('label');
            $selectedCity = new Zend_Form_Element_Hidden('selectedCity');
            $selectedCity->removeDecorator('label');

            $this->addElement($selectedState);
            $this->addElement($selectedCity);

            // Salutation
            $salutation = new Zend_Form_Element_Select('salutation');
            $salutation->setLabel($this->getView()->getCibleText('form_label_salutation'))
                ->setAttrib('class','smallTextInput')
                ->setOrder(1);

            $greetings = $this->getView()->getAllSalutation();
            foreach ($greetings as $greeting){
                $salutation->addMultiOption($greeting['S_ID'], $greeting['ST_Value']);
            }

            // language hidden field
            $language = new Zend_Form_Element_Hidden('language', array('value' => $langId));
            $language->removeDecorator('label');
            // langauge hidden field

            // FirstName
            $firstname = new Zend_Form_Element_Text('firstName');
            $firstname->setLabel($this->getView()->getCibleText('form_label_fName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('class'=>'stdTextInput'))
                ->setOrder(2);

            // LastName
            $lastname = new Zend_Form_Element_Text('lastName');
            $lastname->setLabel($this->getView()->getCibleText('form_label_lName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('class'=>'stdTextInput'))
                ->setOrder(3);

            // email
            $regexValidate = new Cible_Validate_Email();
            $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

            $emailNotFoundInDBValidator = new Zend_Validate_Db_NoRecordExists('GenericProfiles','GP_Email');
            $emailNotFoundInDBValidator->setMessage($this->getView()->getClientText('validation_message_email_already_exists'), 'recordFound');

            $email = new Zend_Form_Element_Text('email');
            $email->setLabel($this->getView()->getCibleText('form_label_email'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->addValidator($regexValidate)
                ->setAttribs(array('maxlength'=> 50, 'class'=>'stdTextInput'))
                ->setOrder(4);

            if( $this->_mode == 'add')
                $email->addValidator($emailNotFoundInDBValidator);
            // email

            // password
            $password = new Zend_Form_Element_Password('password');
            if( $this->_mode == 'add')
                $password->setLabel($this->getView()->getCibleText('form_label_password'));
            else
                $password->setLabel($this->getView()->getCibleText('form_label_newPwd'));

            $password->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class','stdTextInput')
                ->setRequired(true)
                ->setOrder(5)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
            // password

            // password confirmation
            $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
            if( $this->_mode == 'add')
                $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmPwd'));
            else
                $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'));

            $passwordConfirmation->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setRequired(true)
                ->setOrder(6)
                ->setAttrib('class','stdTextInput');

            if (!empty($_POST['identification']['password']))
            {
                $passwordConfirmation->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));

                $Identical = new Zend_Validate_Identical($_POST['identification']['password']);
                $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
                $passwordConfirmation->addValidator($Identical);
            }
            // password confirmation

            // Company name
            $company = new Zend_Form_Element_Text('company');
            $company->setLabel($this->getView()->getCibleText('form_label_company'))
                ->setRequired(false)
                ->setOrder(7)
                ->setAttribs(array('class'=>'stdTextInput'));

            // function in company
            $functionCompany = new Zend_Form_Element_Text('functionCompany');
            $functionCompany->setLabel($this->getView()->getCibleText('form_label_account_function_company'))
                ->setRequired(false)
                ->setOrder(8)
                ->setAttribs(array('class'=>'stdTextInput'));



            // Are you a retailer
            $retailer = new Zend_Form_Element_Select('isRetailer');
            $retailer->setLabel( $this->getView()->getClientText('form_label_retailer') )
                      ->setAttrib('class', 'smallTextInput');

            $retailer->addMultiOption(0, $this->getView()->getCibleText('button_no'));
            $retailer->addMultiOption(1, $this->getView()->getCibleText('button_yes'));


            // Text Subscribe
            $textSubscribe = $this->getView()->getCibleText('form_label_subscribe');
            $textSubscribe = str_replace('%URL_PRIVACY_POLICY%', Cible_FunctionsPages::getPageLinkByID($this->_config->page_privacy_policy->pageID), $textSubscribe);

            // Newsletter subscription
            $newsletterSubscription = new Zend_Form_Element_Checkbox('newsletterSubscription');
            $newsletterSubscription->setLabel($textSubscribe);
            if($this->_mode == 'add')
                $newsletterSubscription->setChecked(1);
            $newsletterSubscription->setAttrib('class','long-text');
            $newsletterSubscription->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

            if( $this->_mode == 'add'){
                $termsAgreement = new Zend_Form_Element_Checkbox('termsAgreement');
                $termsAgreement->setLabel(str_replace('%URL_TERMS_CONDITIONS%', Cible_FunctionsPages::getPageLinkByID($this->_config->termsAndConditions->pageId), $this->getView()->getClientText('form_label_terms_agreement')));
                $termsAgreement->setAttrib('class','long-text');
                $termsAgreement->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
                ));
                $termsAgreement->setRequired(true);
                $termsAgreement->addValidator('notEmpty', true, array(
                  'messages' => array(
                    'isEmpty'=>'You must agree to the terms'
                  )
                ));
            }
            else{
                $termsAgreement = new Zend_Form_Element_Hidden('termsAgreement', array('value' => 1));
            }

            // Submit button
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel($this->getView()->getCibleText('form_label_next_step_btn'))
                   ->setAttrib('class','nextStepButton');

            // Reference number for the job
            $txtConnaissance = new Cible_Form_Element_Html(
                'knowYou',
                array(
                    'value' => $this->getView()->getCibleText('form_account_mieux_vous_connaitre_legend')
                )
                );
            $txtConnaissance->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'description left')
                    ),
                )
        );
            $refJobId = new Zend_Form_Element_Text('refJobId');
            $refJobId->setLabel('refJobId')
                  ->setRequired(false)
                  ->setAttribs(array('class' => 'stdTextInput'));

            // Reference number for the role
            $refRoleId = new Zend_Form_Element_Text('refRoleId');
            $refRoleId->setLabel('refRoleId')
                  ->setRequired(false)
                  ->setAttribs(array('class' => 'stdTextInput'));

            // Reference number for the job title
            $refJobTitleId = new Zend_Form_Element_Text('refJobTitleId');
            $refJobTitleId->setLabel('refJobTitleId')
                  ->setRequired(false)
                  ->setAttribs(array('class' => 'stdTextInput'));

            $refJobTitleId = new Zend_Form_Element_Text('refJobTitleId');
            $refJobTitleId->setLabel('refJobTitleId')
                  ->setRequired(false)
                  ->setAttribs(array('class' => 'stdTextInput'));

            // Provincial tax exemption
            $noProvTax = new Zend_Form_Element_Checkbox('noProvTax');
            $noProvTax->setLabel($this->getView()->getCibleText('form_label_account_provincial_tax'));
            $noProvTax->setAttrib('class','long-text')
                ->setOrder(13);
            $noProvTax->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

             // Provincial tax exemption
            $noFedTax = new Zend_Form_Element_Checkbox('noFedTax');
            $noFedTax->setLabel($this->getView()->getCibleText('form_label_account_federal_tax'));
            $noFedTax->setAttrib('class','long-text')
                ->setOrder(14);
            $noFedTax->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));


            /*  Identification sub form */
            $identificationSub = new Zend_Form_SubForm();
            $identificationSub->setName('identification')
                ->removeDecorator('DtDdWrapper');
            $identificationSub->setLegend($this->getView()->getCibleText('form_account_subform_identification_legend'));
            $identificationSub->setAttrib('class', 'identificationClass subFormClass');
            $identificationSub->addElement($language);
            $identificationSub->addElement($salutation);
            $identificationSub->addElement($lastname);
            $identificationSub->addElement($firstname);
            $identificationSub->addElement($email);
            $identificationSub->addElement($password);
            $identificationSub->addElement($passwordConfirmation);
            $identificationSub->addElement($company);
            $this->addSubForm($identificationSub,'identification');
//            $identificationSub->addElement($functionCompany);

            $addrContactMedia = new Cible_View_Helper_FormAddress($identificationSub);

            if($options['resume'])
                $addrContactMedia->setProperty ('addScript', false);

            $addrContactMedia->enableFields(
                    array(
                        'firstTel',
                        'secondTel',
                        'fax',
                        'webSite')
                    );

            $addrContactMedia->formAddress();

            $identificationSub->addElement($noProvTax);
            $identificationSub->addElement($noFedTax);

            /*  Identification sub form */

            /* billing address */
             // Billing address

            $addressFacturationSub = new Zend_Form_SubForm();
            $addressFacturationSub->setName('addressFact')
                ->removeDecorator('DtDdWrapper');
            $addressFacturationSub->setLegend($this->getView()->getCibleText('form_account_subform_addBilling_legend'));
            $addressFacturationSub->setAttrib('class', 'addresseBillingClass subFormClass');
            $billingAddr = new Cible_View_Helper_FormAddress($addressFacturationSub);
            $billingAddr->setProperty('addScriptState', false);
            if($options['resume'])
                $billingAddr->setProperty ('addScript', false);
            $billingAddr->enableFields(
                    array(
                        'firstAddress',
                        'secondAddress',
                        'state',
                        'cityTxt',
                        'zipCode',
                        'country',
                        'firstTel',
                        'secondTel')
                    );

            $billingAddr->formAddress();

            $addrBill = new Zend_Form_Element_Hidden('addrBill');
            $addrBill->removeDecorator('label');
            $addressFacturationSub->addElement($addrBill);
            $addressFacturationSub->getElement('AI_SecondAddress')->removeDecorator('label');
            $this->addSubForm($addressFacturationSub,'addressFact');

            /* delivery address */
            $addrShip = new Zend_Form_Element_Hidden('addrShip');
            $addrShip->removeDecorator('label');


            $addressShippingSub = new Zend_Form_SubForm();
            $addressShippingSub->setName('addressShipping')
                ->removeDecorator('DtDdWrapper');;
            $addressShippingSub->setLegend($this->getView()->getCibleText('form_account_subform_addShipping_legend'));
            $addressShippingSub->setAttrib('class', 'addresseShippingClass subFormClass');

            $shipAddr = new Cible_View_Helper_FormAddress($addressShippingSub);

            if($options['resume'])
                $shipAddr->setProperty ('addScript', false);

            $shipAddr->duplicateAddress($addressShippingSub);
            $shipAddr->setProperty('addScriptState', false);
            $shipAddr->enableFields(
                array(
                    'firstAddress',
                    'secondAddress',
                    'state',
                    'cityTxt',
                    'zipCode',
                    'country',
                    'firstTel',
                    'secondTel')
                );

            $shipAddr->formAddress();

            $addressShippingSub->addElement($addrShip);

            $this->addSubForm($addressShippingSub,'addressShipping');

            if( $this->_mode == 'edit'){
                $this->addElement($termsAgreement);
            }

            $this->addElement($submit);

            $submit->setDecorators(array(
                            'ViewHelper',
                            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'stepBottomNext')),
                    ));

            if( $this->_mode == 'add'){
                $termsAgreement->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox', 'id' => 'dd-terms-agreement')),
                ));
            }
        }
    }
?>
