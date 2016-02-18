<?php
    class Cible_View_Helper_Auth
    {
        public function auth(){
            return (array)Zend_Auth::getInstance()->getStorage()->read();
        }
    }