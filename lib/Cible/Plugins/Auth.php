<?php
/***
*  Authentication Plugin
*
* @Author Yannick Gagnon yannick.gagnon@ciblesolutions.com
*/
class Cible_Plugins_Auth extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // If we have no identity, we're not logged in
        if (!Zend_Auth::getInstance()->hasIdentity()){

            // If we are not already interacting with the Auth/login, redirect to it
            if( !in_array( $request->getActionName(), array('login', 'logout') ) ){

                // We use setParam to send the requested page so that we can redirect to it when successfully authenticated
                if( isset($_SERVER['REDIRECT_URL']) )
                    $request->setParam('redirect', $_SERVER['REDIRECT_URL']);

                $request->setModuleName('default');
                $request->setControllerName('auth');
                $request->setActionName('login');

            }

        } elseif ( $request->getActionName() == 'login'){

            $request->setModuleName('default');
            $request->setControllerName('index');
            $request->setActionName('index');

        }
    }

}
