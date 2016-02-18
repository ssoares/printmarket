<?php

class FormNewsletterSubscription extends Cible_Form {

    public function __construct($options = null) {
        $this->_disabledDefaultActions = true;
        $baseDir = $this->getView()->baseUrl();
        $viewScript = $options['viewScript'];
        unset($options['viewScript']);
        parent::__construct($options);

        $this->getView()->jQuery()->addJavascriptFile("{$this->getView()->baseUrl()}/js/jquery/jquery.maskedinput.min.js");
        $script = <<< EOS

            $('.phone_format').mask('(999) 999-9999? x99999');
            $('.postalCode_format').mask('a9a 9a9');
            $('.birthDate_format').mask('9999-99-99');
EOS;

        $this->getView()->jQuery()->addOnLoad($script);


        $this->setAttrib('class', 'zendFormNewsletter');

        $defaultType = new Zend_Form_Element_Hidden('NP_TypeID');
        $defaultType->setDecorators(array('ViewHelper'));
        $config = Zend_Registry::get('config');
        $defaultType->setValue($config->newsletter->defaultTypeId);
        $this->addElement($defaultType);
        $subscrDate = new Zend_Form_Element_Hidden('NP_SubscriptionDate');
        $subscrDate->setDecorators(array('ViewHelper'));
        $subscrDate->setValue(date('Y-m-d'));
        $this->addElement($subscrDate);

        // Salutation
        $salutation = new Zend_Form_Element_Select('salutation');
        $salutation->setLabel('Salutation')
                ->setAttrib('class', 'smallTextInput');
        $greetings = $this->getView()->getAllSalutation();
        foreach ($greetings as $greeting) {
            $salutation->addMultiOption($greeting['S_ID'], $greeting['ST_Value']);
        }
        /* $salutation->setDecorators(array(
          'ViewHelper',
          array('label', array('placement' => 'prepend')),
          array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd_form dd_sexe'))
          ));
          $salutation->setAttrib('class', 'newsletter_form_element select_salutations'); */

        //FirstName
        $firstname = new Zend_Form_Element_Text('firstName');
        $firstname->setLabel($this->getView()->getCibleText('newsletter_fo_form_label_fName'))
                ->setRequired(true)
                ->setAttrib('placeholder', $this->getView()->getCibleText('newsletter_fo_form_placeholder_fName'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class', 'newsletter-firstname');

        /* $firstname->setDecorators(array(
          'ViewHelper',
          array('label', array('placement' => 'prepend')),
          array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd_form dd_prenom'))
          ));
          $firstname->setAttrib('class', 'newsletter_form_element text_prenom'); */

        // LastName
        $lastname = new Zend_Form_Element_Text('lastName');
        $lastname->setLabel($this->getView()->getCibleText('newsletter_fo_form_label_lName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->setAttrib('placeholder', $this->getView()->getCibleText('newsletter_fo_form_placeholder_lName'))
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class', 'stdTextInput');
        /* $lastname->setDecorators(array(
          'ViewHelper',
          array('label', array('placement' => 'prepend')),
          array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd_form dd_nom'))
          )); */
        //$lastname->setAttrib('class', 'newsletter_form_element text_nom');

        // email
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel($this->getView()->getCibleText('newsletter_fo_form_label_email'))
                ->setRequired(true)
                ->setAttrib('placeholder', $this->getView()->getCibleText('newsletter_fo_form_placeholder_email'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->addValidator($regexValidate)
                ->setAttrib('class', 'stdTextInput');
        if (isset($options['email']) && $options['email'] != '')
            $email->setValue($options['email']);
        /* $email->setDecorators(array(
          'ViewHelper',
          array('label', array('placement' => 'prepend')),
          array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd_form dd_email'))
          )); */
        // $email->setAttrib('class', 'newsletter_form_element text_email');

        $termsAgreement = new Zend_Form_Element_Checkbox('termsAgreement');
        $langId = Zend_Registry::get('languageID');
        $title = $this->_config->site->title->$langId;
        $replace = array('replace' => array('##SITENAME##' => $title));
        $termsAgreement->setLabel($this->getView()->getCibleText('form_label_agree', null, $replace));
        $termsAgreement->setAttrib('class', 'long-text')->setUncheckedValue(null);
        $termsAgreement->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            'Errors',
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox largedd')),
        ));
        $termsAgreement->setRequired(true);
        $termsAgreement->addValidator('notEmpty', true, array(
            'messages' => array(
                'isEmpty' => $this->getView()->getClientText('terms_agreement_error_message')
            )
        ));

        // action button
        $subscribeButton = new Zend_Form_Element_Submit('subscribe');
        $subscribeButton->setLabel($this->getView()->getCibleText('button_submit'))
                ->setAttrib('id', 'submitSave')
                ->setAttrib('class', 'stdButton link-button')
                ->removeDecorator('Label')
                ->removeDecorator('DtDdWrapper');

        $requiredFields = new Zend_Form_Element_Hidden('RequiredFields');
        $requiredFields->setLabel('<span class="field_required">*</span>'
                . $this->getView()->getCibleText('form_field_required_label')
                . '<br /><br />');

        $requiredFields->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_required_fields'))
        ));

        $invi = new Zend_Form_Element_Hidden('language');
        $invi->setValue(Zend_Registry::get("languageID"));

        if (empty($viewScript)){
            $this->addElement($salutation);
            $this->addElement($firstname);
            $this->addElement($lastname);
        }
        $this->addElement($email);
        if (empty($viewScript)){
            $this->addElement($invi);
            $this->addElement($termsAgreement);
            // Captcha
            $this->getView()->formAddCaptcha($this);
        }
        $this->addElement($subscribeButton);
        if (empty($viewScript)){
            $this->addElement($requiredFields);
            $this->addDisplayGroup(array('refresh_captcha', 'captcha', 'subscribe'), 'captcha-fieldset');
            // reset form decorators to remove the 'dl' wrapper
            $this->formatDivDecorators();
        }else{
            $this->setAttrib('role', 'search');
        }
    }

}
