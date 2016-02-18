<?php
    class Cible_View_Helper_Image extends Zend_View_Helper_Abstract
    {
        public function image($source, $option=null){

            $_image_tag = '<img src="%SOURCE%" alt="%ALT%" %ATTR%  />';
            $docRoot = rtrim(Zend_Registry::get('fullDocumentRoot'), '/');
            $protocol = Zend_Registry::get('protocol');
            if (!file_exists($docRoot . $source) && !strlen(strstr($source, $protocol)) && !isset($option['direct']))
                $_source = $this->view->locateFile($source);
            else
                $_source = $source;

            $_alt = !empty($option['alt']) ? $option['alt'] : '';

            $_attr = '';
            $exludeOptions = array(
                'alt',
                'direct'
            );

            if( !empty($option) )
            {
                foreach($option as $key => $value)
                {
                    if(!in_array($key,$exludeOptions))
                        $_attr .= "$key=\"$value\" ";
                }
            }

            //$_source = $bodytag = str_replace("//", "/", $_source);
            return str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag);
        }
    }