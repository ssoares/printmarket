<?php
    class FormBlockForms extends Cible_Form_Block
    {
        protected $_moduleName = 'forms';

        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];

            parent::__construct($options);

            /****************************************/
            // PARAMETERS
            /****************************************/

            $regexValidate = new Cible_Validate_Email();
            $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

            // display news date (Parameter #1)
            $recipient = new Zend_Form_Element_Text('Param1');
            $recipient->setLabel('Acheminer à l\'adresse suivante: ')
                      ->setOrder(3)
                      ->setRequired(true)
                      ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                      ->addValidator($regexValidate);



            $this->addElement($recipient);

            $this->removeDisplayGroup('parameters');

            $this->addDisplayGroup(array('Param999', 'Param1'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }
?>
