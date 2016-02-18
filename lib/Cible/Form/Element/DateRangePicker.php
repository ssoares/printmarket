<?php
    /** Zend_Form_Element_Xhtml **/
    
    class Cible_Form_Element_DateRangePicker extends Zend_Form_Element
    {
         public function init()
        {
            parent::init();
            $this->addDecorator('ViewScript', array(
                'viewScript' => 'DateRange.phtml'
            ));
            
            $this->addPrefixPath('Cible_Validate', 'Cible/Validate', Zend_Form_Element::VALIDATE);
            
            switch( Zend_Registry::get('languageID')){
                case '1':
                    $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-fr.js");
                    break;
                case '2':
                    $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-en.js");
                    break;
                default:
                    $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/localizations/ui.datepicker-fr.js");
                    break;
            }
            
            $this->getView()->headScript()->appendFile("{$this->getView()->baseUrl()}/js/jquery/jquery.maskedinput.min.js");
        }
     
        public function getValue()
        {
            $valueFiltered = parent::getValue();
            $tmp = array();
            if (!empty($valueFiltered)){
                foreach($valueFiltered as $value){
                    $_from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value['from'] ) ? $value['from'] : '';
                    $_to = preg_match('/^\d{4}-\d{2}-\d{2}$/', $value['to'] ) || empty($value['to']) ? $value['to'] : $_from;
                    
                    if( !empty($_from) ){
                        array_push($tmp, array(
                            'from' => $_from,
                            'to' => $_to
                        ));    
                    }
                }
            }
            return $tmp;    
        }
     
        public function setValue($value)
        {
            $tmp = array();
            
            if( is_array($value) ){
                foreach($value as $val){
                    if( empty($val['from']) && empty($val['to']) )
                        continue;
                    else if( empty($val['to']) && (!empty($val['from']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $val['from'] )) )
                        $val['to'] = $val['from'];
                        
                    array_push($tmp, $val);
                }    
            }
            
            return parent::setValue($tmp);
        }
        
        public function isValid($value, $context = null)
        {
            $result = parent::isValid($value, $context);
            
            if($result){

                $_ranges = $this->getValue();
            
                if( is_array( $_ranges ) ){
                    
                    $dateRange_validator = new Cible_Validate_DateRange('YY-mm-dd');
                    $dateRange_validator->setMessages(array(
                        'dateNotYYYY-MM-DD' => Cible_Translation::getCibleText('validation_message_invalid_date_format'),
                        'endDateEarlier'    => Cible_Translation::getCibleText('validation_message_endDate_earlier')
                    ));
                    
                    foreach( $_ranges as $_range ){
                        
                        if( !$dateRange_validator->isValid($_range) ){
                            
                            $this->addErrorMessages( $dateRange_validator->getMessages());

                            $result = false;
                        }
                    }
                }
                
                $errors = $this->getErrorMessages();
                $this->setErrorMessages( array_unique($errors) );
            }
            
            return $result;
            
        }
    }
?>
