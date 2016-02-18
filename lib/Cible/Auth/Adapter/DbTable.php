<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2014 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of DbTable
 *
 * @category
 * @package
 * @copyright Copyright (c)2014 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: DbTable.php 1534 2014-04-09 13:51:21Z ssoares $
 */
class Cible_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
    public function setDbSelect($select) {
        $this->_dbSelect = $select;
        return $this;
    }

    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        parent::_authenticateSetup();
        $dbSelect = $this->_authenticateCreateSelect();
        $resultIdentities = $this->_authenticateQuerySelect($dbSelect);

        if ( ($authResult = $this->_authenticateValidateResultset($resultIdentities)) instanceof Zend_Auth_Result) {
            return $authResult;
        }

        $authResult = $this->_authenticateValidateResult(array_shift($resultIdentities));
        return $authResult;
    }

    /**
     * _authenticateCreateSelect() - This method creates a Zend_Db_Select object that
     * is completely configured to be queried against the database.
     *
     * @return Zend_Db_Select
     */
    protected function _authenticateCreateSelect()
    {
        $dbSelect = parent::_authenticateCreateSelect();
        $cfg = Zend_Registry::get('config');
        $name = array();
        foreach ($cfg->multisite as $data)
        {
            if ((bool) $data->active){
                $name[] =  $data->name;
            }
        }

        $dbSelect->joinLeft('Extranet_UsersSites', 'EU_ID = EUS_UserId')
            ->where('EU_DefaultSite in (?)', $name);

        return $dbSelect;
    }
}