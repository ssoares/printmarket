<?php
class FormNewsletter extends Cible_Form
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

        // Date picker
        $datePicker = new Cible_Form_Element_DatePicker('NR_Date', array('jquery.params'=> array('changeYear'=>true, 'changeMonth'=> true)));

        $datePicker->setLabel($this->getView()->getCibleText('form_extranet_newsletter_label_releaseDate'))
        //->setAttrib('class','stdTextInput')
        ->setRequired(true)
        ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
        ->addValidator('Date', true, array('messages' => array( 'dateNotYYYY-MM-DD' => $this->getView()->getCibleText('validation_message_invalid_date'),
                                                                'dateInvalid' => $this->getView()->getCibleText('validation_message_invalid_date'),
                                                                'dateFalseFormat' => $this->getView()->getCibleText('validation_message_invalid_date')
                                                            )));
        $this->addElement($datePicker);

        // Language
        $language = new Zend_Form_Element_Select('NR_LanguageID');
        $language->setLabel($this->getView()->getCibleText('form_label_language'))
        ->setAttrib('class','largeSelect');

        $languagesData = Cible_FunctionsGeneral::getAllLanguage();
        foreach ($languagesData as $languageData){
            $language->addMultiOption($languageData['L_ID'], $languageData['L_Title']);
        }

        $this->addElement($language);

        // Category
        $category = new Zend_Form_Element_Hidden('NR_CategoryID');
//        $category->setLabel($this->getView()->getCibleText('form_label_category'))
//        ->setAttrib('class','largeSelect');
        $categoriesData = $this->getView()->getAllNewsletterCategories();
        $category->removeDecorator('Label')->setValue($categoriesData[0]['C_ID']);
//        foreach ($categoriesData as $categoryData){
//            $category->addMultiOption($categoryData['C_ID'], $categoryData['CI_Title']);
//        }

        $this->addElement($category);

        // Model
        $model = new Zend_Form_Element_Select('NR_ModelID');
        $model->setLabel($this->getView()->getCibleText('form_label_model'))
        ->setAttrib('class','largeSelect');

        $modelsData = $this->getView()->getAllNewsletterModels();
        foreach ($modelsData as $modelData){
            $model->addMultiOption($modelData['NMI_NewsletterModelID'], $modelData['NMI_Title']);
        }
        $this->addElement($model);

         //if($this->salutationDefaultText!=""){
        $intro = new Cible_Form_Element_Editor('NR_TextIntro', array('mode' => Cible_Form_Element_Editor::ADVANCED));
        $intro->setLabel($this->getView()->getCibleText('form_label_newsletter_text_intro'))
            ->setAttrib('class','largeEditor');
        $this->addElement($intro);


        // show online
        $showOnline = new Zend_Form_Element_Checkbox('NR_Online');
        $showOnline->setLabel($this->getView()->getCibleText('form_label_showOnline'));
        $showOnline->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        $this->addElement($showOnline);

        // email
//            $regexValidate = new Cible_Validate_Email();
//            $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

        $email = new Zend_Form_Element_Text('NR_AdminEmail');
        $email->setLabel($this->getView()->getCibleText('newsletter_form_label_admin_email'))
//            ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addFilter('StringToLower')
//            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
//            ->addValidator($regexValidate)
        ->setAttrib('class','stdTextInput');
        /*$email->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'prepend')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd_form dd_email'))
        ));*/
       // $email->setAttrib('class', 'newsletter_form_element text_email');
        $this->addElement($email);

    }

    public function populate(array $values)
    {
        if ($values['NR_Status'] == 1 || $values['NR_Status'] == 2){
            $this->getElement ('NR_Date')->setAttrib ('disabled', 'disabled');
        }
        return parent::populate($values);
    }
}
