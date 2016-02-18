<?php
    class Cible_View_Helper_BaseUrl
    {
        public function baseUrl($url = ''){
            $frontController = Zend_Controller_Front::getInstance();
            $baseUrl = $frontController->getBaseUrl();
            if (!empty($url)){
                $baseUrl .= strpos($url, '/') === 0 ?:'/';
                $baseUrl .= $url;
            }
            return $baseUrl;
        }
    }