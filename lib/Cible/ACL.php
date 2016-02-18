<?php
/**
 * Cible Framework
 *
 *
 * @category   Cible
 * @package    Cible_ACL
 * @copyright
 * @license
 * @version
 */


/**
 * @package    Cible_ACL
 * @copyright
 * @license
 */
abstract class Cible_ACL
{
    /**
    * Verifies that the current identity has access to the page received as param page
    *
    * @param mixed $page
    */
    public static function hasAccess($page){

        if(Zend_Auth::getInstance()->hasIdentity()){
            return true;
        } else {
            throw new Exception('Cible_ACL_Exception: no user logged in');
        }

    }

    /**
    * Give access for the current identity  to the page received as param page
    *
    * @param mixed $page
    */
    public static function giveAccess($page)
    {
        throw new Exception('Method giveAccess not implemented');
    }
    /**
    * Remove access for the current identity  to the page received as param page
    *
    * @param mixed $page
    */
    public static function removeAccess($page)
    {
        throw new Exception('Method removeAccess not implemented');
    }
}
