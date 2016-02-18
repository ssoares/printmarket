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
 * @version   $Id: FormMembersProfile.php 1242 2013-06-28 21:07:25Z ssoares $id
 */

/**
 * Form to manage specific data.
 * Fields will change for each project.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormMembersProfile.php 1242 2013-06-28 21:07:25Z ssoares $id
 */
class FormMembersProfile extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;
        $this->_object = $options['object'];

        unset($options['object']);
        parent::__construct($options);

//        $memberForm = new Cible_Form_SubForm(array('name' => 'membersForm'));

        // new password
        $password = new Zend_Form_Element_Password('MP_Password');
        $password->setLabel($this->getView()->getCibleText('form_label_newPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');

        $this->addElement($password);

        // password confirmation
        $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
        $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');

        $this->addElement($passwordConfirmation);

    }

    public function isValid($data)
    {
        $passwordConfirmation = $this->getElement('passwordConfirmation');
        if (!empty($data['MP_Password']))
        {
            $passwordConfirmation->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));

            $Identical = new Zend_Validate_Identical($data['MP_Password']);
            $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
            $passwordConfirmation->addValidator($Identical);
        }

        return parent::isValid($data);
    }
}