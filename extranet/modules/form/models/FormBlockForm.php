<?php
    class FormBlockForm extends Cible_Form_Block
    {
        protected $_moduleName = 'form';
        
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            
            parent::__construct($options);
            
            /****************************************/
            // PARAMETERS
            /****************************************/
            
            // Build the select to choose the associated form (Parameter #1)
            $blockForm = new Zend_Form_Element_Select('Param1');
            $blockForm->setLabel('Formulaire associé à ce bloc')
                      ->setAttrib('class','largeSelect');
            
            $forms = new Form();
            $select = $forms->getFormList();
            
            $formsArray = $forms->fetchAll($select);
            // Set the default value
            $blockForm->addMultiOption('0','Choisir un formulaire');
            //Fill the dropdown list
            foreach ($formsArray as $form)
            {
                $blockForm->addMultiOption($form['F_ID'],$form['FI_Title']);
            }
            // Test if a value has been chosen
            $at_least_one = new Zend_Validate_GreaterThan('0');
            $at_least_one->setMessage('Vous devez choisir un élément dans la liste.');
            $blockForm->addValidator($at_least_one);

            $this->addElements(array($blockForm));
            
            $this->removeDisplayGroup('parameters');
            
            $this->addDisplayGroup(array('Param999', 'Param1'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }
    }