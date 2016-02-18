<?php
class FormNewsletterArticle extends Cible_Form{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $newsletterID  = $options['newsletterID'];
        $imageSrc   = $options['imageSrc'];
        $isNewImage = $options['isNewImage'];

        // Title
        $title = new Zend_Form_Element_Text('NA_Title');
        $title->setLabel($this->getView()->getCibleText('form_label_title'))
            ->setRequired(true)
            //->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');

        $this->addElement($title);


        // article image
        if($newsletterID == '')
            $pathTmp = "../../../../../data/images/newsletter/tmp";
        else
            $pathTmp = "../../../../../data/images/newsletter/$newsletterID/tmp";

         if ($newsletterID == '')
            $pathTmp = $this->_imagesFolder . "/tmp";
        else
            $pathTmp = $this->_imagesFolder . "/$newsletterID/tmp";


        // hidden specify if new image for the news
        $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value'=>$isNewImage));
        $newImage->removeDecorator('Label');
        //$newImage->setDecorators(array('ViewHelper'));
        $this->addElement($newImage);

        $imageTmp  = new Zend_Form_Element_Hidden('ImageSrc_tmp');
        $imageTmp->removeDecorator('Label');
        $this->addElement($imageTmp);

        $cropImage  = new Zend_Form_Element_Hidden('cropImage');
        $cropImage->removeDecorator('Label');
        $cropImage->setValue('');
        $this->addElement($cropImage);

        $imageOrg  = new Zend_Form_Element_Hidden('ImageSrc_original');
        $imageOrg->removeDecorator('Label');
        $this->addElement($imageOrg);

        $imageView = new Zend_Form_Element_Image('ImageSrc_preview', array('onclick'=>'return false;'));
        $imageView->setImage($imageSrc);
        $this->addElement($imageView);

        $imagePicker = new Cible_Form_Element_ImagePicker('ImageSrc', array( 'onchange' => "document.getElementById('imageView').src = document.getElementById('ImageSrc').value",
                                                                                'associatedElement' => 'ImageSrc_preview',
                                                                                'pathTmp'=>$pathTmp,
                                                                                'contentID'=>$newsletterID
                                                                    ));
        $imagePicker->removeDecorator('Label');
        $this->addElement($imagePicker);

        $imageAlt = new Zend_Form_Element_Text("NA_ImageAlt");
        $imageAlt->setLabel($this->getView()->getCibleText('form_label_description_image'))
        ->setAttrib('class','stdTextInput');

        $this->addElement($imageAlt);

        // resume text
        $resume = new Cible_Form_Element_Editor('NA_Resume', array('mode' => Cible_Form_Element_Editor::ADVANCED));
        $resume->setLabel($this->getView()->getCibleText('form_label_short_text'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => Cible_Translation::getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','largeEditor')
            ->setDecorators(array(
                'ViewHelper',
                array('Errors', array('placement' => 'prepend')),
                array('label', array('placement' => 'prepend')),
            ));

        $this->addElement($resume);

        $optionText = new Zend_Form_Element_Radio('NA_TextLink');
            $optionText->setRequired(true)
                ->addMultiOption('1', $this->getView()->getCibleText('extranet_newsletter_option_text_url_text'))
                ->addMultiOption('2', $this->getView()->getCibleText('extranet_newsletter_option_text_url_url'))
                ->addMultiOption('3', $this->getView()->getCibleText('extranet_newsletter_option_text_url_nothing'));
        $optionText->setValue('1');

           // Text
            $bluetext = new Zend_Form_Element_Textarea('NA_Blue');
            $bluetext->setLabel($this->getView()->getCibleText('form_label_blue_text'))
                    ->setAttribs(array(
                        'class' => 'largeEditor content content-blue',
                        'rows' => 5
                        ));

//            $this->addElement($bluetext);
           // Text
            $text = new Cible_Form_Element_Editor('NA_Text', array('mode'=>Cible_Form_Element_Editor::ADVANCED, 'class'=>'textAreaToMoveUp'));
            $text->setLabel($this->getView()->getCibleText('form_label_text'))
                ->setAttrib('class','largeEditor');

            $this->addElement($text);

            $url = new Zend_Form_Element_Text('NA_URL');
            $url->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->setAttrib('class','stdTextInputNewsletterURL');


            $this->addElement($optionText);
            $this->addElement($url);

            $this->addDisplayGroup(array('NA_TextLink', 'NA_URL'), 'linkTo',array('legend' => $this->getView()->getCibleText('extranet_newsletter_text_url')));


      }
  }
?>

<script type="text/javascript">
$('#fieldset-linkTo [value*="1"]').addClass('option_article');
$('#fieldset-linkTo [value*="1"]').addClass('modify1');
$('#fieldset-linkTo [value*="2"]').addClass('option_article');
$('#fieldset-linkTo [value*="2"]').addClass('modify2');
$('#fieldset-linkTo [value*="3"]').addClass('option_article');
$('#fieldset-linkTo [value*="3"]').addClass('modify3');

$('label[for*="NA_TextLink-1"]').addClass('option_label_1');
$('label[for*="NA_TextLink-2"]').addClass('option_label_2');
$('label[for*="NA_TextLink-3"]').addClass('option_label_3');

$('input[id*="NA_URL"]').addClass('url_text_input');
</script>
