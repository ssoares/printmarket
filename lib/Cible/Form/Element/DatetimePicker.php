<?php
/** Zend_Form_Element_Xhtml **/

class Cible_Form_Element_DatetimePicker extends ZendX_JQuery_Form_Element_DatetimePicker
{
     /**
     * Constructor
     *
     * $spec may be:
     * - string: name of element
     * - array: options with which to configure element
     * - Zend_Config: Zend_Config with options for configuring element
     *
     * @param  string|array|Zend_Config $spec
     * @return void
     * @throws Zend_Form_Exception if no element name after initialization
     */
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);

        if( !empty($options['jquery.params']) )
            $this->jQueryParams = $options['jquery.params'];

        switch( Zend_Registry::get('languageID')){
            case '2':
                $file = $this->getView()->locateFile('ui.datepicker-en.js', 'jquery/localizations');
                break;
            default:
                $file = $this->getView()->locateFile('ui.datepicker-fr.js', 'jquery/localizations');
                break;
        }
        $this->getView()->jQuery()->addJavascriptFile($this->getView()->locateFile('jquery-ui-timepicker.js', 'jquery'));
        $this->getView()->jQuery()->addJavascriptFile($file);
    }
}
