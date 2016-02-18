<?php
  class FormProfile extends Cible_Form{
      protected $_mode = 'add';

      public function __construct($options = null)
      {
            parent::__construct($options);
            $langId = 1;
            if( !empty($options['mode']) && $options['mode'] == 'edit' )
                $this->_mode = 'edit';
            if( !empty($options['langId']))
                $langId = $options['langId'];

            $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/jquery.maskedinput.min.js");

            $countries = Cible_FunctionsGeneral::getCountries();
            $states = Cible_FunctionsGeneral::getStates();

            //*** Generic Profile ***/
            $genericForm = new Cible_Form_SubForm();

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
            ->setAttribs(array('maxlength'=> 50, 'class'=>'stdTextInput'));

            if( $this->_mode == 'add')
                $email->addValidator($emailNotFoundInDBValidator);

            $genericForm->addElement($email);

            // Salutation
            $salutation = new Zend_Form_Element_Select('salutation');
            $salutation->setLabel('Salutation :')
            ->setAttrib('class','largeSelect');

            $greetings = $this->getView()->getAllSalutation();
            foreach ($greetings as $greeting){
                $salutation->addMultiOption($greeting['S_ID'], $greeting['ST_Value']);
            }

            $genericForm->addElement($salutation);

            // LastName
            $lastname = new Zend_Form_Element_Text('lastName');
            $lastname->setLabel($this->getView()->getCibleText('form_label_lName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('maxlength'=> 20, 'class'=>'stdTextInput'));

            $genericForm->addElement($lastname);

            //FirstName
            $firstname = new Zend_Form_Element_Text('firstName');
            $firstname->setLabel($this->getView()->getCibleText('form_label_fName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('maxlength'=> 20, 'class'=>'stdTextInput'));

            $genericForm->addElement($firstname);

            $languages = new Zend_Form_Element_Select('language');
            $languages->setLabel( $this->getView()->getCibleText('form_label_language') );

            foreach( Cible_FunctionsGeneral::getAllLanguage() as $lang ){
                $languages->addMultiOption($lang['L_ID'], $lang['L_Title']);
            }

            $genericForm->addElement($languages);

            //*** Newsletter Profile ***/
            $newsletterForm = new Cible_Form_SubForm();

            $newsletterCategories = $this->getView()->GetAllNewsletterCategories();
            $newsletterCategories = $newsletterCategories->toArray();

            foreach($newsletterCategories as $cat){
                //$this->getView()->dump($cat);
                $chkCat = new Zend_Form_Element_Checkbox("chkNewsletter{$cat['C_ID']}");
                $chkCat->setLabel($cat['CI_Title']);
                $chkCat->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
                ));

                $newsletterForm->addElement($chkCat);
            }

            //*** Members Profile ***/
            $memberForm = new Cible_Form_SubForm(array('name' => 'membersForm'));

            // new password
            $password = new Zend_Form_Element_Password('password');
            $password->setLabel($this->getView()->getCibleText('form_label_newPwd'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class','stdTextInput')
                ->setAttrib('autocomplete', 'off');

            $memberForm->addElement($password);

            // password confirmation
            $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
            $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class','stdTextInput')
                ->setAttrib('autocomplete', 'off');

            if (!empty($_POST['membersForm']['password'])) {
                $passwordConfirmation->setRequired(true)
                    ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));

                $Identical = new Zend_Validate_Identical($_POST['membersForm']['password']);
                $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
                $passwordConfirmation->addValidator($Identical);
            }

            $memberForm->addElement($passwordConfirmation);

            //  Status of the customer to access to the cart and order process
            $status = new Zend_Form_Element_Select('status');
            $status->setLabel( $this->getView()->getCibleText('form_label_account_status') );
            $statusList = array(
                '-1' => 'Désactivé',
                '0' => 'Email non validé',
                '1' => 'À valider',
                '2' => 'Activé'
            );
            $status->addMultiOptions($statusList);
            $memberForm->addElement($status);

            // Company name
            $company = new Zend_Form_Element_Text('company');
            $company->setLabel($this->getView()->getCibleText('form_label_company'))
                ->setRequired(false)
//                ->setOrder()
                ->setAttribs(array('class'=>'stdTextInput'));

            $memberForm->addElement($company);


            // Billing address
            $addressFacturationSub = new Cible_Form_SubForm();
            $addressFacturationSub->setName('addressFact')
                ->removeDecorator('DtDdWrapper');
            $addressFacturationSub->setLegend($this->getView()->getCibleText('form_account_subform_addBilling_legend'));
            $addressFacturationSub->setAttrib('class', 'addresseBillingClass subFormClass');
            $billingAddr = new Cible_View_Helper_FormAddress($addressFacturationSub);
            $billingAddr->setParentForm($memberForm->getName());
            $billingAddr->enableFields(
                    array(
                        'firstAddress',
                        'secondAddress',
                        'state',
                        'cityTxt',
                        'zipCode',
                        'country',
                        'firstTel',
                        'seconfTel',
                        'fax'
                        )
                    );

            $billingAddr->formAddress();

            $addrBill = new Zend_Form_Element_Hidden('addrBill');
            $addrBill->removeDecorator('label');
            $addressFacturationSub->addElement($addrBill);

            $memberForm->addSubForm($addressFacturationSub, 'addressFact');

            /* delivery address */
            $addrShip = new Zend_Form_Element_Hidden('addrShip');
            $addrShip->removeDecorator('label');

            $addressShippingSub = new Cible_Form_SubForm();
            $addressShippingSub->setName('addressShipping')
                ->removeDecorator('DtDdWrapper');;
            $addressShippingSub->setLegend($this->getView()->getCibleText('form_account_subform_addShipping_legend'));
            $addressShippingSub->setAttrib('class', 'addresseShippingClass subFormClass');

            $shipAddr = new Cible_View_Helper_FormAddress($addressShippingSub);
            $shipAddr->setParentForm($memberForm->getName());
            $shipAddr->duplicateAddress($addressShippingSub);
            $shipAddr->enableFields(
                array(
                    'firstAddress',
                    'secondAddress',
                    'state',
                    'cityTxt',
                    'zipCode',
                    'country',
                    'firstTel',
                    'seconfTel',
                    'fax'
                    )
                );

            $shipAddr->formAddress();

            $addressShippingSub->addElement($addrShip);
            $this->addSubForm($addressShippingSub,'addressShipping');

            $memberForm->addSubForm($addressShippingSub, 'addressShipping');

            //*** Add subform to the form ***/
            $this->addSubForm($genericForm, 'genericForm');
            $this->addSubForm($newsletterForm, 'newsletterForm');
            $this->addSubForm($memberForm, 'membersForm');
      }
  }
?>
