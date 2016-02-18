<?php
    class FormBlockRss extends Cible_Form_Block
    {
        protected $_moduleName = 'rss';
        
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            
            parent::__construct($options);
            
            /****************************************/
            // PARAMETERS
            /****************************************/
            
            // display news date (Parameter #1)
            $category = new Zend_Form_Element_Select('Param1');
            $category->setLabel($this->_view->getCibleText('form_select_option_rss_choose_category'))
                      ->setOrder(3);
             
            $categories = Cible_FunctionsGeneral::getRssCategories();                                         
            
            foreach($categories as $cat)
            {
                $category->addMultiOption($cat['C_ID'], $cat['CI_Title']);
            }
                                         
            $this->addElement($category);
            
            $this->removeDisplayGroup('parameters');
            
            $this->addDisplayGroup(array('Param999', 'Param1'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }
?>
