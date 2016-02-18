<?php
class FormNewsletterCopy extends Cible_Form
{
     public function __construct($options = null)
     {
        parent::__construct($options);

        // Title
        $title = new Zend_Form_Element_Text('NR_Title');
        $title->setLabel($this->getView()->getCibleText('form_label_title'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');

        $this->addElement($title);

    }
}
