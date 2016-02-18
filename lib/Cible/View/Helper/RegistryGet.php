<?php
    class Cible_View_Helper_RegistryGet
    {
        public function registryGet($identifier){
            if(Zend_Registry::isRegistered($identifier))
                return Zend_Registry::get($identifier);
            else
                return "$identifier was not found in registry";
        }
    }