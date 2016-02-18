<?php
    class FormRetailersSelectOne extends Cible_Form
    {
        public function __construct($options = null)
        {
            $this->_disabledDefaultActions = true;
            parent::__construct($options);
            $baseDir = $this->getView()->baseUrl();

            $countries = Cible_FunctionsGeneral::getCountries(null, null, true);
            $langId    = Zend_Registry::get('languageID');

            $json_countries =  json_encode($countries);

            $script =<<< EOS
            var countries = {$json_countries};
            var langId = {$langId};

            $('#country').change(function(){
                var ctl_states = $('#state')
                var ctl_cities = $('#city')
                $('#retailer').empty();
                ctl_states.empty();
                ctl_cities.empty();
                $('#retailer-data').html('');

            $.getJSON(
                '{$this->getView()->baseUrl()}/retailers/index/ajax-states/countryId/' + $(this).val() + '/langId/' + langId + '/filter/1',
                 function(states_list){
                    $('<option value="" label="">{$this->getView()->getCibleText('form_label_select_state')}</option>').appendTo(ctl_states);
                    $.each(states_list, function(i, item){
                        if($('#selectedState').val() == item.id){
                            $('<option value="'+item.id+'" label="'+item.name+'" selected="selected">'+item.name+'</option>').appendTo(ctl_states);
                            $('#selectedState').val('');
                        }
                        else
                            $('<option value="'+item.id+'" label="'+item.name+'">'+item.name+'</option>').appendTo(ctl_states);

                    });
                });
            }).change();

            $('#state').change(function(){

                var ctl_cities = $('#city');
                ctl_cities.empty();
                $('#retailer').empty();
                $('#retailer-data').html('');
                stateId = $(this).val();
                if ($('#selectedState').val() != '')
                    var stateId = $('#selectedState').val();

                $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-cities/stateId/' + stateId + '/filter/1',
                    function(data){
                        $('<option value="" label="">{$this->getView()->getCibleText('form_label_select_city')}</option>').appendTo(ctl_cities);
                        $.each(data, function(i, item){
                            if($('#selectedCity').val() == item.C_ID){
                                $('<option value="'+item.C_ID+'" label="'+item.V_Name+'" selected="selected">'+item.V_Name+'</option>').appendTo(ctl_cities);
                                $('#selectedCity').val('');
                            }
                            else
                                $('<option value="'+item.C_ID+'" label="'+item.V_Name+'">'+item.V_Name+'</option>').appendTo(ctl_cities);
                        });

                        if ($('#selectedCity').val())
                            $('#selectedCity').val('');
                });
            }).change();

            $('#city').change(function(){
                var ctl_retailers = $('#retailer');
                ctl_retailers.empty();
                $('#retailer-data').html('');
                idCity = $('#selectedCity').val();
                if ($('#selectedCity').val() == '')
                    idCity = $(this).val();

                $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-retailers/render/false/field/cityId/value/' + idCity + '/langId/' + langId,
                    function(data){

                        $('<option value="" label="">{$this->getView()->getClientText('form_label_select_retailer')}</option>').appendTo(ctl_retailers);

                        $.each(data, function(i, item){
                            $('<option value="'+item.CityID+"-"+item.RetailerId+'" label="'+item.Entreprise1+'">'+item.Entreprise1+'</option>').appendTo(ctl_retailers);

                        });
                });

            }).change();

            $('#retailer').change(function()
            {
                $('#retailer-data').html('');
                var params = ($(this).val()).split('-');
                var cityId = params[0];
                var retailerId = params[1];

                $.getJSON('{$this->getView()->baseUrl()}/retailers/index/ajax-retailers/field/cityId/value/' + cityId + '/retailerId/' + retailerId + '/langId/' + langId,
                    function(data){

                        $('#retailer-data').html(data);
                });

            });


EOS;

            $this->getView()->jQuery()->addOnLoad($script);
            //Textarea for comments
            $comments = new Zend_Form_Element_Textarea('infoComments');
            $comments->setLabel($this->getView()->getClientText('quoteRequest_resume_comments_title'))
                    ->setAttrib('class', '')
                    ->setDecorators(array(
                        'viewHelper',
                        'Errors',
                        array('Label',array('tag'=>'p', 'class' => 'subtitles')),
                        array(array('row'=>'HtmlTag'),array('tag'=>'div', 'id' => 'comments'))
                ));

            $this->addElement($comments);
            // Country
            $country = new Zend_Form_Element_Select('country');
            $country->setLabel($this->getView()->getCibleText('form_label_country'))
                    ->setAttrib('class', 'stdTextInput');

            foreach($countries as $_country)
                $country->addMultiOption($_country['ID'], $_country['name']);

            $this->addElement($country);

            // State
            $state = new Zend_Form_Element_Select('state');
            $state->setLabel($this->getView()->getCibleText('form_label_state'))
                  ->setAttrib('class','stdTextInput');

            $this->addElement($state);

            // City
            $city = new Zend_Form_Element_Select('city');
            $city->setLabel($this->getView()->getCibleText('form_label_city'))
                 ->setAttribs(array('class'=>'stdTextInput'));

            $city->addMultiOption('', $this->getView()->getCibleText('form_label_select_city'));

            $this->addElement($city);


            // Retailers
            $retailer = new Zend_Form_Element_Select('retailer');
            $retailer->setLabel($this->getView()->getCibleText('form_label_choose_retailer'))
                ->setAttrib('class','stdTextInput');
//                ->setRequired(true)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim')
//                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));

            $this->addElement($retailer);

            // Submit button
            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('')
                   ->setAttrib('class','subscribeButton-' . Zend_Registry::get("languageSuffix"));

            $this->addElement($submit);

            $submit->removeDecorator('Label')
                   ->addDecorators(array(
                    array(array('data'=>'HtmlTag'),array('tag'=>'td', 'colspan'=>2, 'class' => 'account-submit'))
            ));

            $this->addAttribs(array('id' => 'formDetaillants'));
        }
    }
?>
