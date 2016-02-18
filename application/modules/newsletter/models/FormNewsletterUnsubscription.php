<?php

class FormNewsletterUnsubscription extends Cible_Form {

    public function __construct($options = null) {
        $this->_disabledDefaultActions = true;

        parent::__construct($options);

        // email
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

        $email = new Zend_Form_Element_Text('email');
        $this->setAttrib('class', 'zendFormNewsletter');
        $email->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->addValidator($regexValidate)
                ->setAttrib('class', 'stdTextInput')
                ->setAttrib('placeholder', $this->getView()->getCibleText('forms_label_placeholder_email'))
                ;

        $this->addElement($email);

        //unsubscription reason
        $reason = new Zend_Form_Element_Select('reason');
        $this->setAttrib('class', 'zendFormNewsletter');
        $reason->setLabel($this->getView()->getCibleText('form_label_unsubscribe_reason'))
//            ->setRequired(true)
//            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                'Errors',
                                array('placement' => 'append')
                            ),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag' => 'dd',
                                    'class' => 'reasonSelect',
                                    'id' => '')
                            ),
                        )
                )
                ->setAttrib('class', 'stdSelect');

        $selectoptions = array();
        $oRef = new ReferencesObject();
        $options = $oRef->referencesCollection('unsubscrArg', Zend_Registry::get('languageID'));
        foreach ($options as $option) {
            $value = $option['R_TypeRef'] . '-' . $option['R_ID'];
            $selectoptions[$option['R_ID']] = $option['RI_Value'];
        }

        $reason->addMultiOptions($selectoptions);
        $reason->addMultiOption(0, $this->getView()->getCibleText('newsletter_title_desabonnement_other'));
        //$this->addElement($reason);

        //unsubscription reason
        $reasonOther = new Zend_Form_Element_Textarea('reasonOther');
        $this->setAttrib('class', 'zendFormNewsletter');
        $reasonOther->setAttrib('cols', '35');
        $reasonOther->setAttrib('rows', '5');
        $reasonOther
//            ->setLabel($this->getView()->getCibleText('form_label_unsubscribe_reason'))
//            ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
//            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->addFilter('StringToLower')
                ->setAttrib('class', 'reasonTextarea');

       // $this->addElement($reasonOther);

        $unsubscribeButton = new Zend_Form_Element_Submit('unsubscribe');
        $unsubscribeButton->setLabel($this->getView()->getCibleText('newsletter_title_desabonnement_text'))
                ->setAttrib('id', 'unsubmitSave')
                ->setAttrib('class', 'unsubscribeButton1')
                ->removeDecorator('Label')
                ->removeDecorator('DtDdWrapper');

        $this->addElement($unsubscribeButton);

        $this->addDisplayGroup(
                array('unsubscribe'), 'actions'
        );

        $actions = $this->getDisplayGroup('actions');
        $this->setDisplayGroupDecorators(array(
            'formElements',
            'fieldset',
            array(array('outerHtmlTag' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd-unsubscribe-button'))
        ));

        $requiredFields = new Zend_Form_Element_Hidden('RequiredFields');
        $requiredFields->setLabel('<br /><span class="">*</span>'
                . $this->getView()->getCibleText('form_field_required_label')
                . '<br /><br />');

        $requiredFields->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_required_fields'))
        ));
        $this->addElement($requiredFields);

        $this->setDecorators(array('FormElements', 'Form'));

        $this->formatDivDecorators();
    }
    
    public function formatDivDecorators($options = null) {
        parent::formatDivDecorators();        
        
    }

}
