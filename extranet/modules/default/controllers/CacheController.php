<?php

/** Zend_Controller_Action */

class CacheController extends Cible_Extranet_Controller_Action
{
    function indexAction()
    { 
        
    }
    
    function clearAction()
    {
        $tag = $this->_request->getParam('ID');
        
        if( in_array( $tag, array('cible','client')) ){
            $cache = Zend_Registry::get('cache');
            $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag) );
            $this->_redirect( 'cache' );
        }
    }
}
?>