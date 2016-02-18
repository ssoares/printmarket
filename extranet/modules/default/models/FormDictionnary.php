<?php
    
    class FormDictionnary extends Cible_Form
    {
        public function __construct($options = null)
        {
            parent::__construct($options);
            
            // tinymce editor for the text of the text online
            $value = new Zend_Form_Element_Textarea('ST_Value');
            $value->setLabel('Valeur:')
                ->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => 'Vous devez saisir un texte')))
                ->setAttrib('class','mediumEditor');
            
            $this->addElement($value);
            
            $identifier = new Zend_Form_Element_Hidden('ST_Identifier');
            $identifier->removeDecorator('DtDdWrapper');
            $identifier->removeDecorator('label');
            
            $this->addElement($identifier);
            
            $language = new Zend_Form_Element_Hidden('ST_LangID');
            $language->removeDecorator('DtDdWrapper');
            $language->removeDecorator('label');
            
            $this->addElement($language);
            
            $type = new Zend_Form_Element_Hidden('ST_Type');
            $type->removeDecorator('DtDdWrapper');
            $type->removeDecorator('label');
            
            $this->addElement($type);
        }
    }
?>
