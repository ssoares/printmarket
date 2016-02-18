<?php $this->headScript()->appendFile($this->baseUrl().'/js/csa/overlay.js'); ?>
<style>

    #page_edit { position: fixed; display: none; z-index: 100001; background-color: #fff; border: solid 1px #666 }
    #page_edit #page_action { background-color: #666; color: #fff; font-weight: bold; text-align: right; line-height: 25px;}
    #page_edit #page_action a { color: #fff }
    #page_edit #page_content { overflow: auto; width: 700px;}

</style>

<script>
    //$(document).ready(function() {
        function dump(arr,level) {
            var dumped_text = "";
            if(!level) level = 0;

            if(level == 1)
                return;
            //The padding given at the beginning of the line.
            var level_padding = "";
            for(var j=0;j<level+1;j++) level_padding += "    ";

            if(typeof(arr) == "object") { //Array/Hashes/Objects
             for(var item in arr) {
              var value = arr[item];

              if(typeof(value) == "object") { //If it is an array,
               dumped_text += level_padding + "\'" + item + "\' ...\n";
               dumped_text += dump(value,level+1);
              } else {
               dumped_text += level_padding + "\'" + item + "\' => \"" + value + "\"\n";
              }
             }
            } else { //Stings/Chars/Numbers etc.
             dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
            }
            return dumped_text;
        }

        function pretty_header_format(){
            var actions_panel = $('#page_edit #fieldset-actions');
            var language_switcher = $('#page_edit #language-switcher');
            var header_height = $('#page_edit #header').height() + 10;

            actions_panel.css('top', header_height);
            language_switcher.css('top', header_height);

            return header_height;
        }

        function ajustPopUp(){
            var window_height = $(window).height();
            var window_width = $(window).width();

            var page_edit = $('#page_edit');
            var page_content = $('#page_content');

            // Popup has been loaded, we grab the height and width
            var page_height = page_edit.height();
            var page_width = page_edit.width();
            var page_content_width = page_width;

            // We define the acceptable height for our popup

            //var acceptable_popup_height = window_height - (window_height / 3);
            var min_popup_height = 200;
            var max_popup_height = Math.round(window_height - (window_height / 5));


            var min_popup_width = 700;
            var max_popup_width = Math.round(window_width - (window_width / 5));
            // if page_height is greater than acceptable_height, we use acceptable height instead
            /*
            if( page_height > acceptable_popup_height ){
                page_height = acceptable_popup_height;
            }
            */
            if(page_width < min_popup_width){
                page_width = min_popup_width;
                page_content_width = min_popup_width;
            }
            else if(page_width > max_popup_width){
                page_width = max_popup_width;
                page_content_width = max_popup_width;
            }

            if(page_height < min_popup_height){
                page_height = min_popup_height;
            }
            else if(page_height > max_popup_height){
                page_height = max_popup_height;
                //page_content_width = page_content_width - 25;
            }

            page_content_width = page_content_width - 25;
            //alert(max_popup_width);

            // we set the css top attribute for our popup
            var page_edit_top = Math.round((window_height - page_height ) / 2);

            // we set our popup size and position
            page_content.css({
                'width': page_content_width + 'px',
                'background-color' : 'white',
                'padding-right' : '0px'

            });

            page_edit.css({
                'top': page_edit_top + 'px',
                'left': Math.round((window_width - 700) / 2) + 'px',
                'width': page_width + 'px'
            }).slideDown('slow');

            // we find out the space the header is using since some components are position absolutely
            header_height = pretty_header_format();

            // we substract the header height from the popup height so that our scrollable content doesn't oversize the popup
            editpanel_height = page_height - header_height;
            page_content.css('height', editpanel_height + 'px');

            // web grab the actions panel positioned absolute height so that we can move down the content panel
            var actions_panel = $('#page_edit #fieldset-actions');
            page_content.css('margin-top', actions_panel.height() + 'px');
        }

        function getEditPage(url){
            $.get(url, function(data){
                overlay.show();
                loadContent(data, url);
            });
        }

        function loadContent(data, url){
            var page_content = $('#page_content');
            var page_edit = $('#page_edit');
            page_content.css('height', '');
            page_edit.css('height', '');

            $("#topInfo #title").html($("#page_edit #header #title").attr('value'));
            $("#topInfo #description").html($("#page_edit #header #description").attr('value'));
            $("#page_edit #breadcrumb").html($("#page_edit #header #breadcrumb").attr('value'));
            page_content.html(data);

            ajustPopUp();

            catchSubmit(page_content, url);

        }



        function catchSubmit(parent, url){
            var resultPane = $('#result');
            if(resultPane.size() > 0){
                //alert("oui");
                resultPane.click();
                $('#page_edit').slideUp('slow');
                $('#page_content').empty();
                overlay.hide();

            }
            else{
                $('ul#language-switcher a', parent).each(function(){
                    var currentLink = $(this);

                    currentLink.click(function(){
                        var preconfigured_action = '';

                        if( currentLink.attr('href') != 'javascript:void(0);' ){
                            preconfigured_action = currentLink.attr('href');
                            currentLink.data('link', preconfigured_action);
                            currentLink.attr('href','javascript:void(0);');
                        } else {
                            preconfigured_action = currentLink.data('link');
                        }

                        //getEditPage(preconfigured_action);

                        $.get(preconfigured_action, function(data){
                            loadContent(data, preconfigured_action);
                            ajustPopUp();
                        });
                    });

                });

               // Catch cancel
                $('form button#cancel', parent).click(function(event){
                    event.preventDefault();
                    //$(this).attr('onclick', "");
                    //alert($(this).attr('onclick'));
                    $('#page_edit').slideUp('slow');
                    $('#page_content').empty();
                    overlay.hide();
                });

                // Catch submit
                $('form', parent).submit(function(){

                    try {
                        if (tinyMCE) {
                            tinyMCE.triggerSave();
                        }
                    }
                    catch (err) {
                        //alert("n'exsite pas");
                    }

                    $.post(url, $(this).serialize(), function(data){
                        //alert("non");
                        var success = false;
                        loadContent(data, url);
                    });
                    return false;
                });
            }
        }

        overlay = new CSAOverlay(overlay);
    //});


</script>


<?php echo $this->partial('partials/loading.phtml'); ?>

<div id="page_edit">
    <div id="header">
         <input id="title" name="title" type="hidden" value=""/>
         <input id="description" name="description" type="hidden" value=""/>
         <input id="breadcrumb" name="breadcrumb" type="hidden" value=""/>
         <?php
            echo $this->partial('partials/header.pageDetails.phtml', array(
                //'pageTitle' => $this->getCibleText('page_website_sitemap_title'),
                //'pageDescription' => $this->getCibleText('page_website_sitemap_description'),
                'pageTitle' => '&nbsp;',
                'pageDescription' => '&nbsp;',
                'breadcrumb' => '&nbsp;'
            ));
            ?>
    </div>
    <div id="page_content">

    </div>
</div>