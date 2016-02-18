<?php
    
    class FormOrderPayment extends Cible_Form
    {

        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            $readOnly = $options['readOnlyForm'];
            $payement = $options['payMean'];
            $config   = Zend_Registry::get('config');
            
            unset($options['readOnlyForm']);
            unset($options['payMean']);
            
            parent::__construct($options);

            $this->setAttrib('id', 'accountManagement');
            
            $buttonLabel = $this->getView()->getClientText('form_label_confirm_order_btn');

            if (in_array($payement, array('visa', 'mastercard')))
            {
                $this->setAction($config->payment->url);
                $buttonLabel = $this->getView()->getClientText('form_label_confirm_payment_btn');
            }
            
            $baseDir = $this->getView()->baseUrl();

            // Account data summary
            $summary = new Cible_Form_Element_Html('summary',
                        array(
                            'value' => $readOnly
                        )
            );
            $summary->setDecorators(
                    array(
                        'ViewHelper',
                        array('label', array('placement' => 'prepend')),
                        array(
                            array('row' => 'HtmlTag'),
                            array(
                                'tag' => 'dd',
                                'class' => 'form_title_inline left')
                        ),
                    )
            );
            $this->addElement($summary);
            
            $storeId = new Zend_Form_Element_Hidden('ps_store_id', array('value' => $config->payment->storeId));
            $storeId->removeDecorator('label');
            $this->addElement($storeId);
            $hppKey = new Zend_Form_Element_Hidden('hpp_key', array('value' => $config->payment->hppkey));
            $hppKey->removeDecorator('label');
            $this->addElement($hppKey);
            $total = new Zend_Form_Element_Hidden('charge_total');
            $total->removeDecorator('label');
            $this->addElement($total);


            // Submit button
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel($buttonLabel)
                    ->setAttrib('class','nextStepButton')
                    ->setDecorators(array(
                                    'ViewHelper',
                                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'stepBottomNext')),
                            ));

            $this->addElement($submit);
        }
    }
?>
