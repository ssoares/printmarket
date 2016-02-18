/**
 * Load and intialize tinymce editor.
 * This will replace a textarea by the tinymce wysiwyg editor.
 *
 * @param string id    Name of the textarea to associate with.
 * @apram string theme Type of editor theme (advanced...)
 *
 * @return void
 */
var loadTinyMce = {
    load: function (id,theme, cssPath){

        tinymce.init({
            // General options
            relative_urls : false,
            remove_script_host : true,
            moxiemanager_image_settings : {
                rootpath: "{$imgContentFolder}"
            },
            moxiemanager_file_settings : {
                rootpath: "{$fileContentFolder}"
            },

            //elements : "{$_id}",
            selector : "#" + id,
            //theme : "{$this->_mode}",
            theme : theme,
            plugins : [
                    "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                    "searchreplace wordcount visualblocks visualchars code fullscreen",
                    "insertdatetime media nonbreaking save table contextmenu directionality",
                    "emoticons template paste textcolor moxiemanager"
                ],
//            language : "{$_lang}",
            moxiemanager_language : "fr",
            language : "fr",

            // Theme options
            menubar: false,
            toolbar1 : "undo,redo,|,bold,italic,underline,strikethrough,|,alignleft aligncenter alignright alignjustify,|,forecolor,backcolor,subscript,superscript,|,",
            toolbar2 : "cut,copy,paste,pastetext,|,searchreplace,|,formatselect,styleselect,fontsizeselect",
            toolbar3 : "bullist,numlist,outdent,indent,|,link,unlink,anchor,|,insertfile,image,media,|,removeformat,code,|,preview,fullscreen,print,",
            toolbar4 : "table,|,hr,nonbreaking,charmap,visualblocks",
            toolbar_items_size:'small',
            image_advtab: true,
            // Style formats
            style_formats : [
                    {title : 'Numéro téléphone', inline : 'span', classes : 'fontSizeXxLarge fourthColor '},
//                    {title : 'Images'},
//                    {title : 'Agrandissement photo', selector : 'img', classes : 'add_prettyphoto'},
//                    {title : 'Tableaux'},
//                    {title : 'Cellule fond blanc', block : 'td', classes : 'fondBlanc'},
//                    {title : 'Bouton'},
//                    {title : 'Bouton lien', block : 'div', classes : 'grayish-button2'},
                    {title : 'Padding'},
                    {title : 'Padding 5 haut et bas', block : 'p', classes : 'paddingTopBottom'}
            ],

            formats : {
//                        alignleft : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'left'},
//                        aligncenter : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'center'},
//                        alignright : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'right'},
//                        alignfull : {selector : 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', classes : 'full'},
//                        bold : {inline : 'span', 'classes' : 'bold'},
//                        italic : {inline : 'span', 'classes' : 'italic'},
//                        underline : {inline : 'span', 'classes' : 'underline', exact : true},
//                        strikethrough : {inline : 'del'},
            },
            setup : function(ed) {
                ed.addButton('addTabsContainer', {
                    title : 'Boite avec onglets',
                    image : '{$iconTab}',
                    onclick : function() {
                        // Add you own code to execute something on click
                        ed.focus();
                        ed.selection.setContent('<div class="tabsContainer"><ul><li>Tab 1</li><ul><li>Contenu tab1</li></ul></li></ul></div>');
                    }
                });
                    ed.addButton('lorem', {
                    title : 'Lorem Ipsum',
                    image : '{$iconLorem}',
                    onclick : function() {
                        // Add you own code to execute something on click
                        ed.focus();
                        ed.selection.setContent('Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam ali');
                    }
                });
            },
            // Example content CSS (should be your site CSS)
            //content_css : "{$_cssPath}",
            content_css : cssPath,
//            theme_advanced_blockformats : "p,h2,h3",

        });
    }
};
