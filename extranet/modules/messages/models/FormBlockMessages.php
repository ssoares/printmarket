<?php
    class FormBlockMessages extends Cible_Form_Block
    {
        protected $_moduleName = 'messages';

        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];

            parent::__construct($options);

            /****************************************/
            // PARAMETERS
            /****************************************/

            $oData = new MessagesObject();
            $messages = $oData->getMessagesList();

            // display news date (Parameter #1)
            $msg = new Zend_Form_Element_Select('Param1');
            $msg->setLabel($this->getView()->getCibleText('form_block_label_select_message'));
            $msg->setOrder(3);

            $msg->addMultiOptions($messages);
            $this->addElement($msg);
            // Define the popup width
            $width = new Zend_Form_Element_Text('Param2');
            $width->setLabel($this->getView()->getCibleText('form_block_label_popup_width'))
                ->setOrder(4)
                ->setAttrib('class','smallTextInput');

            $this->addElement($width);
            
            $this->removeDisplayGroup('parameters');

            $this->addDisplayGroup(array('Param999', 'Param1', 'Param2'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }
?>
