<?php
    /** Zend_Form_Element_Xhtml */
    require_once 'Zend/Form/Element/Hidden.php';

    class Cible_Form_Element_LanguageSelector extends Zend_Form_Element_Hidden
    {
        protected $_lang;
        protected $_params;
        protected $_mode = 'edit';

        public function __construct($spec, $params=array(), $options = null)
        {
            parent::__construct($spec, $options);

            $this->_lang = empty( $options['lang'] ) ? 1 : $options['lang'] ;
            if($options['mode'] == 'add')
            {
                $this->_mode = 'add';
                $config = Zend_registry::get('config');
                $this->_lang = $config->defaultEditLanguage;
            }

            $this->_params = $params;
        }
        /**
         * Render form element
         *
         * @param  Zend_View_Interface $view
         * @return string
         */
        public function render(Zend_View_Interface $view = null)
        {
            $_availableLanguages = Cible_FunctionsGeneral::getAllLanguage();

            $_baseUrl = $this->getView()->baseUrl();

            $_module = '';
            $_controller = '';
            $_action = '';
            $_params = '';

            foreach( $this->_params as $_key => $_val){
                switch( $_key ){
                    case 'module':
                        $_module = $_val;
                        break;
                    case 'controller':
                        $_controller = $_val;
                        break;
                    case 'action':
                        $_action = $_val;
                        break;
                    default:
                        if( strtolower($_key) != 'lang' && !isset( $_POST[$_key] ) )
                            $_params .= "/$_key/$_val";
                }
            }

            $_requestURI = "$_baseUrl/$_module/$_controller/$_action$_params";

            $content = '';

            $first = true;

            foreach($_availableLanguages as $_lang){
                $_selected = false;

                $_class = '';
                if( $first ){
                    $_class = 'first';
                    $first = false;
                }

                if( $_lang['L_ID'] == $this->_lang )
                    $_selected = true;

                if( $_selected ){
                    $content .= '<li class="languageSelector_'.$_lang['L_ID'].'">';
                }

                else
                    $content .=  '<li>';

                if( $_selected ){
                    $content .= $this->getView()->link("$_requestURI/lang/{$_lang['L_Suffix']}", $_lang['L_Title'], array('class'=> $_class . ' selected' . " languageSelector_{$_lang['L_ID']}"));
                } else {
                   if($this->_mode == 'add')
                        $content .= '<span class="disabled-language">' . $_lang['L_Title'] . '</span>';
                   else
                        $content .= $this->getView()->link("$_requestURI/lang/{$_lang['L_Suffix']}", $_lang['L_Title'], array('class'=> $_class));
                }
                $content .=  '</li>';
            }

            if( !empty($content) ){
                $content = "<ul id='language-switcher'>$content</ul>";
            }

            foreach ($this->getDecorators() as $decorator) {
                $decorator->setElement($this);
                $content = $decorator->render($content);
            }

            return $content;
        }
    }
?>
