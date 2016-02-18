<?php

    class FormLogin extends Cible_Form
    {
        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            $this->_addRequiredAsterisks = false;
            parent::__construct($options);
            $baseDir = $this->getView()->baseUrl();

//            $this->getView()->headLink()->appendStylesheet("{$this->getView()->baseUrl()}/themes/default/css/login.css",'all');

            $this->setAttrib('class','login');

            $regexValidate = new Cible_Validate_Email();
            $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

            $email = new Zend_Form_Element_Text('emailLogin');
            $email->setLabel($this->getView()->getCibleText('form_label_email'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->addValidator($regexValidate)
            ->setAttrib('class','stdTextInput');

            $this->addElement($email);

            $password = new Zend_Form_Element_Password('password');
            $password->setLabel($this->getView()->getCibleText('form_label_password'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class','stdTextInput')
                //->setAttrib('autocomplete', 'off')
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));

            $this->addElement($password);

            // checkbox for client persistance
            $status = new Zend_Form_Element_Checkbox('stayOn');
            $status->setLabel($this->getView()->getCibleText('login_form_stayOn_label'));
            $status->setValue(1);
            $status->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd')),
            ));

            $this->addElement($status);

            // Submit button
            $submit = new Zend_Form_Element_Submit('submit_login');
            $submit->setLabel($this->getView()->getCibleText('button_submit'))
                   ->setAttrib('class', 'button');
            $submit->setDecorators(array(
                'ViewHelper',
                array(array('row' => 'HtmlTag'), array('tag' => 'dd'))
                )
            );
            $this->addElement($submit);

            $this->setAttrib('class', 'login-form');
        }
    }
?>
