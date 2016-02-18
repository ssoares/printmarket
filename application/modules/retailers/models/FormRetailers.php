<?php
    class FormRetailers extends Cible_Form 
    {
        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            parent::__construct($options);
            $baseDir = $this->getView()->baseUrl();
            
            $countries = Cible_FunctionsGeneral::getCountries();
            $langId    = Zend_Registry::get('languageID');
            
            $json_countries =  json_encode($countries);
            
            $script =<<< EOS
            var countries = {$json_countries};
            var langId = {$langId};
            var first  = 1;
            
            $(window).load(function(){
//                $('#country').val('7');
            });

            $('#country').change(function(){
                var ctl_states = $('#state')
                var ctl_cities = $('#city')
                ctl_states.empty();
                ctl_cities.empty();
                if(first)
                {
                    $('#country').val('7');
                    $('#state').val('11');
                }
                $('#retailers-list').html('');

                $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-retailers/field/countryId/value/' + $(this).val() + '/langId/' + langId,
                    function(data){

                        $('#retailers-list').html(data);
                });
                $.getJSON(
                    '{$this->getView()->baseUrl()}/retailers/index/ajax-states/countryId/' + $(this).val() + '/langId/' + langId,
                     function(states_list){
                        $('<option value="" label="">{$this->getView()->getCibleText('form_label_select_state')}</option>').appendTo(ctl_states);
                        $.each(states_list, function(i, item){
                            if($('#selectedState').val() == item.id){

                                $('<option value="'+item.id+'" label="'+item.name+'" selected="selected">'+item.name+'</option>').appendTo(ctl_states);
                                $('#selectedState').val('');
                            }
                            else if(first){                         
                                if(item.id==11){
                                    $('<option value="'+item.id+'" label="'+item.name+'" selected="selected">'+item.name+'</option>').appendTo(ctl_states);
                                    $('#selectedState').val('');
                                }
                                else{
                                    $('<option value="'+item.id+'" label="'+item.name+'">'+item.name+'</option>').appendTo(ctl_states);
                                }
                            }
                            else
                                $('<option value="'+item.id+'" label="'+item.name+'">'+item.name+'</option>').appendTo(ctl_states);

                        });
                        
                        
                    });
                     
                }).change();
             
            $('#state').change(function(){               
                var ctl_cities = $('#city');
                ctl_cities.empty();
                $('#retailers-list').html('');
                var stateId = $(this).val();
                if ($('#selectedState').val() != '')
                    stateId = $('#selectedState').val();
             // alert(first);
               if(first){
                    first = 0;
                    stateId = 11;                        
                }
                if(stateId)
                {                    
                    $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-retailers/field/stateId/value/' + stateId + '/langId/' + langId,
                        function(data){

                        $('#retailers-list').html(data);
                    });

                    $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-cities/stateId/' + stateId + '/filter/1',
                        function(data){
                            $('<option value="" label="">{$this->getView()->getCibleText('form_label_select_city')}</option>').appendTo(ctl_cities);
                            $.each(data, function(i, item){
                                if($('#selectedCity').val() == item.C_ID)
                                {
                                    $('<option value="'+item.C_ID+'" label="'+item.C_Name+'" selected="selected">'+item.C_Name+'</option>').appendTo(ctl_cities);
                                    $('#selectedCity').val('');
                                }
                                else
                                    $('<option value="'+item.C_ID+'" label="'+item.C_Name+'">'+item.C_Name+'</option>').appendTo(ctl_cities);
                            });
                        });
                        
                }
                 
            }).change();
            
            $('#city').change(function(){
                
                $('#retailers-list').html('');
                idCity = $('#selectedCity').val();
                if ($('#selectedCity').val() == '')
                    idCity = $(this).val();

                $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-retailers/field/cityId/value/' + idCity + '/langId/' + langId,
                    function(data){
                        
                        $('#retailers-list').html(data);                        
                });
                
            });
            
        
EOS;

            $this->getView()->jQuery()->addOnLoad($script);
            
            
            // Country
            $country = new Zend_Form_Element_Select('country');
            $country->setLabel($this->getView()->getCibleText('form_label_country'))
                    ->setAttrib('class', 'stdSelect');
               
            foreach($countries as $_country)
                $country->addMultiOption($_country['ID'], $_country['name']);

            $this->addElement($country);
            
            // State
            $state = new Zend_Form_Element_Select('state');
            $state->setLabel($this->getView()->getCibleText('form_label_state'))
                  ->setAttrib('class','stdSelect');
            
            $this->addElement($state);
            
            // City
            $city = new Zend_Form_Element_Select('city');
            $city->setLabel($this->getView()->getCibleText('form_label_city'))
                  ->setAttrib('class','stdSelect');

            $city->addMultiOption('', $this->getView()->getCibleText('form_label_select_city'));
            
            $this->addElement($city);
            
            $this->addAttribs(array('id' => 'formDetaillants'));
        }
    }
?>
