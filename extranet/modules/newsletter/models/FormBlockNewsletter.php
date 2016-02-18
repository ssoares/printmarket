<?php
    class FormBlockNewsletter extends Cible_Form_Block
    {
        protected $_moduleName = 'newsletter';

        public function __construct($options = null){
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            $options['cancelUrl'] = "";
            parent::__construct($options);

            /****************************************/
            // PARAMETERS
            /****************************************/

            /****************************************/
            // select newsletters categories (Param #1)
            $categoriesData = $this->getView()->getAllNewsletterCategories();

            $blockCategory = new Zend_Form_Element_Hidden('Param1');
//            $blockCategory->setLabel($this->getView()->getCibleText('form_label_category'))
//            ->setAttrib('class','largeSelect');
            $blockCategory->removeDecorator('Label')->setValue($categoriesData[0]['C_ID']);
//            foreach ($categoriesData as $category){
//                $blockCategory->addMultiOption($category['C_ID'],$category['CI_Title']);
//            }

            $this->addElement($blockCategory);

            $this->addDisplayGroup(array('Param999','Param1','Param2'), 'parameters');
            $parameters = $this->getDisplayGroup('parameters');

            $parameters->setLegend($this->getView()->getCibleText('form_legend_settings'));
        }
    }
?>
