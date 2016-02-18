<?php
/**
 * File to manage the form building
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormFront - Build the form for the front office part.
 *
 * This class will receive an array with form data.
 * It will define the elments type to insert in this form, types of
 * validation and options.
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormFront extends Cible_Form
{
    const TEXTZONE     = 'textzone';
    const QUESTION     = 'questions';
    const UNDERSCORE   = '_';

    /**
     * Container with the form elements. it will allow to build the form with
     * the choices made by the administrator.
     *
     * @var array
     */
    private $_data = array();

    /**
     * the path to root of the application.
     *
     * @var string
     */
    private $_baseDir;

    /**
     * Name of the section group to display grouped elements.
     *
     * @var string
     */
    private $_displayGroupName = '';

    /**
     * Store the elements to be associated with its section.
     *
     * @var array
     */
    private $_displayGroupElements = array();

    /**
     * Form is controlled by a captcha. Default = false.
     * @var booelean
     */
    private $_hasCaptcha = false;

    /**
     * Send an email alert to the defined recipients
     *
     * @var boolean
     */
    private $_sendNotification = false;

    /**
     * Set the data values.
     *
     * @param array $data
     *
     * @return void
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * Getter for $_hasCaptcha.
     * Enable form control with the captcha.
     *
     * @return boolean
     */
    public function getHasCaptcha()
    {
        return $this->_hasCaptcha;
    }

    /**
     * Getter for $_sendNotification.
     * Enable email notification on form submit.
     *
     * @return boolean
     */
    public function getSendNotification()
    {
        return $this->_sendNotification;
    }

    /**
     ** Class constructor
     *
     * @access public
     *
     * @param mixte $options Options form behaviour
     * @param array $data    Data to build the form elements.
     */
    public function __construct($options = null, $data = array())
    {
        $this->_disabledDefaultActions = true;
        parent::__construct($options);
        $this->_baseDir = $this->getView()->baseUrl();

        $this->_data = $data;

        $this->buildForm();

    }

    /**
     * Build the form according to received data .
     *
     * @return void
     */
    public function buildForm()
    {

//        $this->_setJqueryPluginMask();
//        $this->_setPhoneFormatJs();

        //Construire le form en fonction des données récupérées en base.
        $formParams = $this->_data['form'];
        unset($this->_data['form']);

        $this->setAttrib('id', 'form_module');
        $this->_setJqueryPluginValidate();

        $formTitle = new Cible_Form_Element_Html('formTitle');
        $formTitle->removeDecorator('DtDdWrapper')
                  ->removeDecorator('Label');
        $formTitle->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'title')
                )
        ));

        $formTitle->setValue($formParams['FI_Title']);
        $this->addElement($formTitle);

        foreach ($this->_data as $sections)
        {
            $this->_addSections($sections);
        }

        foreach ($formParams as $param => $value)
        {
            switch ($param) {
                case 'F_Notification':
                    if ($value > 0)
                        $this->setNotification(true);
                    break;
                case 'F_Captcha':
                    if ($value > 0)
                        $this->_addCaptcha();

                    break;
                case 'F_Profil':
                    if ($value > 0)
                        $this->hasLoginProfile();
                    break;
                default:
                    break;
            }
        }

        $this->_addSubmitButton();
        $this->_setRefreshCaptchaJs();

    }

    /**
     * Build the section with the related elements
     *
     * @param array $sections
     *
     * @return void
     */
    private function _addSections($sections)
    {
        foreach ($sections as $attribut => $section)
        {
            $options = array();
            $elements = $section['elements'];
            $this->_addElements($elements);
            $this->_displayGroupName = 'section_' . $section['FS_ID'];

            $options['disableLoadDefaultDecorators'] = true;
            $decorators[] = 'formElements';
            $decorators[] = array_merge(
                            array('innerHtmlTag' => 'HtmlTag'),
                            array('tag' => 'td'));
            $decorators[] = 'fieldset';
            $decorators[] = array_merge(
                            array('outerHtmlTag' => 'htmlTag'),
                            array('tag' => 'td')
                        );

            if ($section['FS_ShowTitle'])
            {
                $options['legend'] = $section['FSI_Title'];
            }

            $this->addDisplayGroup(
                $this->_displayGroupElements,
                $this->_displayGroupName,
                $options);

            $this->setDisplayGroupDecorators($decorators);

            $this->_displayGroupElements = array();
        }
    }

    /**
     *
     * @param array $elements
     *
     * @return void
     */
    private function _addElements($elements)
    {
        foreach ($elements as $element)
        {
            if (isset($element[self::TEXTZONE]))
            {
                $this->_addTextZone($element[self::TEXTZONE]);
            }
            if (isset($element[self::QUESTION]))
            {
                $this->_addQuestion($element[self::QUESTION]);
            }
        }
    }

    private function _addQuestion($questions)
    {
        // Add element and validors for each question
        foreach ($questions as $question)
        {
            // According to the type of question select the right element type
            $elemToAddFunction = '_add' . $question['FQT_TypeName'];

            $this->$elemToAddFunction($question);
        }
    }

    /**
     * Add a html zone to the form which will display the textzone data.
     *
     * @param array $element
     *
     * @return void
     */
    private function _addTextZone($element)
    {
        $textZoneName = self::TEXTZONE . '_' . $element['FT_ElementID'];

        $textzone = new Cible_Form_Element_Html($textZoneName);
        $textzone->removeDecorator('DtDdWrapper')
                 ->removeDecorator('Label');

        $textzone->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'textzone')
                )
        ));

        $textzone->setValue($element['FTI_Text']);

        $this->addElement($textzone);
        $this->_displayGroupElements[] = $textZoneName;
    }

    /**
     * Add an input text element to the form.
     *
     * @param array $question
     *
     * @return void
     */
    private function _addText($question)
    {
        $elemName   = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $element = new Zend_Form_Element_Text($elemName);
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));
        $element->setAttrib('class', 'stdTextInput');

        $this->_addExtras($element, $question);

        $this->addElement($element);

        $this->_displayGroupElements[] = $elemName;
    }
    /**
     * Add a textarea element to the form.
     *
     * @param array $question
     *
     * @return void
     */
    private function _addMultiline($question)
    {
        $elemName   = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $element = new Zend_Form_Element_Textarea($elemName);
        $element->setAttribs(
            array('cols' => 23,
                  'rows' => 5)

            );
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));
        $element->setAttrib('class','stdTextarea');

        $this->_addExtras($element, $question);

        $this->addElement($element);
        $this->_displayGroupElements[] = $elemName;
    }
    /**
     * Add a select (dropdown list) element to the form.
     *
     * @param array $question
     *
     * @return void
     */
    private function _addSelect($question)
    {
        $elemName = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $element  = new Zend_Form_Element_Select($elemName);
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));
        $element->setAttrib('class','stdSelect');

        $this->_addExtras($element, $question);
        $this->addElement($element);
        $this->_displayGroupElements[] = $elemName;
    }
    /**
     * Add a radio button element to the form.
     *
     * @param array $question
     *
     * @return void
     */
    private function _addSingleChoice($question)
    {
        $elemName = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $element  = new Zend_Form_Element_Radio($elemName);
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));

        $this->_addExtras($element, $question);
        $this->addElement($element);

        $this->_displayGroupElements[] = $elemName;
    }
    /**
     * Add a multi checkbox element to the form.
     *
     * @param array $question
     *
     * @return void
     */
    private function _addMultiChoice($question)
    {
        $elemName = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $element  = new Zend_Form_Element_MultiCheckbox($elemName);
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'ViewHelper',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));

        $this->_addExtras($element, $question);
        $this->addElement($element);
        $this->_displayGroupElements[] = $elemName;
    }
    /**
     * Add a date picker element to the form.
     *
     * @see Cible_Form_Element_DatePicker
     *
     * @param array $question
     *
     * @return void
     */
    private function _addDate($question)
    {
        $elemName = $question['FQT_TypeName']
                        . self::UNDERSCORE
                        . $question['FQ_ElementID'];
        $options['jquery.params']  = array('changeYear'=>true, 'changeMonth'=> true);
        $element  = new Cible_Form_Element_DatePicker($elemName, $options);

        $element->addValidator(
            'Date',
            true,
            array(
                'format'   => 'dd-mm-yy',
                'messages' => array(
                    'dateNotYYYY-MM-DD' => $this->getView()->getCibleText('validation_message_invalid_date'),
                    'dateInvalid'       => $this->getView()->getCibleText('validation_message_invalid_date'),
                    'dateFalseFormat'   => $this->getView()->getCibleText('validation_message_invalid_date')
                )
            )
        );
        $element->removeDecorator('DtDdWrapper');
        $element->addDecorators(
         array(
            'UiWidgetElement',
            array('HtmlTag',
                array(
                    'tag' => 'div',
                    'class' => 'answer-zone')
                )
        ));
        $element->setAttrib('class','dateTextInput');
        $element->setjQueryParam('dateFormat', 'dd-mm-yy');

        $this->_addExtras($element, $question);
        $this->addElement($element);
        $this->_displayGroupElements[] = $elemName;
    }

    private function _addExtras($element, $data)
    {
        $element->setLabel($data['FQI_Title']);

        if (count($data['validators']) > 0)
        {
            $this->_addValidators($element, $data['validators']);
        }

        if (count($data['options']) > 0)
        {
            $this->_addOptions($element, $data['options']);
        }

        if (count($data['responseOption']) > 0)
        {
            $this->_addResponseOption($element, $data['responseOption']);
        }
    }

    private function _addValidators($element, $validators)
    {
        foreach ($validators as $validator)
        {
            switch ($validator['FQV_TypeID'])
            {
                case 1:
                    if ($validator['FQV_Value'] == 1)
                    {
                        $element->addValidator(
                            'NotEmpty',
                            true,
                            array(
                                'messages' => array(
                                    'isEmpty' => $this->getView()->getCibleText('error_field_required')
                                )
                            )
                        )
                        ->setRequired(true);

                        /* jQuery validate Required */
                        $element->setAttrib('class', $element->getAttrib('class') . ' {validate:{required:true}}');
                        $element->setAttrib('title', $this->getView()->getCibleText('error_field_required'));
                    }
                    break;
                case 2:

                    break;
                case 3:

                    break;
                case 4:

                    break;
                case 5:

                    break;
                case 6:
                    $element->setAttrib(
                        'class',
                        'phone_format mediumTextInput');

                    break;
                case 7:
                    $element->setAttrib(
                        'class',
                        'postalCode_format smallTextInput');

                    break;
                case 8:

                    break;
                case 9:

                    break;
                case 11:

                    break;
                case 12:

                    break;
                case 13:

                    break;
                case 14:

                    break;
                default:
                    break;
            }
        }
    }

    private function _addOptions($element, $options)
    {

    }

    private function _addResponseOption($element, $optionsList)
    {
        $defaultValues = array();

        foreach ($optionsList as $list)
        {
            switch ($list['FRO_Type'])
            {
                case 'select':
                    $element->addMultiOption('', $this->getView()->getClientText('form_detail_label_choose'));

                default:
                    if ($list['FRO_Default'] > 0)
                        $defaultValues[] = $list['FRO_ID'];

                    $element->addMultiOption(
                        $list['FRO_ID'],
                        $list['FROI_Label']
                    );

                    if ($list['FRO_Other'] > 0 )
                    {
                        $idLabel = $element->getName()
                                    . '-' . $list['FRO_ID'];

                        $nameLabel = $element->getName() . '['.$list['FRO_ID'].'0]';

                        $detailLabel = $this->getView()->getCibleText(
                                'form_detail_label_for_response_options');

                        $script = <<< EOS

                        $('#detail_{$idLabel}').live('click', function(){
                            $('#{$idLabel}').attr('checked', 'checked');
                        })
                        if($('#{$idLabel}').attr('checked'))
                        {
                            $('label[for={$idLabel}]').after('<br /><span class="detailOptions">{$detailLabel}&nbsp;<span class="field_required">*</span>&nbsp;&nbsp;<input type="text" id="detail_{$idLabel}" name="{$nameLabel}" class="{validate:{required:true}}" title="{$this->getView()->getCibleText('error_field_required')}"/></span>');
                        }
                        $('#{$idLabel}').live('click', function(){

                            if ($(this).is(':checked') && $('#detail_{$idLabel}').attr('id'))
                            {
                                $('#detail_{$idLabel}').focus();

                            }else if($(this).not(':checked') && $('#detail_{$idLabel}').attr('id'))
                            {
                                $('#detail_{$idLabel}').parent('span.detailOptions').next('br').remove();
                                $('#detail_{$idLabel}').parent('span.detailOptions').remove();
                            }
                            else
                            {
                                $('#{$idLabel}').attr('checked', 'checked');
                                $('label[for={$idLabel}]').after('<br /><span class="detailOptions">{$detailLabel}&nbsp;<span class="field_required">*</span>&nbsp;&nbsp;<input type="text" id="detail_{$idLabel}" name="{$nameLabel}" class="{validate:{required:true}}" title="{$this->getView()->getCibleText('error_field_required')}"/></span>');
                                $('#detail_{$idLabel}').focus();
                            }
                        })

                        $('input[name^=SingleChoice]').change(function(){
                            var test = $('#{$idLabel}').is(':checked');

                            if ('input[id^=SingleChoice_]'
                                && $('#detail_{$idLabel}').attr('id')
                                && !test)
                            {
                                $('#detail_{$idLabel}').parent('span.detailOptions').next('br').remove();
                                $('#detail_{$idLabel}').parent('span.detailOptions').remove();
                            }
                        })
EOS;
                        $this->getView()->jQuery()->addOnLoad($script);
                    }

                    break;
            }
        }

        //Set the default values and disable the inArray validation for options.
        $element->setValue($defaultValues)
                ->setRegisterInArrayValidator(false);
    }

    /**
     * Set the path to javascript file and append it to the page.
     *
     * @return void
     */
    private function _setJqueryPluginMask()
    {
        $javascriptFile = $this->getView()->locateFile(
            "jquery.maskedinput.min.js",
            "jquery");
        $this->getView()->jQuery()->addJavascriptFile($javascriptFile);
    }

    /**
     * Set the script to append to the form page for phone number mask.
     * It will interact with jquery plugins.
     *
     * @return string
     */
    private function _setPhoneFormatJs()
    {
        $script =<<< EOS
        $('.phone_format').mask('(999)999-9999? x99999');
        $('.postalCode_format').mask('a9a 9a9');
EOS;

        $this->getView()->jQuery()->addOnLoad($script);
    }

    /**
     * Set the path to javascript file and append it to the page.
     *
     * @return void
     */
    private function _setJqueryPluginValidate()
    {
        $javascriptFile = $this->getView()->locateFile(
            "jquery.validate.js",
            "jquery");
        $this->getView()->jQuery()->addJavascriptFile($javascriptFile);

        $javascriptFile = $this->getView()->locateFile(
            "jquery.metadata.js",
            "jquery");
        $this->getView()->jQuery()->addJavascriptFile($javascriptFile);

        $script = <<< EOS

        $(document).ready(function() {
            var validator = $("#{$this->getAttrib('id')}").validate({
                wrapper: 'span',
                meta: "validate"
            });
        });

EOS;

        $this->getView()->headScript()->appendScript($script);
    }


    /**
     * Defines the javascript function to refresh catcha which will be append
     * to the page.
     *
     * @return void
     */
    private function _setRefreshCaptchaJs()
    {
        $script = <<< EOS

        function refreshCaptcha(id){
            $.getJSON('{$this->_baseDir}/{$this->current_module}/index/captcha-reload',
                function(data){
                    $("dd#dd_captcha img").attr({src : data['url']});
                    $("#"+id).attr({value: data['id']});
            });
        }

EOS;

        $this->getView()->headScript()->appendScript($script);
    }

    private function _addCaptcha()
    {
        $this->_hasCaptcha = true;
        // Captcha
        $this->getView()->FormAddCaptcha($this);
    }

    private function _addSubmitButton()
    {
        // Submit button
        $submit = new Zend_Form_Element_Submit('submit');
        //$submit->setLabel($this->getView()->getCibleText('button_submit'))
        $submit->setLabel('')
                   ->setAttrib('class','subscribeButton-' . Zend_Registry::get("languageSuffix"))
                   ->removeDecorator('DtDdWrapper');
        $submit->addDecorators(array(
            array(array('row'=>'HtmlTag'),array('tag'=>'dd'))
        ));

        $this->addElement($submit);

        // Required fields label
        $requiredFields = new Zend_Form_Element_Hidden('RequiredFields');
        $requiredFields->setLabel('<span class="field_required">*</span> ' . $this->getView()->getCibleText('form_field_required_label'));

        $this->addElement($requiredFields);
    }

    public function hasLoginProfile()
    {
        throw new Exception('Can not find login profile. Not implemented yet.');
    }

    /**
     * Setter for $_setNotification.
     * Enables the notification emails.
     *
     * @param boolean $value Enables the notification sending.
     */
    public function setNotification($value)
    {
        $this->_sendNotification = $value;
//        throw new Exception('Can not set notification emails. Not implemented yet.');
    }
}
?>