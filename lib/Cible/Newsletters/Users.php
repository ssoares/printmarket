<?php
/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Manage Courrielleur Users.
 *
 * @category Cible
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_Newsletters_Users extends Cible_Newsletters
{
    protected $_component = 'User';
    protected $_groupsActions = array(
        'login' => array('User', 'Login'),
        );

    public function __construct($options = array())
    {
        parent::__construct($options);

    }

    public function login()
    {
        $this->_data = $this->_loginUserInfo;
        $this->_action = 'Login';
        $this->process();
        if ($this->_results['status'] == 'success'){
            $this->_userKey = $this->_results['data']['user_key'];
        }

        return $this;
    }

}
