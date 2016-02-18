
<?php
    class FormBlockRepertoire extends Cible_Form_Block
    {
        protected $_moduleName = 'repertoire';

        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];

            parent::__construct($options);

            /****************************************/
            // PARAMETERS
            /****************************************/


            // number of repertoire to show in front-end (Parameter #1)
            $blockRepertoireMax = new Zend_Form_Element_Text('Param2');
            $blockRepertoireMax->setLabel($this->getView()->getCibleText('label_number_repertoire_show'))
                         ->setAttrib('class','smallTextInput')
                         ->setOrder(4);

           // $this->addElement($blockRepertoireMax);

            // show the breif text in front-end (Parameter #3)
//            $blockShowBrief = new Zend_Form_Element_Checkbox('Param1');
//            $blockShowBrief->setLabel($this->getView()->getCibleText('label_show_brief_text'))
//                           ->setOrder(5);
//            $blockShowBrief->setDecorators(array(
//                'ViewHelper',
//                array('label', array('placement' => 'append')),
//                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
//            ));
//
//            $this->addElement($blockShowBrief);

            // display order (Parameter #4)
            $blockOrder = new Zend_Form_Element_Select('Param1');
            $blockOrder->setLabel($this->getView()->getCibleText('label_order_display'))
            ->setAttrib('class','largeSelect')
            ->setOrder(6);

            $blockOrder->addMultiOption('RI_Name ASC',$this->getView()->getCibleText('form_block_label_orderby_name'));
            $blockOrder->addMultiOption('RD_Region ASC',$this->getView()->getCibleText('form_block_label_orderby_region'));

            //$this->addElement($blockOrder);

            $this->removeDisplayGroup('parameters');

            $this->addDisplayGroup(array('Param999', 'Param1', 'Param2'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
            //$parameters->setLegend($this->_view->getCibleText('form_parameters_fieldset'));
        }
    }
?>
