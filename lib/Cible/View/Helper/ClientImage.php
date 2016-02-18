<?php
    class Cible_View_Helper_ClientImage extends Zend_View_Helper_Abstract
    {
        public function clientImage($source, $option=null, $absolutePath = false){

            $_image_tag = '<img src="%SOURCE%" alt="%ALT%" %ATTR%  />';
            $_source = '';

            $server_path = $_SERVER['DOCUMENT_ROOT'];
            $base_path = $baseUrl = "{$this->view->baseUrl()}/";

            if ($absolutePath)
                $base_path = rtrim(Zend_Registry::get('absolute_web_root'), '/') . '/';

            if (!Zend_Registry::isRegistered('languageSuffix'))
                $suffix = Cible_FunctionsGeneral::getLanguageSuffix(Zend_Registry::get('languageID'));
            else
                $suffix = Zend_Registry::get('languageSuffix');

            $_config = Zend_Registry::get('config');

            $theme_path = $_config->themes->path;
            $currentTheme = $default_theme = $_config->themes->defaultTheme;

            if (Zend_Registry::isRegistered('currentTheme'))
            {
                $currentTheme = Zend_Registry::get('currentTheme');
                $tmpTheme = explode('/', $currentTheme);
                if (count($tmpTheme) > 1)
                {
                    $currentTheme = $tmpTheme[0];
                    $theme_path .= $tmpTheme[1] . '/';
                }
            }

            // We try to find the image in the localized folder (fr/en/etc), if not found, we look in the common folder
            if( file_exists("{$server_path}{$baseUrl}$currentTheme/{$theme_path}images/$suffix/$source") )
                $_source = "{$base_path}$currentTheme/{$theme_path}images/$suffix/$source";
            else if( file_exists("{$server_path}{$baseUrl}$currentTheme/{$theme_path}images/common/$source") )
                $_source = "{$base_path}$currentTheme/{$theme_path}images/common/$source";

            // if image has not been found in the current theme folders and the current theme folder is not the default theme,
            // then we look at the default localized folder, if still not
            if( empty( $_source ) ){
                $theme_path = $_config->themes->path;
                if( file_exists("{$server_path}{$baseUrl}{$theme_path}$default_theme/images/$suffix/$source") )
                    $_source = "{$base_path}{$theme_path}$default_theme/images/$suffix/$source";
                else if( file_exists("{$server_path}{$baseUrl}{$theme_path}$default_theme/images/common/$source") )
                    $_source = "{$base_path}{$theme_path}$default_theme/images/common/$source";
            }
            $_alt = !empty($option['alt']) ? $option['alt'] : '';

            $_attr = '';
            $exludeOptions = array(
                'alt'
                );

            if( !empty($option) )
            {
                foreach($option as $key => $value){
                    if(!in_array($key,$exludeOptions))
                        $_attr .= "$key=\"$value\" ";
                }
            }

            return str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag);
        }
    }
