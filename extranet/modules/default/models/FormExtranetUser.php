<?php

class FormExtranetUser extends Cible_Form
{

    public function __construct($options = null, $isAdministrator)
    {
        $this->_addSubmitSaveClose = true;
        // variable
        parent::__construct($options);
        $baseDir = $options['baseDir'];
        $exclude = array();
        $userId = 0;
        $profile = false;
        if (array_key_exists('userId', $options))
            $userId = $options['userId'];
        $exclude = array('field' =>'EU_ID', 'value'=> $userId);
        if (array_key_exists('profile', $options))
            $profile = $options['profile'];

        // lastname
        $lname = new Zend_Form_Element_Text('EU_LName');
        $lname->setLabel($this->getView()->getCibleText('form_label_lname'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('escape', false);

        $this->addElement($lname);

        // firstname
        $fname = new Zend_Form_Element_Text('EU_FName');
        $fname->setLabel($this->getView()->getCibleText('form_label_fname'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput');

        $this->addElement($fname);

        // email
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');
        $emailNotFoundInDBValidator = new Zend_Validate_Db_NoRecordExists('Extranet_Users', 'EU_Email', $exclude);
        $emailNotFoundInDBValidator->setMessage($this->getView()->getClientText('validation_message_email_already_exists'), 'recordFound');

        $email = new Zend_Form_Element_Text('EU_Email');
        $email->setLabel($this->getView()->getCibleText('form_label_email'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->addValidator($regexValidate)
            ->addValidator($emailNotFoundInDBValidator)
            ->setAttrib('class', 'stdTextInput');

        $this->addElement($email);

        // username
        $usernameNotFoundInDBValidator = new Zend_Validate_Db_NoRecordExists('Extranet_Users', 'EU_Username', $exclude);
        $usernameNotFoundInDBValidator->setMessage($this->getView()->getCibleText('validation_message_username_already_exists'), 'recordFound');

        $username = new Zend_Form_Element_Text('EU_Username');
        $username->setLabel($this->getView()->getCibleText('form_label_username'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->addValidator($usernameNotFoundInDBValidator)
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');

        $this->addElement($username);

        // new password
        $validatePassword = new Cible_Validate_Password();
        $password = new Zend_Form_Element_Password('EU_Password');
        $password->setLabel($this->getView()->getCibleText('form_label_newPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator($validatePassword)
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');

        $this->addElement($password);

        // password confirmation
        $passwordConfirmation = new Zend_Form_Element_Password('PasswordConfirmation');
        $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'stdTextInput');

        if (!empty($_POST['EU_Password']))
        {
            $passwordConfirmation->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));

            $Identical = new Zend_Validate_Identical($_POST['EU_Password']);
            $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
            $passwordConfirmation->addValidator($Identical)
                ->addValidator($validatePassword);
        }
        $this->addElement($passwordConfirmation);


        $show = new Zend_Form_Element_Checkbox('EU_ShowError');
        $show->setLabel($this->getView()->getCibleText('show_super_user_error'));
        $show->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        $this->addElement($show);

        if ($profile <> true)
        {
            if ($isAdministrator <= 2)
            {
                $disabled = new Zend_Form_Element_Checkbox('EU_Disabled');
                $disabled->setLabel($this->getView()->getCibleText('form_label_EU_Disabled'));
                $disabled->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
                ));
                $this->addElement($disabled);
            }
            $html = new Cible_Form_Element_Html('associateEntities');
            $html->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_no_empty_sites'))));
            $this->addElement($html);
        }
    }

    public function render(Zend_View_Interface $view = null)
    {
        if ($this->getView()->joinAssociation)
        {
            $html = $this->getElement('associateEntities');
            $html->setValue(Cible_FunctionsAssociationElements::getNewAssociationEntities(
                    'entities',
                    '',
                    '',
                    '1',
                    '',
                    $this->getView()->data,
                    $this->getView()->related))
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');
        }
        return parent::render($view);
    }

    public function isValid($data)
    {
        $error = false;
        $testAccess = true;
        if (empty($data['testAccess']) && !is_numeric($data['testAccess'])){
            $testAccess = false;
        }elseif(is_numeric($data['testAccess']) && $data['testAccess'] < 1){
            $testAccess = false;
        }
        if(empty($data['testDefault'])){
            if ($testAccess){
                $this->getElement('associateEntities')
                    ->addError($this->getView()->getCibleText('validation_message_no_default_site'));
            }
            $error = true;
        }elseif (!preg_match('/' . $data['testDefault'] . '/', $data['testAccess'])){
                $this->getElement('associateEntities')
                    ->addError($this->getView()->getCibleText('validation_message_wrong_default_site'));
            $error = true;
        }

        if ($testAccess && !$error){
            $this->getElement('associateEntities')
                ->clearValidators()
                ->setRequired(false);
        }

        return parent::isValid($data);
    }
}
