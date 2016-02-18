<?php
    class Cible_View_Helper_Link extends Zend_View_Helper_Abstract
    {
        public function link($url, $title, $option=null){

            $_tag = '<a href="%URL%" %ATTR%>%TITLE%</a>';

            $_url = empty( $url ) ? 'javascript:void(0)' : $url;
            $_title = isset($title) ? $title : $_url;

            $_attr = '';

            if( !empty($option) )
            {
                foreach($option as $key => $value){
                    $_attr .= "$key=\"$value\" ";
                }
            }

            return str_replace(array('%URL%','%TITLE%','%ATTR%'), array($_url, $_title, $_attr), $_tag);
        }
    }