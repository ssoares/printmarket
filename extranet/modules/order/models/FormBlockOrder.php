<?php
    class FormBlockOrder extends Cible_Form_Block
    {
        protected $_moduleName = 'order';
        
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            
            parent::__construct($options);
            
            $this->removeDisplayGroup('parameters');
            
            $this->addDisplayGroup(array('Param999', 'Param1'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }
?>
