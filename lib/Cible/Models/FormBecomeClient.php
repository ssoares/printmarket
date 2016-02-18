<?php

class FormBecomeClient extends Cible_Form_GenerateForm {

    protected $_mode = 'add';
    private $_requiredValidator = null;

    public function __construct($options = null) {

        $this->_disabledDefaultActions = true;
        $this->_disabledLangSwitcher = true;
         $this->_requiredValidator = new Zend_Validate_NotEmpty();
        $this->_requiredValidator->setMessage($this->getView()->getCibleText('validation_message_empty_field'), 'isEmpty');

        if (isset($options['object'])){
            $this->_object = new $options['object'];
            unset($options['object']);
        }
        $this->_mode = 'add';
        if (!empty($options['mode']) && $options['mode'] == 'edit'){
            $this->_mode = 'edit';
        }
        unset($options['mode']);
        /*  Identification sub form teams profile */
        $this->getView()->FormAddressTeams($this, array(
            'requiredValidator' => $this->_requiredValidator,
            'mode' => $this->_mode));


        parent::__construct($options);
        $attrs = $this->getAttribs();
        foreach($options as $key => $value){
            if (in_array($key, array_keys($attrs))){
                $this->removeAttrib($key);
            }
        }
        $this->getDisplayGroup('columnRight')->setLegend(
            Cible_Translation::getCibleText('fieldset_team'))
            ->setAttrib('class', 'col-xs-12 col-md-5')
            ->setDecorators(array('FormElements', 'Fieldset'));
        $this->removeElement("TED_ProfileId");
        $this->removeElement("TED_Delete");
        $this->removeElement("TED_AskValidation");
        $this->removeElement("TED_IsValid");
        $this->getElement('TED_AddressId')->clearValidators()->setRequired(false);
        $member = new Zend_Form_Element_Text('TM_Member');
        $member->setLabel($this->getView()->getCibleText('form_label_team_members'))
            ->setAttrib('class', 'member-row')
            ->setBelongsTo('teamMembers')
            ->setDecorators(array('ViewHelper',
                'Errors',
                array('HtmlTag', array('tag' => 'div', 'class' => 'memberRow')),
                array('Label', array('class'=> '', 'tag'=>'div', 'placement' => 'prepend') ),
                array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'row')),
                ));
        $this->getDisplayGroup('columnRight')->addElement($member);
        if ($this->_mode == 'add') {
            $this->getView()->formAddCaptcha($this);
        }
        $campaignId = new Zend_Form_Element_Hidden('CAT_CampaignId');
        $campaignId->setDecorators(array('ViewHelper'));
        $this->addElement($campaignId);
        // Submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submitLabel = $this->getView()->getCibleText('form_account_button_submit');
        if ($this->_mode == 'edit'){
            $submitLabel = $this->getView()->getCibleText('button_submit');
//            $this->getElement('TED_Goal')->clearValidators()->setRequired(false);
//            $this->removeElement('TED_Goal');
        }
        $submit->setLabel($submitLabel)
                ->setAttrib('class', 'button_submit');
        $this->addElement($submit);
        $this->addDisplayGroup(array('captcha', 'refresh_captcha',
            'CAT_CampaignId','submit'), 'columnBottom');
        $this->getDisplayGroup('columnBottom')->setLegend(null)
            ->setOrder(50)
            ->setAttrib('class', 'col-xs-12 col-md-12')
            ->removeDecorator('DtDdWrapper');

    }

    public function populate(array $values)
    {
        if (!empty($values['teamMembers'])){
            $this->getDisplayGroup('columnRight')->getElement('TM_Member')->setValue($values['teamMembers'][0]);
            unset($values['teamMembers'][0]);
            foreach($values['teamMembers'] as $key => $value)
            {
                $tmp = new Zend_Form_Element_Text('TM_Member' . $key,
                    array('value' => $value, 'class' => 'member-row'));
                $tmp->setDecorators(array('ViewHelper',
                'Errors',
                array('HtmlTag', array('tag' => 'div', 'class' => 'memberRow')),
                array(array('div' => 'HtmlTag'), array('tag' => 'div', 'class' => 'row')),
                ));
                $this->getDisplayGroup('columnRight')->addElement($tmp);
            }
        }
        return parent::populate($values);
    }

    public function isValid($data)
    {
        if (!empty($data['GP_Password'])) {
            $validatePassword = new Cible_Validate_Password();
            $password = $this->getElement('GP_Password');
            $passwordConfirmation = $this->getElement('passwordConfirmation');
            $password->addValidator($validatePassword);
            $Identical = new Zend_Validate_Identical($data['GP_Password']);
            $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
            $passwordConfirmation->addValidator($validatePassword);
            $passwordConfirmation->addValidator($Identical);
        }
        $emailMain = $this->getSubForm('identification')
                ->getElement('GP_Email');
            // Test if the email already exists and password is the same
        $oProfile = new GenericProfilesObject();
        $existsButDifferent = $oProfile->validateExistingAccount($data['identification']);
        if ($existsButDifferent){
            $same = new Zend_Validate_Identical();
            $same->setToken($existsButDifferent)->setMessage(
            Cible_Translation::getCibleText('donator_already_exists_nomatch_name'));
            $decos = $emailMain->setValidators(array($same))
                ->getDecorators();
            $deco = $decos['Zend_Form_Decorator_Errors'];
            $deco->setOption('escape', false);
        }
        if ($this->_mode == 'edit') {
            $currentEmail = $emailMain->getValue();
            if ($data['identification']['GP_Email'] == $currentEmail) {
                $emailMain->clearValidators()->setRequired(false);
            }

            if (empty($data['identification']['GP_Password'])
                && empty($data['identification']['passwordConfirmation'])) {
                $this->getSubForm('identification')->getElement('GP_Password')
                    ->clearValidators()->setRequired(false);
                $this->getSubForm('identification')->getElement('passwordConfirmation')
                    ->clearValidators()->setRequired(false);
            }
        }

        $isValid = parent::isValid($data);

        return $isValid;
    }

    public function render(Zend_View_Interface $view = null)
    {
        $this->getElement('CAT_CampaignId')->setValue($this->getView()->cId);

        $this->getElement('TEI_Name')
            ->setAttrib('class', 'full-width')
            ->setDecorators(array('ViewHelper', 'Errors',
                array('Label', array('class'=> 'full-width') ),
                array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'row'))));
//        if ($this->_mode == 'add'){
            $this->getElement('TED_Goal')
                ->setDecorators(array('ViewHelper', 'Errors',
                    array('Label', array('class'=> 'full-width') ),
                    array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'row'))));
//        }
        $this->getElement('TED_Logo_preview')
            ->setDecorators(array('ViewHelper', 'Errors',
                array('Label', array('class'=> 'full-width') ),
                array('row' => 'HtmlTag', array('tag' => 'div', 'class' => ''))));
        $this->getElement('TEI_Description')
            ->setDecorators(array('ViewHelper', 'Errors',
                array('Label', array('class'=> 'full-width') ),
                array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'row'))));

        $this->setDecorators(array('FormElements', 'Form'));
        $this->setAttrib('class', 'container form-teams');
        return parent::render($view);
    }

}
