<?php
    class FormExtranetGroup extends Cible_Form
    {
        public function __construct($options = null) 
        {
            // variable
            parent::__construct($options);
            $baseDir = $options['baseDir'];
            
            // name
            $name = new Zend_Form_Element_Text('EGI_Name');
            $name->setLabel($this->getView()->getCibleText('form_label_name'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');
            
            $this->addElement($name);
            
            
            // description
            $description = new Zend_Form_Element_Textarea('EGI_Description');
            $description->setLabel($this->getView()->getCibleText('form_label_description'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextareaEdit');
            
            $this->addElement($description);
            
            //status
            $status = new Zend_Form_Element_Select('EG_Status');
            $status->setLabel($this->getView()->getCibleText('form_label_status'))
            ->setAttrib('class','stdSelect');
            $status = Cible_FunctionsGeneral::fillStatusSelectBox($status,'Extranet_Groups', 'EG_Status');
            
            $this->addElement($status);
            
            // Hidden GroupID
            $groupID = new Zend_Form_Element_Hidden('groupID');
            $groupID->removeDecorator('label');
            $groupID->removeDecorator('DtDdWrapper');
            if(isset($options['groupID']))
                $groupID->setValue($options['groupID']);
                
            $this->addElement($groupID);
        }
    }
?>
