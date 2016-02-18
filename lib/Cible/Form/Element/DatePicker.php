<?php
    /** Zend_Form_Element_Xhtml **/
    
    class Cible_Form_Element_DatePicker extends ZendX_JQuery_Form_Element_DatePicker
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
                case '1':                                                                                                                                                          
                    $this->getView()->jQuery()->addJavascriptFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-fr.js");
                    break;
                case '2':
                    $this->getView()->jQuery()->addJavascriptFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-en.js");
                    break;
                default:
                    $this->getView()->jQuery()->addJavascriptFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-fr.js");
                    break;
            }
        }
    }
?>
