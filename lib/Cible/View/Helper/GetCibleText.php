<?php
class Cible_View_Helper_GetCibleText extends Zend_View_Helper_Abstract
{
    public function getCibleText($key, $lang = null, $options = array()){
        return Cible_Translation::__($key, Cible_Translation::TRANSLATION_TYPE_CIBLE, $lang, $options);
    }
}