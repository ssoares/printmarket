<?php
    
    class FormOrderAddr extends Cible_Form
    {
        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            parent::__construct($options);
            $this->setAttrib('id', 'accountManagement');
            $this->setAttrib('class', 'step2');
            $baseDir = $this->getView()->baseUrl();

            /* billing address */
             // Billing address
            $addressFacturationSub = new Zend_Form_SubForm();
        $addressFacturationSub->setName('address')
                ->removeDecorator('DtDdWrapper');
            $addressFacturationSub->setLegend($this->getView()->getCibleText('form_account_subform_addBilling_legend'));
        $addressFacturationSub->setAttrib('class', 'col-lg-6');
            $billingAddr = new Cible_View_Helper_FormAddress($addressFacturationSub);
            $billingAddr->setProperty('addScriptState', false);
            $billingAddr->enableFields(
                    array(
                        'firstAddress',
                        'secondAddress',
                        'cityTxt',
                    'state',
                    'stateTxt',
                        'zipCode',
                        'country',
                        'firstTel',
                        'secondTel')
                    );

            $billingAddr->formAddress();
            $addrBill = new Zend_Form_Element_Hidden('addrBill');
            $addrBill->removeDecorator('label');
            $addressFacturationSub->addElement($addrBill);
        $this->addSubForm($addressFacturationSub,'address');

            /* delivery address */
            $addrShip = new Zend_Form_Element_Hidden('addrShip');
            $addrShip->removeDecorator('label');

            $addressShippingSub = new Zend_Form_SubForm();
            $addressShippingSub->setName('addressShipping')
                ->removeDecorator('DtDdWrapper');;
            $addressShippingSub->setLegend($this->getView()->getCibleText('form_account_subform_addShipping_legend'));
        $addressShippingSub->setAttrib('class', 'col-lg-6');

            $shipAddr = new Cible_View_Helper_FormAddress($addressShippingSub);
            $shipAddr->duplicateAddress($addressShippingSub);
            $shipAddr->setProperty('addScriptState', false);
            $shipAddr->enableFields(
                array(
                    'firstAddress',
                    'secondAddress',
                'cityTxt',
                    'state',
                'stateTxt',
                    'zipCode',
                    'country',
                    'firstTel',
                    'secondTel')
                );

            $shipAddr->formAddress();

            $addressShippingSub->addElement($addrShip);
            $this->addSubForm($addressShippingSub,'addressShipping');
        $this->getSubForm('addressShipping')->getElement('duplicate')->setAttrib('checked', null);
//            $termsAgreement = new Zend_Form_Element_Checkbox('termsAgreement');
//            $termsAgreement->setLabel(str_replace('%URL_TERMS_CONDITIONS%', Cible_FunctionsPages::getPageLinkByID($this->_config->termsAndConditions->pageId), $this->getView()->getClientText('form_label_terms_agreement')))
//                           ->setDecorators(array(
//                               'ViewHelper',
//                                array('label', array('placement' => 'append')),
//                                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
//                            ));
//
//            $this->addElement($termsAgreement);

            //Means of payment
        $payments = new Zend_Form_Element_Select('payments');
        $payments->setLabel($this->getView()->getCibleText('form_label_payment_means'))
                    ->setRequired(true)
                    ->addValidator(
                            'NotEmpty',
                            true,
                            array(
                                'messages' => array(
                                    'isEmpty' => $this->getView()->getCibleText('validation_message_empty_field')
                            )
                        )
                    )
                    ->setAttrib('class', 'stdTextInput')
                    ->setDecorators(array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array('errors', array('placement' => 'append')),
                            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'selectPayment')),
                    ));

        $payments->addMultiOption('', $this->getView()->getClientText('cart_details_select_choose_label'));
        $payments->addMultiOption('paypal', $this->getView()->getCibleText('form_label_payement_paypal'));
//            $payments->addMultiOption('mastercard', $this->getView()->getCibleText('form_label_payement_mastercard'));
//            $payments->addMultiOption('compte', $this->getView()->getCibleText('form_label_payement_account'));
//            $payments->addMultiOption('cod', $this->getView()->getCibleText('form_label_payement_cod'));

//        $this->addElement($payments);
            
            // Submit button
            $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('placeholder', $this->getView()->getCibleText('form_label_next_step_btn'))
                ->setAttrib('class','nextStepButton hidden')
                    ->setDecorators(array(
                                    'ViewHelper',
                                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'stepBottomNext')),
                            ));

            $this->addElement($submit);
        $this->addDisplayGroup(array('submit'), 'bottom');
        $bt = $this->getDisplayGroup('bottom');
        $bt->setAttrib('class', 'col-lg-12');
//        $this->formatDivDecorators(array('classDiv' => 'form-div'));
        }
    }
