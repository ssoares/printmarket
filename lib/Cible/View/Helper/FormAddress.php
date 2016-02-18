<?php
/**
 * Edith: Cible Framework
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2010 Cible solutions (http://www.ciblesolutions.com)
 * @license
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "Address" element
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2010 Cible solutions (http://www.ciblesolutions.com)
 * @license
 */
class Cible_View_Helper_FormAddress extends Zend_View_Helper_FormElement
{
    const NB_FIELDS        = 16;
    const FIELD_START      = 0;
    const REQ_FIELD_START  = 16;
    const DISP_FIELD_START = 31;

    protected $_name          = 'AI_Name';
    protected $_email         = 'A_Email';
    protected $_country       = 'A_CountryId';
    protected $_state         = 'A_StateId';
    protected $_city          = 'A_CityId';
    protected $_cityTxt       = 'A_CityTextValue';
    protected $_stateTxt      = 'A_StateTextValue';
    protected $_zipCode       = 'A_ZipCode';
    protected $_firstAddress  = 'AI_FirstAddress';
    protected $_secondAddress = 'AI_SecondAddress';
    protected $_firstTel      = 'AI_FirstTel';
    protected $_firstExt      = 'AI_FirstExt';
    protected $_secondTel     = 'AI_SecondTel';
    protected $_secondExt     = 'AI_SecondExt';
    protected $_fax           = 'A_Fax';
    protected $_webSite       = 'AI_WebSite';

    protected $_nameRequired          = true;
    protected $_emailRequired         = false;
    protected $_countryRequired       = true;
    protected $_stateRequired         = true;
    protected $_cityRequired          = true;
    protected $_cityTxtRequired       = true;
    protected $_stateTxtRequired      = false;
    protected $_zipCodeRequired       = true;
    protected $_firstAddressRequired  = true;
    protected $_secondAddressRequired = false;
    protected $_firstTelRequired      = true;
    protected $_firstExtRequired      = false;
    protected $_secondTelRequired     = false;
    protected $_secondExtRequired     = false;
    protected $_faxRequired           = false;
    protected $_webSiteRequired       = false;

    protected $_nameDisplay          = true;
    protected $_emailDisplay         = true;
    protected $_countryDisplay       = true;
    protected $_stateDisplay         = true;
    protected $_cityDisplay          = true;
    protected $_cityTxtDisplay       = true;
    protected $_stateTxtDisplay      = false;
    protected $_zipCodeDisplay       = true;
    protected $_firstAddressDisplay  = true;
    protected $_secondAddressDisplay = true;
    protected $_firstTelDisplay      = true;
    protected $_firstExtDisplay      = true;
    protected $_secondTelDisplay     = true;
    protected $_secondExtDisplay     = true;
    protected $_faxDisplay           = true;
    protected $_webSiteDisplay       = false;

    protected $_nameSequence          = 1;
    protected $_emailSequence         = 2;
    protected $_stateSequence         = 3;
    protected $_citySequence          = 4;
    protected $_cityTxtSequence       = 5;
    protected $_stateTxtSequence      = 6;
    protected $_countrySequence       = 7;
    protected $_zipCodeSequence       = 8;
    protected $_firstAddressSequence  = 9;
    protected $_secondAddressSequence = 10;
    protected $_firstTelSequence      = 11;
    protected $_firstExtSequence      = 12;
    protected $_secondTelSequence     = 13;
    protected $_secondExtSequence     = 14;
    protected $_faxSequence           = 15;
    protected $_webSiteSequence       = 16;

    protected $_cityDefaultVal    = 0;
    protected $_stateDefaultVal   = 11;
    protected $_countryDefaultVal = 7;

    protected $_propertiesList = array();
    protected $_fieldsValue    = array();
    protected $_isSetMask      = false;
    protected $_parentForm     = '';
    protected $_addScript      = true;
    protected $_addScriptState = true;
    protected $_script;
    protected $_form;
    protected $_isXmlHttpRequest = false;
    protected $_labelCSS;

    /**
     * Class cosntructor. Set the form if defined and other properties.
     *
     * @param Zend_Form $form    The form which we will add address fields.
     * @param array     $options An array of properties to set.
     *
     * @return void
     */
    public function  __construct(Zend_Form $form = null, array $options = array())
    {
        if (Zend_Registry::isRegistered('isXmlHttpRequest'))
            $this->_isXmlHttpRequest = Zend_Registry::get('isXmlHttpRequest');

        $this->_propertiesList = get_class_vars(get_class());

        if ($form)
            $this->_form = $form;

        if (count($options) > 0)
            $this->_setProperties($options);

        $this->view = $this->_form->getView();

        if (Zend_Registry::isRegistered('numberMask'))
            $this->_isSetMask = Zend_Registry::get('numberMask');
    }

    /**
     * Setter for the form. This is to add required element to the form in
     * order to build adress fields.
     *
     * @param array $_fieldsValue
     */
    public function setForm($form)
    {
        $this->_form = $form;
    }

    /**
     * Setter for fieldsValue
     *
     * @param array $_fieldsValue
     */
    public function setFieldsValue($fieldsValue)
    {
        $this->_fieldsValue = $fieldsValue;
    }

    /**
     * Set if the field is required or not.<br />
     * Default value are set for each property.
     *
     * @param srting $property Name of the property.
     * @param Mixed  $value    Value to set.
     *
     * @return void
     */
    public function requiredProperty($property, $value)
    {
        $propName = '_' . $property . 'Required';

        $this->$propName = $value;
    }

    /**
     * Set any class property value.<br />
     *
     * @param srting $property Name of the property.
     * @param Mixed  $value    Value to set.
     *
     * @return void
     */
    public function setProperty($property, $value)
    {
        if(substr($property,0,1) != '_')
            $propName = '_' . $property;
        else
            $propName = $property;

        $this->$propName = $value;
    }

    /**
     * Set the field sequence.<br />
     * It defines the display order. By default it's defined by the order
     * which they are registered.
     *
     * @param srting $property Name of the property.
     * @param Mixed  $value    Value to set.
     *
     * @return void
     */
    public function setSequence($property, $value)
    {
        if(substr($property,0,1) != '_')
            $propName = '_' . $property . 'Sequence';
        else
            $propName = $property;

        $this->$propName = $value;
    }

    /**
     * Set the default value of the country, state and city dropdown list.<br />
     * Default values are:<br />
     *  country = 7 (Canada)<br />
     *  state = 11(Quebec) and<br />
     *  city = 0
     *
     * @param srting $property Name of the property.
     * @param Mixed  $value    Value to set.
     *
     * @return void
     */
    public function setDefaultValue($property, $value)
    {
        if(substr($property,0,1) != '_')
            $propName = '_' . $property . 'DefaultVal';
        else
            $propName = $property;

        if (array_key_exists($propName, $this->_propertiesList))
            $this->$propName = $value;
    }

    /**
     * Set status to display the field.<br />
     * Default value are set for each property.
     *
     * @param srting $property Name of the property.
     * @param Mixed  $value    Value to set.
     *
     * @return void
     */
    public function displayProperty($property, $value)
    {
        if(substr($property,0,1) != '_')
            $propName = '_' . $property . 'Display';
        else
            $propName = $property;

        $this->$propName = $value;
    }

    /**
     * Setter for the parent fieldset name.
     *
     * @param <type> $value
     *
     * @return void
     */
    public function setParentForm($value)
    {
        $this->_parentForm = $value . '-';
    }

    /**
     * Generates a 'Address' element.
     *
     * @access public
     *
     * @return string The element XHTML.
     */
    public function formAddress()
    {
        if(isset($options['values']))
            $this->setFieldsValue($options['values']);

        $fields = array_slice(
                $this->_propertiesList,
                self::FIELD_START,
                self::NB_FIELDS);

        // Add the elements to the form
        foreach ($fields as $property => $value)
        {
            $action = $property . 'Render';
            $this->$action($property);
        }

        if (!empty($this->_script))
            if (!$this->_isXmlHttpRequest)
                $this->view->jQuery()->addOnLoad($this->_script);
            else
                $this->view->inlineScript()->appendScript($this->_script);
    }

    /**
     * Set the dropdown list for the cities
     *
     * @access private
     *
     * @return
     */
    private function _cityRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Select($this->$property);
            $field->setRequired ($this->$req)
                    ->setAttrib('class', 'stdTextInput')
                    ->setRegisterInArrayValidator(false)
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $hiddenSrc = new Zend_Form_Element_Hidden('selectedCity');

            $this->_form->addElement($hiddenSrc);
            $this->_form->addElement($field);
        }

    }
    /**
     * Set an input field for city name. This value is seized by the user.
     *
     * @access private
     *
     * @return void
     */
    private function _cityTxtRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setAttrib('class', 'stdTextInput')
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $this->_addScriptState = false;
            $this->_form->addElement($field);
        }

    }

    /**
     * Set an input field for city name. This value is seized by the user.
     *
     * @access private
     *
     * @return void
     */
    private function _stateTxtRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setAttrib('class', 'stdTextInput')
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $field->setDecorators(array(
                'ViewHelper',
                array('label'),
                array(array('row' => 'HtmlTag'),
                    array('tag' => 'dd', 'class' => 'stateText hidden')),
            ));

            $this->_addScriptState = false;
            $this->_form->addElement($field);
        }

    }

    /**
     * Set the dropdown list for the states
     *
     * @access private
     *
     * @return
     */
    private function _stateRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Select($this->$property);
            $field->setRequired ($this->$req)
                    ->setValue($this->_stateDefaultVal)
                    ->setAttrib('class', 'stdSelect')
                    ->setRegisterInArrayValidator(false)
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);
            if ($this->$req)
            {
                $field->setAttrib('class', 'stdSelect ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $config = Zend_Registry::get('config');
            $defaultStates = $config->address->default->states;
            $hiddenSrc = new Zend_Form_Element_Hidden('selectedState');
            $hiddenSrc->setValue($defaultStates);
            $hiddenSrc->setDecorators(array('ViewHelper'));
            $this->_form->addElement($hiddenSrc);
            $this->_form->addElement($field);
        }

    }

    private function _countryRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $countries = Cible_FunctionsGeneral::getCountries();
            if($this->_addScript){
                $this->jsLocationScript($countries);
            }
            $field = new Zend_Form_Element_Select($this->$property);
            $field->setRequired ($this->$req)
                    ->setAttrib('class', 'stdSelect')
                    ->setValue($this->_countryDefaultVal)
                    ->setRegisterInArrayValidator(false)
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);
            if ($this->$req)
            {
                $field->setAttrib('class', 'stdSelect ' . $req);
                $this->_addRequiredValidator ($field);
            }

            foreach ($countries as $key => $country)
            {
                $field->addMultiOption($country['ID'], $country['name']);
            }

            $this->_form->addElement($field);
        }

    }
    private function _zipCodeRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setAttrib('class', 'stdTextInput zipCode_format')
                ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput zipCode_format ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $this->_form->addElement($field);
            $this->addNumbersMask();
        }

    }

    private function _faxRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setAttrib('class', 'stdTextInput faxNum')
                ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);
            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput faxNum ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $this->_form->addElement($field);
            $this->addNumbersMask();
        }

    }

    private function _emailRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $regexValidate = new Cible_Validate_Email();
            $regexValidate->setMessage($this->view->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setLabel($this->view->getCibleText('form_label' . $property))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->setAttrib('class', 'stdTextInput')
                ->addValidator($regexValidate);

            if($this->$seq)
                $field->setOrder($this->$seq);
            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $this->_form->addElement($field);
        }

    }

    private function _nameRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);

            $field->setRequired ($this->$req)
                    ->setLabel($this->view->getCibleText('form_label' . $property))
                    ->setAttrib('class', 'stdTextInput');

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $this->_form->addElement($field);
        }

    }

    private function _firstAddressRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setAttrib('class', 'stdTextInput')
                ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->_form->addElement($field);
        }

    }

    private function _secondAddressRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setLabel($this->view->getCibleText('form_label' . $property))
                    ->setAttrib('class', 'stdTextInput');


            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }

            $label = $field->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->_form->addElement($field);
        }

    }

    private function _firstTelRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setAttrib('class', 'stdTextInput phoneNum')
                ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);


            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput phoneNum ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
//            $label->setOption('class', $this->_labelCSS);
            $this->_form->addElement($field);
            $this->addNumbersMask();
        }

    }

    private function _firstExtRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
//            $label->setOption('class', $this->_labelCSS);
            $label->setOption('class', 'firstExt');
            $this->_form->addElement($field);
        }

    }

    private function _secondTelRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                ->setAttrib('class', 'stdTextInput phoneNum ')
                ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput phoneNum ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
//            $label->setOption('class', $this->_labelCSS);
            $this->_form->addElement($field);
            $this->addNumbersMask();
        }

    }

    private function _secondExtRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setLabel($this->view->getCibleText('form_label' . $property));

            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
//            $label->setOption('class', $this->_labelCSS);
            $label->setOption('class', 'secondExt');
            $this->_form->addElement($field);
        }

    }

    private function _webSiteRender($property)
    {
        $disp = $property . 'Display';
        $req  = $property . 'Required';
        $seq  = $property . 'Sequence';

        if($this->$disp)
        {
            $field = new Zend_Form_Element_Text($this->$property);
            $field->setRequired ($this->$req)
                    ->setLabel($this->view->getCibleText('form_label' . $property))
                    ->setAttrib('class', 'stdTextInput');


            if($this->$seq)
                $field->setOrder($this->$seq);

            if ($this->$req)
            {
                $field->setAttrib('class', 'stdTextInput ' . $req);
                $this->_addRequiredValidator ($field);
            }
            $label = $field->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->_form->addElement($field);
        }

    }


    /**
     * Prepend a javascript script in order to manage dropdown lists of
     * countries, states and cities.
     * Also add mask for numbers format.
     *
     * @return void
     */
    public function jsLocationScript($countries)
    {
        $idPrefix = '';
        if($this->_form instanceof  Cible_Form_SubForm || $this->_form instanceof  Zend_Form_SubForm)
        {
            $idPrefix = $this->_parentForm . $this->_form->getName() . '-';
        }

        $index = 0;
        $tmp   = 0;
//        $tmp   = "var selectedCity  = new Array();\r\n";

        if(Zend_Registry::isRegistered('nbAddr'))
        {
            $index = Zend_Registry::get('nbAddr');
        }
        if (!$index)
        {
            $tmp  = "var selectedState = 0;\r\n";
            $tmp .= "var selectedCity  = 0;\r\n";
            $tmp .= "if($('input[id$=selectedState]').length){\r\n";
            $tmp .= "    selectedState = ($('input[id$=selectedState]').val()).split('||');}\r\n";
            $tmp .= "if($('input[id$=selectedCity]').length){\r\n";
            $tmp .= "    selectedCity  = ($('input[id$=selectedCity]').val()).split('||');}\r\n";
        }
        $langId = Zend_Registry::get('languageID');
        $onLive = SESSIONNAME == 'application' ? 'on' : 'live';
//        $countries      = Cible_FunctionsGeneral::getCountries();
//        $json_countries = json_encode($countries);
//        var countries = {$json_countries};
        $script =<<< EOS
        var langId = {$langId};
        {$tmp}
        $('select[id$={$idPrefix}{$this->_country}]').{$onLive}('change',function()
        {
            var ctl_states = $('select[id$={$idPrefix}{$this->_state}]')
            var ctl_cities = $('select[id$={$idPrefix}{$this->_city}]')
            var states_list = [];
            ctl_states.empty();
            ctl_cities.empty();

            $.getJSON(
                '{$this->view->baseUrl()}/default/index/ajax-states/countryId/' + $(this).val() + '/langId/' + langId,
                function(states_list){
                    $('<option value="-1" label="">{$this->view->getCibleText('form_select_default_label')}</option>').appendTo(ctl_states);
                    $.each(states_list, function(i, item){
                        if(selectedState[{$index}] == item.id){
                            $('<option value="'+item.id+'" label="'+item.name+'" selected="selected">'+item.name+'</option>').appendTo(ctl_states);
                            $('#selectedState').val('');
                            selectedState[{$index}] = '';
                        }
                        else
                            $('<option value="'+item.id+'" label="'+item.name+'">'+item.name+'</option>').appendTo(ctl_states);

                    });

                    ctl_states.trigger('cible.modification');
                    if ($('select[id$={$idPrefix}{$this->_state}] option').length < 2)
                    {
                        ctl_states.parent('dd').addClass('hidden');
                        ctl_states.attr('disabled', 'disabled');
                        $('label[for={$idPrefix}{$this->_state}]').parent('dt').addClass('hidden');
                        $('select[id$={$idPrefix}{$this->_stateTxt}]').parent('dd').removeClass('hidden');
                    }
                    else
                    {
                        ctl_states.parent('dd').removeClass('hidden');
                        ctl_states.removeAttr('disabled');
                        $('label[for={$idPrefix}{$this->_state}]').parent('dt').removeClass('hidden');
                        $('select[id$={$idPrefix}{$this->_stateTxt}]').parent('dd').addClass('hidden');
                    }
                }
            );
        }).change();\r\n
EOS;
        if ($this->_addScriptState)
        {
            $script .=<<<EOS
            $('select[id$={$idPrefix}{$this->_state}]').{$onLive}('change', function()
            {
                var ctl_cities = $('select[id$={$idPrefix}{$this->_city}]');
                ctl_cities.empty();
                var stateId = $(this).val();

                var bool = selectedState[{$index}] || 0;

                if (bool)
                    stateId = selectedState[{$index}];
                else
                    stateId = $(this).val();

                if (selectedCity.length == 1 && {$index} > 0)
                    thisCity = selectedCity[0];
                else
                    thisCity = selectedCity[{$index}];

                $.getJSON('{$this->view->baseUrl()}/default/index/ajax-cities/stateId/' + stateId,
                    function(data)
                    {
                        $('<option value="" label="">{$this->view->getCibleText('form_label_select_city')}</option>').appendTo(ctl_cities);
                        $.each(data, function(i, item){
                            if(thisCity == item.C_ID){
                                $('<option value="'+item.C_ID+'" label="'+item.C_Name+'" selected="selected">'+item.C_Name+'</option>').appendTo(ctl_cities);
                                $('#selectedCity').val('');
                            }
                            else
                                $('<option value="'+item.C_ID+'" label="'+item.C_Name+'">'+item.C_Name+'</option>').appendTo(ctl_cities);
                        });
                });
            }).change();
EOS;
        }
            Zend_Registry::set('nbAddr', $index + 1);

            $this->_script .= $script;

    }

    /**
     * Add the js script to display number marsk.
     *
     * @return void
     */
    public function addNumbersMask()
    {
        if (!$this->_isSetMask)
        {
            $idPrefix= '';
            if($this->_form instanceof  Zend_Form_SubForm)
            {
                $idPrefix = $this->_form;
            }

            $this->view->jQuery()->addJavascriptFile("{$this->view->baseUrl()}/js/jquery/jquery.maskedinput.min.js");

            $script =<<< EOS
//            $('.zipCode_format').mask('a9a 9a9');
//            $('.phoneNum').mask('(999) 999-9999');
//            $('.faxNum').mask('(999) 999-9999');
//            $('.phoneFree').mask('1-999-999-9999');
//            $('#fax').mask('(999)999-9999');
EOS;
            $this->_script    .= $script;
            $this->_isSetMask = true;
            Zend_Registry::set('numberMask', true);
        }
    }

    /**
     * Sets class properties values when the options array is defined.
     *
     * @param array $options
     *
     * @return void
     */
    private function _setProperties(array $options = array())
    {
        foreach ($options as $key => $array)
        {
            if(count($array) > 0)
            {
                switch ($key)
                {
                    case 'display':
                        foreach ($array as $property => $value)
                        {
                            if($property == 'all')
                                $this->disableAll();
                            else
                                $this->displayProperty($property, $value);
                        }
                        break;
                    case 'required':
                        foreach ($array as $property => $value)
                        {
                            $this->requiredProperty($property, $value);
                        }
                        break;
                    case 'fieldsValue':
                        $this->setFieldsValue($array);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    /**
     * Set to false all the display properties.
     * No fild will be added to the form
     *
     * @return void
     */
    public function disableAll()
    {
        $properties = $this->_setFieldsList(self::DISP_FIELD_START, self::NB_FIELDS);

        foreach ($properties as $property => $value)
        {
            $this->displayProperty($property, false);
        }
    }

    /**
     * Defines the fields to display.
     * This allows to split the address fields in different parts of the form.
     *
     * @param array $fieldsList List of the fields to display.
     *
     * @return void
     */
    public function enableFields(array $fieldsList)
    {
        $nbPrevFields = 0;

        if (count($fieldsList) > 0)
        {
            $this->disableAll();

            $prevFields   = $this->_form->getElements();
            $nbPrevFields = count($prevFields);
            if (!$this->_isAssociativeArray($fieldsList))
            {
                foreach ($fieldsList as $property)
                {
                    $this->displayProperty($property, true);
                    $this->setSequence($property, $nbPrevFields + 1);
                    $nbPrevFields++;
                }
            }
            else
            {
                foreach ($fieldsList as $property => $value)
                {
                    $this->displayProperty($property, true);
                    $this->setSequence($property, $nbPrevFields + 1);

                    $this->requiredProperty($property, $value);
                    $nbPrevFields++;
                }
            }
        }
    }

    private function _setFieldsList($start, $length)
    {
        $properties = array_slice(
                $this->_propertiesList,
                $start,
                $length);

        return $properties;
    }
    private function _addRequiredValidator($field)
    {
        $field->addValidator(
            'NotEmpty',
            true,
            array(
                'messages' => array(
                    'isEmpty' => $this->view->getCibleText('validation_message_empty_field'))
            )
        );
    }

    /**
     * Insert a checkcbox to activate data copy if checked. <br />
     * The billing address data will be utilized for db insert.
     *
     * @param Zend_Form_SubForm $billingSubForm The subForm containing the
     *                          address fields to duplicate.
     *
     * @return void
     */
    public function duplicateAddress($billingSubForm)
    {
        if ($billingSubForm instanceof Zend_Form_SubForm || $billingSubForm instanceof Cible_Form_SubForm)
        {
            $name = $this->_parentForm . $billingSubForm->getName();

            if (empty($name))
                throw new Exception('Please, set this subform name in order to add the checkbox to duplicate address.');

            $duplicateAddr = new Zend_Form_Element_Checkbox('duplicate');
            $duplicateAddr->setLabel($this->view->getCibleText('form_account_duplicate_address_label'))
                ->setAttrib('checked', 'checked');
            $duplicateAddr->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'),
                    array('tag' => 'dd', 'class' => 'label_after_checkbox duplicate')),
            ));

            $this->_form->addElement($duplicateAddr);

            $this->_addJsActions($name, 'duplicate');
        }
        else
        {
            throw new Exception('The parameter is not an instance of Zend_Form_SubForm or Cible_form_SubForm');
        }

    }
    /**
     * Insert radio buttons to activate data copy if checked. <br />
     * Depending on the radio checked, the address used will be extracted from
     * the profile or from a temporary table or empty.
     *
     * @param Zend_Form_SubForm $addressSubForm The subForm containing the
     *                                          address fields to duplicate.
     * @param Array             $options        Options to defined the radio buttons.
     *
     * @return void
     */
    public function addressSource($addressSubForm, $options = array())
    {
        if ($addressSubForm instanceof Zend_Form_SubForm || $addressSubForm instanceof Cible_Form_SubForm)
        {
            $name = $this->_parentForm . $addressSubForm->getName();

            if (empty($name))
                throw new Exception('Please, set this subform name in order to add the checkbox to duplicate address.');

            $addrSource = new Zend_Form_Element_Radio('addrSource');
            $addrSource->addMultiOptions($options['choices']);
            $addrSource->setValue($options['default']);
            $addrSource->setDecorators(array(
                'ViewHelper',
                array(array('row' => 'HtmlTag'),
                    array('tag' => 'dd', 'class' => 'addrSource label_after_checkbox')),
            ));

            $this->_form->addElement($addrSource);

            $this->_addJsActions($name . '-addrSource', 'addrSource');
        }
        else
    {
            throw new Exception('The parameter is not an instance of Zend_Form_SubForm or Cible_form_SubForm');
        }

    }

    private function _addJsActions($prefix = "", $type = "")
    {
        $script = "";
        $onLive = SESSIONNAME == 'application' ? 'on' : 'live';
        switch ($type)
        {
            case 'duplicate':
        $script =<<< EOS
        $(window).load(function(){
                    if ($('input[id$=-{$type}]').is(':checked'))
            {
                        var dl = $('input[id$=-{$type}]').parents('dl:first')
                dl.children('dd:not(:first)').hide();
                dl.children('dt').hide();
            }
        });

                $('input[id$=-{$type}]').click(function(){
            if ($(this).is(':checked'))
            {
                var dl = $(this).parents('dl:first')
                dl.children('dd:not(:first)').fadeOut();
                dl.children('dt').fadeOut();
            }
            else
            {
                var dl = $(this).parents('dl:first')
                dl.children(':not(dd:first)').fadeIn();
                dl.children('dt').fadeIn();
            }
        });
EOS;
                break;
            case 'addrSource':
                $script =<<< EOS
                var address = 0;
                var emptyStyle = {
                    backgroundColor:"",
                    color : "",
                    'font-style': ""
                };
                var style = {
                    backgroundColor:"#fff",
//                    border : 0,
                    color : "#bbb",
                    'font-style': "italic"
                };
                $(window).load(function(){
                    if ($('input[id={$prefix}-1]').is(':checked'))
                    {
                        var dl = $('input[id^={$prefix}]').parents('dl:first')
                        var inputs = dl.children().children('input[type=text]');
                        var selects = dl.children().children('select');
                        inputs.attr('disabled', 'disabled');
                        selects.attr('disabled', 'disabled');
                        inputs.css(style);
                        selects.css(style);
                        address = dl.clone();
                        $('input[id={$prefix}-1]').click();
                    }
                });

                $('input[id^={$prefix}]').{$onLive}('click',function(){
                    var dl = $('input[id^={$prefix}]').parents('dl:first')
                    var inputs = dl.children().children('input[type=text]');
                    var selects = dl.children().children('select');

                    if ($(this).val() == 2)
                    {
                        inputs.val("");
                        inputs.removeAttr('disabled');
                        selects.removeAttr('disabled');
                        inputs.css(emptyStyle);
                        selects.css(emptyStyle);
                        if ($(this).val() == 2)
                            inputs[0].focus();
                    }
                    else
                    {
                        if ($(this).val() == 3)
                            inputs.val("");
                        inputs.attr('disabled', 'disabled');
                        selects.attr('disabled', 'disabled');
                        inputs.css(style);
                        selects.css(style);
                        if ($(this).val() == 1)
                        {
                            $('input[id={$prefix}-1]').focus();
                            address.children('input[type=radio]:first').attr('checked');
                            dl.html(address.children());
                            dl.children('input[type=radio]:first').focus();
                            address = dl.clone();
                        }
                    }
                });
EOS;
                break;

            default:
                break;
        }

        $this->_script .= $script;
    }

    /**
     * Test if the array is an associative array or just contains fields list.
     *
     * @param array $array
     *
     * @return boolean
     */
    private function _isAssociativeArray(array $array = array())
    {
        $isAssociative = false;
        $keysVal       = array();

        $fieldsList = $this->_setFieldsList(self::FIELD_START, self::NB_FIELDS);

        foreach ($array as $key => $value)
        {
            $field = '_' . $key;
            if (array_key_exists($field, $fieldsList))
                $keysVal[] = $field;
        }

        if(count($keysVal) > 0)
            $isAssociative = true;

        return $isAssociative;
    }
}