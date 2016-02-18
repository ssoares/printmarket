<?php
    
class FormOrder extends Cible_Form
{
    public function __construct($options = null)
    {
        parent::__construct($options);

        $status = new Zend_Form_Element_Select('status');
        $status->setLabel($this->getView()->getClientText('quote_request_status_label'));

        $status->addMultiOptions(
            array(
                1 => $this->getView()->getCibleText('quoteRequest_status_1'),
                2 => $this->getView()->getCibleText('quoteRequest_status_2'))
            );
        
        $this->addElement($status);
    }
}