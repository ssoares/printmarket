<?php
    require_once 'Zend/View/Helper/FormElement.php'; 
    class Cible_View_Helper_FormHtml extends Zend_View_Helper_FormElement
    {
        public function formHtml($name, $value = null, array $attribs = array())
        {
            //echo($name);    
            return $value;
            
        }
    } 
?>
