<?php
    class FormExtranetGroupAssociate extends Cible_Form
    {
        public function __construct($options = null, $pagesData = array()) 
        {
            // variable
            parent::__construct($options);
            $baseDir    = $options['baseDir'];
            
            //$this->getView()->dump($pagesData);
            foreach ($pagesData as $page){
                $checkBox = new Zend_Form_Element_Checkbox($page['P_ID']);
                $checkBox->setLabel($page['PI_PageTitle']);
                $this->addElement($checkBox);
                if (count($page['child'] > 0)){
                    
                    FormExtranetGroupAssociate::checkBoxChildConstruct($this, $page['child']);
                }
            }

            // submit button  (save)
            $submitSave = new Zend_Form_Element_Submit('submit');
            $submitSave->setLabel('Sauvegarder')
                ->setName('submitSave')
                ->setAttrib('id', 'submitSave')
                ->setAttrib('class','stdButton')
                ->removeDecorator('DtDdWrapper');
            
            $this->addElement($submitSave);    
            
            // cancel button
            $cancel = new Zend_Form_Element_Button('Annuler',array('onclick'=>"document.location.href='$baseDir'"));
            $cancel->setAttrib('class','stdButton')
            ->setName('Annuler')
            ->removeDecorator('DtDdWrapper');
            
            $this->addElement($cancel);
            
            
        }
        
        public function checkBoxChildConstruct($form, $childs, $class = 1)
        {
            
            foreach ($childs as $child){
                $checkBox = new Zend_Form_Element_Checkbox($child['P_ID']);
                $checkBox->setLabel($child['PI_PageTitle']);
                $checkBox->setAttrib('class', "childNiveau_$class");
                $form->addElement($checkBox);
                if (count($child['child'] > 0)){
                    FormExtranetGroupAssociate::checkBoxChildConstruct($this, $child['child'], $class++);    
                }
            }
        }
    }
?>
