<?php
/**
 * Module Users
 * Data management for the registered users.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormGenericProfile.php 1788 2015-06-18 20:07:52Z ssoares $id
 */

/**
 * Form to manage the generic profile.
 * Data are used to create account basis.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormGenericProfile.php 1788 2015-06-18 20:07:52Z ssoares $id
 */
class FormGenericProfile extends Cible_Form
{

    protected $_mode = 'add';

    public function __construct($options = null)
    {
//        $this->_disabledDefaultActions = true;
//        $this->_object = $options['object'];
        unset($options['object']);
        parent::__construct($options);
        $langId = 1;
        if (!empty($options['mode']) && $options['mode'] == 'edit')
            $this->_mode = 'edit';
        if (!empty($options['langId']))
            $langId = $options['langId'];

//        $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/jquery.maskedinput.min.js");
//        // email
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

        $emailNotFoundInDBValidator = new Zend_Validate_Db_NoRecordExists('GenericProfiles', 'GP_Email');
        $emailNotFoundInDBValidator->setMessage($this->getView()->getClientText('validation_message_email_already_exists'), 'recordFound');

        $email = new Zend_Form_Element_Text('GP_Email');
        $email->setLabel($this->getView()->getCibleText('form_label_email'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->addValidator($regexValidate)
            ->setAttribs(array('maxlength' => 50, 'class' => 'required email mediumTextInput'));

        if ($this->_mode == 'add')
            $email->addValidator($emailNotFoundInDBValidator);

        $this->addElement($email);

        if ($this->_mode == 'edit')
        {
            // Salutation
            $salutation = new Zend_Form_Element_Select('GP_Salutation');
            $salutation->setLabel($this->getView()->getCibleText('form_label_salutation'))
                ->setAttrib('class', 'largeSelect');

            $greetings = $this->getView()->getAllSalutation();
            foreach ($greetings as $greeting)
            {
                $salutation->addMultiOption($greeting['S_ID'], $greeting['ST_Value']);
            }

            $this->addElement($salutation);

            // LastName
            $lastname = new Zend_Form_Element_Text('GP_LastName');
            $lastname->setLabel($this->getView()->getCibleText('form_label_lName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('maxlength' => 20, 'class' => 'required stdTextInput'));

            $this->addElement($lastname);

            //FirstName
            $firstname = new Zend_Form_Element_Text('GP_FirstName');
            $firstname->setLabel($this->getView()->getCibleText('form_label_fName'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttribs(array('maxlength' => 20, 'class' => 'required stdTextInput'));

            $this->addElement($firstname);

            $languages = new Zend_Form_Element_Select('GP_Language');
            $languages->setLabel($this->getView()->getCibleText('form_label_language'));

            foreach (Cible_FunctionsGeneral::getAllLanguage() as $lang)
            {
                $languages->addMultiOption($lang['L_ID'], $lang['L_Title']);
            }

            $this->addElement($languages);
        }

//        // new password
//        $password = new Zend_Form_Element_Password('GP_Password');
//        $password->setLabel($this->getView()->getCibleText('form_label_newPwd'))
//            ->addFilter('StripTags')
//            ->addFilter('StringTrim')
//            ->setAttrib('class', 'stdTextInput')
//            ->setAttrib('autocomplete', 'off');
//
//
//        // password confirmation
//        $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
//        $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'))
//            ->addFilter('StripTags')
//            ->addFilter('StringTrim')
//            ->setAttrib('class', 'stdTextInput')
//            ->setAttrib('autocomplete', 'off');
//
//        if (Zend_Registry::get('pwdOn'))
//        {
//            $this->addElement($password);
//            $this->addElement($passwordConfirmation);
//        }

        $this->setAttrib('id', 'genericProfile');

    }


//    public function isValid($data)
//    {
//        $passwordConfirmation = $this->getElement('passwordConfirmation');
//        if (Zend_Registry::get('pwdOn') && !empty($data['GP_Password']))
//        {
//            $passwordConfirmation->setRequired(true)
//                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));
//
//            $Identical = new Zend_Validate_Identical($data['GP_Password']);
//            $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
//            $passwordConfirmation->addValidator($Identical);
//        }
//
//        return parent::isValid($data);
//    }
}