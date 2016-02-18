<?php

class FormHomePage extends FormPage
{
    public function __construct($options = null)
    {
        Throw new Exception('This form is obsolete.');
        
        /*parent::__construct($options);
        $this->setName('page');
        
        // contains the id of the page
        $id = new Zend_Form_Element_Hidden('id');
        
        // textarea for the meta description of the page 
        $metaDescription = new Zend_Form_Element_Textarea('PI_MetaDescription');
        $metaDescription->setLabel('Description (meta)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->setAttrib('class','stdTextarea');
        
        // textarea for the meta keywords of the page
        $metaKeyWords = new Zend_Form_Element_Textarea('PI_MetaKeywords');
        $metaKeyWords->setLabel('Mots-clés (meta)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->setAttrib('class','stdTextarea');
        
        // submit button 
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('id', 'submitbutton');
        
        
        // add element to the form
        $this->addElements(array($metaDescription, $metaKeyWords, $submit,$id));
        */
    }
}