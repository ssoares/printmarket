<?php
    
    class FormBecomeClient extends Cible_Form
    {
        protected $_mode = 'add';

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
//            $addressParams = array(
//                "fieldsValue" => array(),
//                "display"   => array(),
//                "required" => array(),
//            );

            // Salutation
            $salutation = new Zend_Form_Element_Select('salutation');
            $salutation->setLabel($this->getView()->getCibleText('form_label_salutation'))
                ->setAttrib('class','smallSelect')
                ->setAttrib('tabindex','1')
                ->setOrder(1);

            $greetings = $this->getView()->getAllSalutation();
            foreach ($greetings as $greeting){
                $salutation->addMultiOption($greeting['S_ID'], $greeting['ST_Value']);
            }

            // Language
            $languages = new Zend_Form_Element_Select('language');
            $languages->setLabel( $this->getView()->getCibleText('form_label_language') )
                ->setAttrib('class', 'stdSelect')
                ->setAttrib('tabindex','9')
                ->setOrder(9);
            foreach( Cible_FunctionsGeneral::getAllLanguage() as $lang ){
                $languages->addMultiOption($lang['L_ID'], $lang['L_Title']);
            }

            // FirstName
            $firstname = new Zend_Form_Element_Text('firstName');
            $firstname->setLabel($this->getView()->getCibleText('form_label_fName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('class'=>'stdTextInput'))
                ->setAttrib('tabindex','2')
                ->setOrder(2);

            // LastName
            $lastname = new Zend_Form_Element_Text('lastName');
            $lastname->setLabel($this->getView()->getCibleText('form_label_lName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('class'=>'stdTextInput'))
                ->setAttrib('tabindex','3')
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
                ->setAttrib('tabindex','5')
                ->setOrder(5);

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
                ->setAttrib('tabindex','6')
                ->setRequired(true)
                ->setOrder(6)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
            // password

            // password confirmation
            $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
            if( $this->_mode == 'add')
                $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmPwd'));
            else
                $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmPwd'));
//                $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'));

            $passwordConfirmation->addFilter('StripTags')
            ->addFilter('StringTrim')
                ->setRequired(true)
                ->setOrder(7)
                ->setAttrib('class','stdTextInput')
                ->setAttrib('tabindex','7')
                ->setDecorators(array(
                    'ViewHelper',
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd')),
                    array('label', array('class' => 'test', 'tag' => 'dt', 'tagClass' => 'alignVertical')),
                ));;

            if (!empty($_POST['identification']['password'])) {
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
                ->setAttrib('tabindex','4')
                ->setOrder(4)
                ->setAttribs(array('class'=>'stdTextInput'));

            // Account number
            $account = new Zend_Form_Element_Text('accountNum');
            $account->setLabel($this->getView()->getCibleText('form_label_account'))
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setOrder(8)
                ->setAttribs(array('class'=>'stdTextInput'))
                ->setAttrib('tabindex','8')
                ->setDecorators(array(
                    'ViewHelper',
                    'Errors',
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd')),
                    array('label', array('class' => 'test', 'tag' => 'dt', 'tagClass' => 'alignVertical')),
                ));



            // Text Subscribe
            $textSubscribe = $this->getView()->getCibleText('form_label_subscribe');

            $textSubscribe = str_replace('%URL_PRIVACY_POLICY%', Cible_FunctionsPages::getPageLinkByID($this->_config->privacyPolicy->pageId), $textSubscribe);

            // Newsletter subscription
            $newsletterSubscription = new Zend_Form_Element_Checkbox('newsletterSubscription');
            $newsletterSubscription->setLabel($textSubscribe);
            if($this->_mode == 'add')
                $newsletterSubscription->setChecked(1);
            $newsletterSubscription->setAttrib('class','long-text');
            $newsletterSubscription->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'id' => 'subscribeNewsletter' ,'class' => 'label_after_checkbox')),
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
            $submitLabel = $this->getView()->getCibleText('form_account_button_submit');
            if ($this->_mode == 'edit')
                $submitLabel = $this->getView()->getCibleText('button_submit');

            $submit->setLabel($submitLabel)
                   ->setAttrib('class','stdButton subscribeButton1-' . Zend_Registry::get("languageSuffix"));

            // Captcha
            // Refresh button
            $refresh_captcha = new  Zend_Form_Element_Button('refresh_captcha');
            $refresh_captcha->setLabel($this->getView()->getCibleText('button_refresh_captcha'))
                   ->setAttrib('onclick', "refreshCaptcha('captcha-id')")
                   ->setAttrib('class','stdButton')
                   ->removeDecorator('Label')
                   ->removeDecorator('DtDdWrapper');

            $refresh_captcha->addDecorators(array(
                array(array('row'=>'HtmlTag'),array('tag'=>'dd', 'class' => 'dd-refresh-captcha-button')),
            ));

            $captcha = new Zend_Form_Element_Captcha('captcha', array(
                'label' => $this->getView()->getCibleText('form_label_securityCaptcha'),
                'captcha' => 'Image',
                'captchaOptions' => array(
                    'captcha' => 'Word',
                    'wordLen' => 5,
                    'fontSize' => 28,
                    'height'  => 67,
                    'width'   => 169,
                    'timeout' => 300,
                    'dotNoiseLevel' => 0,
                    'lineNoiseLevel' => 0,
                    'font'    => Zend_Registry::get('application_path') ."/../{$this->_config->document_root}/captcha/fonts/ARIAL.TTF",
                    'imgDir'  => Zend_Registry::get('application_path') . "/../{$this->_config->document_root}/captcha/tmp",
                    'imgUrl'  => "$baseDir/captcha/tmp"
                ),
            ));
            $captcha->setAttrib('class','stdTextInputCatcha');
            $captcha->setRequired(true);
            $captcha->addDecorators(
                array(
                    array(
                        array('row'=>'HtmlTag'),
                        array('tag'=>'dd', 'id'=> 'dd_captcha'),
                        )
                    )
                )
                ->addDecorator('Label', array('class' => 'clear'));

            $french = array(
                'badCaptcha'    => 'Veuillez saisir la chaîne ci-dessus correctement.'
            );

            $english = array(
                'badCaptcha'    => 'Captcha value is wrong'
            );

            $translate = new Zend_Translate('array', $french, 'fr');

            $this->setTranslator($translate);

            $this->getView()->jQuery()->enable();

            $script = <<< EOS

            function refreshCaptcha(id){
                $.getJSON('{$this->getView()->baseUrl()}/newsletter/index/captcha-reload',
                    function(data){

                        $("dd#dd_captcha img").attr({src : data['url']});
                        $("#"+id).attr({value: data['id']});

                });
            }

EOS;

            $this->getView()->headScript()->appendScript($script);
            // Captcha

            /*  Identification sub form */
            $identificationSub = new Cible_Form_SubForm();
            $identificationSub->setName('identification')
                ->removeDecorator('DtDdWrapper');
            $identificationSub->setLegend($this->getView()->getCibleText('form_account_subform_identification_legend'));
            $identificationSub->setAttrib('class', 'identificationClass subFormClass');
            $identificationSub->addElement($languages);
            $identificationSub->addElement($salutation);
            $identificationSub->addElement($lastname);
            $identificationSub->addElement($firstname);
            $identificationSub->addElement($email);
            $identificationSub->addElement($password);
            $identificationSub->addElement($passwordConfirmation);
            $identificationSub->addElement($company);
            $identificationSub->addElement($account);
            $identificationSub->addDisplayGroup(
                array(
                    'salutation',
                    'firstName',
                    'company',
                    'password',
                    'accountNum'),
                'leftColumn'
                );
            $identificationSub->addDisplayGroup(
                array(
                    'lastName',
                    'email',
                    'passwordConfirmation',
                    'language'),
                'rightColumn'
                )->removeDecorator('DtDdWrapper');

            $leftColGroup = $identificationSub->getDisplayGroup('leftColumn');
            $rightColGroup = $identificationSub->getDisplayGroup('rightColumn');

            $leftColGroup->removeDecorator('DtDdWrapper');
            $rightColGroup->removeDecorator('DtDdWrapper');

            $this->addSubForm($identificationSub,'identification');

            // Billing address
            $addressFacturationSub = new Cible_Form_SubForm();
            $addressFacturationSub->setName('addressFact')
                ->removeDecorator('DtDdWrapper');
            $addressFacturationSub->setLegend($this->getView()->getCibleText('form_account_subform_addBilling_legend'));
            $addressFacturationSub->setAttrib('class', 'addresseBillingClass subFormClass');
            $billingAddr = new Cible_View_Helper_FormAddress($addressFacturationSub);
            $billingAddr->enableFields(
                    array(
                        'firstAddress',
                        'secondAddress',
                        'cityTxt',
                        'zipCode',
                        'country',
                        'state',
                        'stateTxt',
                        'firstTel',
                        'secondTel',
                        'fax'
                        )
                    );

            $billingAddr->formAddress();

            $addrBill = new Zend_Form_Element_Hidden('addrBill');
            $addrBill->removeDecorator('label');
            $addressFacturationSub->addElement($addrBill);
            $this->addSubForm($addressFacturationSub,'addressFact');

            /* delivery address */
            $addrShip = new Zend_Form_Element_Hidden('addrShip');
            $addrShip->removeDecorator('label');


            $addressShippingSub = new Cible_Form_SubForm();
            $addressShippingSub->setName('addressShipping')
                ->removeDecorator('DtDdWrapper');;
            $addressShippingSub->setLegend($this->getView()->getCibleText('form_account_subform_addShipping_legend'));
            $addressShippingSub->setAttrib('class', 'addresseShippingClass subFormClass');

            $shipAddr = new Cible_View_Helper_FormAddress($addressShippingSub);
            $shipAddr->duplicateAddress($addressShippingSub);
            $shipAddr->setProperty('addScriptState', false);
            $shipAddr->enableFields(
                array(
                    'firstAddress',
                    'secondAddress',
                    'cityTxt',
                    'zipCode',
                    'country',
                    'state',
                    'stateTxt',
                    'firstTel',
                    'secondTel',
                    'fax'
                    )
                );

            $shipAddr->formAddress();

            $addressShippingSub->addElement($addrShip);
            $this->addSubForm($addressShippingSub,'addressShipping');


            if($this->_mode == 'add')
            {
                $this->getView()->jQuery()->enable();
                $script = <<< EOS

                function refreshCaptcha(id){
                    $.getJSON('{$this->getView()->baseUrl()}/order/index/captcha-reload',
                        function(data){
                            $("dd#dd_captcha img").attr({src : data['url']});
                            $("#"+id).attr({value: data['id']});
                    });
                }

EOS;

//                $this->getView()->headScript()->appendScript($script);
//                $this->addElement($refresh_captcha);
//                $this->addElement($captcha);
                $this->addElement($newsletterSubscription);
                $this->addElement($termsAgreement);
            }

           $this->addElement($submit);


            $submit->setDecorators(array(
                    'ViewHelper',
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'account-submit')),
                ));


            if( $this->_mode == 'add'){
                $termsAgreement->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox', 'id' => 'dd-terms-agreement')),
                ));
            }

            $captchaError = array(
                'badCaptcha'    => $this->getView()->getCibleText('validation_message_captcha_error')
            );

            $translate = new Zend_Translate('array', $captchaError, $this->getView()->registryGet('languageSuffix'));

            $this->setTranslator($translate);

        }
    }
?>
