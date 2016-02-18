<?php
    
    class FormBackButton extends Cible_Form_Multilingual
    {
        /**
         *
         * @param array $options Options to build the form
         */
        public function __construct($options = null)
        {
            parent::__construct($options);
            $this->getView()->assign('labelColor', $this->_labelCSS);
//            $cancelUrl = $options['cancelUrl'];
//
//            // Set data to go back
//            $cancel_params = array('onclick'=>"document.location.href='$cancelUrl'");
//            // Create a new cancel button replacing the default one.
//            $back = new Zend_Form_Element_Button('cancel',$cancel_params);
//            $back->setLabel('Retour')
//                ->setAttrib('class','stdButton')
//                ->setDecorators(array(
//                    'ViewHelper',
//                    array(array('data'=>'HtmlTag'),array('tag'=>'li')),
//                    array(array('row'=>'HtmlTag'),array('tag'=>'ul', 'class' => 'actions-buttons')
//                )))
//                ->setOrder(10);
//
//            $this->addElement($back);
//            // Add this button to the display group
//            $this->addDisplayGroup(
//                    array('cancel'),
//                    'actions'
//                );
//
//           $this->setAttrib('id', 'Back');
        }
    }
?>
