<?php
    class FormBlockCart extends Cible_Form_Block
    {
        protected $_moduleName = 'cart';
        
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            
            parent::__construct($options);
            
            /****************************************/
            // PARAMETERS
            /****************************************/
            
            // display news date (Parameter #1)
//            $cartType = new Zend_Form_Element_Radio('Param1');// Zend_Form_Element_Checkbox('Param1');
//            $cartType->setLabel('Type de panier')
//                           ->setOrder(3);
//            $cartType->setSeparator('');
//            $cartType->addMultiOptions(array(
//                1 => 'Flooring',
//                2 => 'SheetRubber')
//            )->setValue(1);
//
//            $this->addElement($cartType);
            
            $this->removeDisplayGroup('parameters');
            
            $this->addDisplayGroup(array('Param999', 'Param1'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }
?>
