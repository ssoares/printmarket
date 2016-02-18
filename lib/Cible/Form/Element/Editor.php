<?php

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Textarea.php';

class Cible_Form_Element_Editor extends Zend_Form_Element_Textarea {

    /**
     * Use formTextarea view helper by default
     * @var string
     */
//public $helper = 'formEditor';

    const SIMPLE = 'simple';
    const ADVANCED = 'modern';

    protected $_mode = 'modern';
    protected $_script;
    protected $_request;
    protected $_showDropPara = true;
    protected $_showDropStyle = true;
    protected $_showBgColor = false;
    protected $_themeAdvancedBlockFormats = "p,h2,h3";
    protected $_themeAdvancedButtons1 = "bold,italic,underline,strikethrough,|,alignleft aligncenter alignright alignjustify,|,##PARAMS##fontsizeselect,|,forecolor,backcolor";
    protected $_iconList = array(
        "icon-close" => 'Fermer',
        "icon-dons" => 'Dons',
        "icon-finance" => 'Finance',
        "icon-import" => 'Importer',
        "icon-key" => 'Clé',
        "icon-lock" => 'Cadenat',
        "icon-news" => 'Nouvelles',
        "icon-stats" => 'Statistiques',
        "icon-table" => 'Table',
        "icon-team" => 'Équipe',
        "icon-newsletter" => 'Infolettre',
        "icon-arrow-left" => 'Flèche gauche',
        "icon-arrow-right" => 'Flèche droite',
        "icon-dropdown-arrow" => 'Flèche bas',
        "icon-dropdown-arrow-inverted" => 'Flèche haut',
        "icon-banner-previous" => 'Flèche bannière gauche',
        "icon-banner-next" => 'Flèche bannière droite',
        "icon-linkedin" => 'LinkedIn',
        "icon-instagram" => 'Instagram',
        "icon-google" => 'Google',
        "icon-facebook" => 'Facebook',
        "icon-twitter" => 'Twitter',
        "icon-youtube" => 'YouTube',
        "icon-video-play" => 'Jouer vidéo',
        "icon-options" => 'Options',
        "icon-plus" => 'Plus',
    );

    public function setProperties($options) {
        foreach ($options as $property => $value) {
            $propertyName = '_' . $property;

            if (property_exists($this, '_' . $property))
                $this->$propertyName = $value;
        }
    }

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
    public function __construct($spec, $options = null) {
        $iconInsertList = "";
        //construit la liste d'icône dans l'éditeur
        foreach ($this->_iconList as $class => $title) {
            if ($iconInsertList != "")
                $iconInsertList .= ',';
            $iconInsertList .= <<< EOS
                    {text: '{$title}' , onclick: function() {ed.insertContent('&nbsp;<span class="icon {$class}">&nbsp;</span>&nbsp;');} }
EOS;
        }
        parent::__construct($spec, $options);

        $this->setProperties($options);
        $imgContentFolder = '/data/images/content/';
        $fileContentFolder = '/data/files';
        $session = new Zend_Session_Namespace(SESSIONNAME);
        $plugins = "'advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor moxiemanager charmap'";
        $toolbar3 = "table,|,hr,nonbreaking,charmap,visualblocks,|,addTabsContainer,|,lorem,|,two-columns-one-one,two-columns-one-two,two-columns-two-one,two-columns-one-one-one,two-columns-one-five,|,icon-list,|,video-play,|,googlemap";
        if (SESSIONNAME == 'application'){
            $this->_showDropPara = false;
            $plugins = '"paste searchreplace wordcount charmap contextmenu table"';
            $toolbar3 = "table,|,hr,nonbreaking,charmap,visualblocks";
        }
        $styleSelect = "";
        if ($this->_showDropPara)
            $styleSelect .= "formatselect,";

        if ($this->_showDropStyle)
            $styleSelect .= "styleselect,";

        $this->_themeAdvancedButtons1 = str_replace('##PARAMS##', $styleSelect, $this->_themeAdvancedButtons1);

        if (!empty($options['showBgColor'])) {
            $this->_showBgColor = $options['showBgColor'];
        }

        $fc = Zend_Controller_Front::getInstance();
        $this->_request = $fc->getRequest();

        if (null === $this->_view)
            $this->setView($this->getView());

        $_id = $this->getId();
        if (!empty($options['subFormID']))
            $_id = $options['subFormID'] . "-" . $_id;

        $currentTheme = $this->getView()->currentSite;
        $_lang = Cible_FunctionsGeneral::getLanguageSuffix(Zend_Registry::get('languageID'));
        $iconTab = $this->getView()->locateFile('iconTabContent.png', null, 'front', 'default');
        $iconLorem = $this->getView()->locateFile('icon-lorem.png', null, 'front', 'default');
        $iconCol11 = $this->getView()->locateFile('icon-1-1.png', null, 'front', 'default');
        $iconCol21 = $this->getView()->locateFile('icon-2-1.png', null, 'front', 'default');
        $iconCol12 = $this->getView()->locateFile('icon-1-2.png', null, 'front', 'default');
        $iconCol111 = $this->getView()->locateFile('icons-3x.png', null, 'front', 'default');
        $iconCol15 = $this->getView()->locateFile('icon-1-5.png', null, 'front', 'default');
        $iconGMaps = $this->getView()->locateFile('icon-googlemaps.png', null, 'front', 'default');
        $_cssPath = $this->getView()->locateFile('integration.css', null, 'front', 'default');
        $_cssPath .= ',' . $this->getView()->locateFile('integration.css', null, 'front', $currentTheme);
        
        $mediaPath = Zend_Registry::get('www_root') . "/data/files/videos/media_list.js";
        $_iconRoot = Zend_Registry::get('www_root');
        $_SESSION["moxiemanager.filesystem.rootpath"] = "../../../../../" . $session->currentSite . '/data';

        $this->_script = <<< EOS
            tinymce.init({
                // General options
                relative_urls : false,
                remove_script_host : true,
                end_container_on_empty_block: true,
                moxiemanager_image_settings : {
                    rootpath: "{$imgContentFolder}"
                },
                moxiemanager_file_settings : {
                    rootpath: "{$fileContentFolder}"
                },
                selector : "#{$_id}",
                theme : "{$this->_mode}",
                plugins : [{$plugins}],
                language : "{$_lang}",
                moxiemanager_language : "{$_lang}",
                 // Theme options
                menubar: false,
                toolbar1 : "{$this->_themeAdvancedButtons1},subscript,superscript,|,",
                toolbar2 : "undo,redo,|,cut,copy,paste,pastetext,|,searchreplace,|,bullist,numlist,outdent,indent,|,link,unlink,anchor,|,insertfile,image,media,|,removeformat,code,|,preview,fullscreen,print,",
                toolbar3 : "$toolbar3",
                toolbar_items_size:'small',
                image_advtab: true,
                block_formats: "Paragraphe=p;Titre 2=h2;Titre 3=h3;Titre 4=h4;Bloc de code=pre;Citation=blockquote",
                // Schema is HTML5 instead of default HTML4
                schema: "html5",
                // Style formats
                style_formats : [
                        {title : 'Bannières', items: [
                            {title : 'Titre italic large', selector : 'p', classes : 'title-big-italic'},
                            {title : 'Titre italic normal', selector : 'p', classes : 'title-normal-italic'}
 //                         {title : 'Texte gros', inline : 'span', classes : 'banner-text-large'},
 //                         {title : 'Texte normal', inline : 'span', classes : 'banner-text-medium'}
//                          {title : 'Icône dans gros cercle blanc', selector : '.icon', classes : 'banner-white-circle'},
//                          {title : 'Icône jaune', selector: 'span', classes : 'banner-secondary-icon'}
                        ]},
                        {title : 'Contenu', items: [
                            {title : 'Lien style bouton bleu', selector : 'a', classes : 'btn-banner'}


 //                         {title : 'Style identique au h1', inline : 'span', classes : 'content-h1-style'},
 //                         {title : 'Titre footer', selector : 'h2,h3', classes : 'title-footer'},
 //                         {title : 'Transformer un lien en bouton', selector : 'a', classes : 'link-button'},
 //                         {title : 'Gras bleu', inline : 'strong', classes : 'strong-primary'},
 //                         {title : 'Icône dans cercle gris', selector : '.icon', classes : 'icon-circle icon-circle-grey'},
 //                         {title : 'Image centrée', selector : 'img', classes : 'img-vertical-align'},
 //                         {title : 'Ajouter de l\'espace en haut de l\'élément', selector : 'p,h1,h2,h3,h4,img,blockquote', classes : 'super-margin-top'}
                        ]}
                        //{title : 'Padding'},
                        //{title : 'Padding 5 haut et bas', block : 'p', classes : 'paddingTopBottom'},
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
                extended_valid_elements : "table[class|id|align|style|onmouseover|onmouseout|name]",
                valid_children:"+a[div|p|h1|h2|h3|h4|h5|h6]",

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
                        ed.on('init', function(args) {
                            ed = args.target;

                            ed.on('NodeChange', function(e) {
                                if (e && e.element.nodeName.toLowerCase() == 'img') {
                                    width = e.element.width;
                                    height = e.element.height;
                                    tinyMCE.DOM.setAttribs(e.element, {'width': null, 'height': null});
                                    tinyMCE.DOM.setAttribs(e.element,
                                        {'style': 'width:' + width + 'px; height:' + height + 'px;'});
                                }
                            });
                        });
                        ed.addButton('icon-list', {
                            type: 'menubutton',
                            text: 'Icons',
                            icon: false,
                            menu: [
                                {$iconInsertList}
                            ]
                        });
                        ed.addButton('two-columns-one-one', {
                            title : '2 colonnes 1:1',
                            image : '{$iconCol11}',
                            onclick : function() {
                                ed.focus();
                                ed.selection.setContent('<div class="columns"><div class="column-content column-content-first column-content-1x2"><p>colonne 1</p></div><div class="column-content column-content-1x2"><p>colonne 2</p></div></div>&nbsp;');
                            }
                        });
                        ed.addButton('two-columns-one-two', {
                            title : '2 colonnes 1:2',
                            image : '{$iconCol12}',
                            onclick : function() {
                                ed.focus();
                                ed.selection.setContent('<div class="columns"><div class="column-content column-content-first column-content-1x3"><p>colonne 1</p></div><div class="column-content column-content-2x3"><p>colonne 2</p></div></div>&nbsp;');
                            }
                        });
                        ed.addButton('two-columns-two-one', {
                            title : '2 colonnes 2:1',
                            image : '{$iconCol21}',
                            onclick : function() {
                                ed.focus();
                                ed.selection.setContent('<div class="columns"><div class="column-content column-content-first column-content-2x3"><p>colonne 1</p></div><div class="column-content column-content-1x3"><p>colonne 2</p></div></div>&nbsp;');
                            }
                        });
                        ed.addButton('two-columns-one-one-one', {
                            title : '3 colonnes 1:1:1',
                            image : '{$iconCol111}',
                            onclick : function() {
                                ed.focus();
                                ed.selection.setContent('<div class="columns"><div class="column-content column-content-first column-content-1x3"><p>colonne 1</p></div><div class="column-content column-content-1x3"><p>colonne 2</p></div><div class="column-content column-content-1x3"><p>colonne 3</p></div></div>&nbsp;');
                            }
                        });
                        ed.addButton('two-columns-one-five', {
                            title : '2 colonnes 1:5',
                            image : '{$iconCol15}',
                            onclick : function() {
                                ed.focus();
                                ed.selection.setContent('<div class="columns"><div class="column-content column-content-first column-content-1x6"><p>colonne 1</p></div><div class="column-content column-content-5x6"><p>colonne 2</p></div></div>&nbsp;');
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

                    ed.addButton('googlemap', {
                        title : 'Google Maps',
                        image : '{$iconGMaps}',
                        onclick : function() {
                            ed.windowManager.open( {
                                title: 'Insérer une carte Google',
                                body: [
                                        {
                                                type: 'textbox',
                                                name: 'latitudeBox',
                                                label: 'Latitude',
                                                value: ''
                                        },
                                        {
                                                type: 'textbox',
                                                name: 'longitudeBox',
                                                label: 'Longitude',
                                                value: ''
                                        },
                                        {
                                                type: 'textbox',
                                                name: 'zoomBox',
                                                label: 'Zoom',
                                                value: ''
                                        },
                                        {
                                                type: 'listbox',
                                                name: 'colorList',
                                                label: 'Couleurs',
                                                'values': [
                                                        {text: 'Défaut', value: 'false'},
                                                        {text: 'Bleu', value: 'blue'}
                                                ]
                                        }
                                ],
                                onsubmit: function( e ) {
                                    var color = e.data.colorList || 'plain';
                                    ed.insertContent('<div class="google-map" data-latitude="'+e.data.latitudeBox+'" data-longitude="'+e.data.longitudeBox+'" data-zoom="'+e.data.zoomBox+'" data-color="'+color+'"></div>&nbsp;');
                                }
                            });
                        }
                    });

                },

                // Example content CSS (should be your site CSS)
                content_css : "{$_cssPath}",
//                theme_advanced_styles : "",
//                theme_advanced_blockformats : "{$this->_themeAdvancedBlockFormats}",

                // Drop lists for link/image/media/template dialogs
//                template_external_list_url : "lists/template_list.js",
//                external_link_list_url : "lists/link_list.js",
//                external_image_list_url : "lists/image_list.js",
//                media_external_list_url : "{$mediaPath}"

            });
EOS;

        if ($this->_request->isXmlHttpRequest())
            $this->getView()->inlineScript()->appendScript($this->_script);
    }

    /**
     * Render form element
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null) {
        $this->getView()->headLink()->appendStylesheet($this->getView()->locateFile('integration.css', null, 'front', $this->getView()->currentSite));
        $this->getView()->headScript()->appendFile($this->getView()->locateFile('setTabsBox.js', null, 'front'));
        if (null === $this->_view)
            $this->setView($this->getView());

        if (!$this->_request->isXmlHttpRequest()) {
            $this->_view->headScript()->appendScript($this->_script);
        }

        $content = '';
        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        if ($this->_showBgColor) {
//                $content .= $this->_view->partial('partials/tinyMCE.backcolor.phtml');
        }

        return $content;
    }

}
