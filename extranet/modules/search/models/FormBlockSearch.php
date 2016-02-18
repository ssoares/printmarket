<?php
    class FormBlockSearch extends Cible_Form_Block   
    {
        protected $_moduleName = 'search';
        
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            
            parent::__construct($options);
        }        
    }
?>
