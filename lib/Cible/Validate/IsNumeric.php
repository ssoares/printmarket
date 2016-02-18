<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Digits.php 8064 2008-02-16 10:58:39Z thomas $
 */


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * Test if value is a numric one.
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cible_Validate_IsNumeric extends Zend_Validate_Abstract
{
    /**
     * Validation failure message key for when the value contains non-digit characters
     */
    const NOT_NUMERIC = 'notNumeric';

    /**
     * Validation failure message key for when the value is an empty string
     */
    const STRING_EMPTY = 'stringEmpty';

    /**
     * Digits filter used for validation
     *
     * @var Zend_Filter_Digits
     */
    protected static $_filter = null;

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_NUMERIC   => "'%value%' is not a valid number",
        self::STRING_EMPTY => "'%value%' is an empty string"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value only contains digit characters
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $value = str_replace(array(' ', ','), array('','.'), $value);;

        $this->_setValue($value);

        if ('' === $value) {
            $this->_error(self::STRING_EMPTY);
            return false;
        }

        if (!is_numeric($value)){
            $this->_error(self::NOT_NUMERIC);
            return false;
        }

        return true;
    }

}
