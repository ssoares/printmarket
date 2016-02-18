<?php
/**
 * Cible: EDITH
 *
 * @category   Cible
 * @package    Cible_Validate
 * @copyright  Copyright (c) Cibles solutions d'affaires
 * @version    $Id: CheckedBox.php 483 2011-05-17 17:55:12Z ssoares $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * Validate if a checkbox is checked or not.
 * 
 * @category   Cible
 * @package    Cible_Validate
 * @copyright  Copyright (c) Cibles solutions d'affaires
 */
class Cible_Validate_CheckedBox extends Zend_Validate_Abstract
{
     /**
     * Validation failure message key for when the value does not fit the given dateformat or locale
     */
    const NOT_CHECKED    = 'notChecked';
    
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_CHECKED => "'%value%' equals to '%validate%'."
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'validate' => '_validate'
    );

    /**
     * The value for notChecked status
     *
     * @var mixed
     */
    protected $_validate;

    /**
     * Sets validator options
     *
     * @param  int $val
     * @return void
     */
    public function __construct($val = 0)
    {
        $val = (int)$val;
        $this->setValidate($val);
    }

    /**
     * Returns the validate option
     *
     * @return mixed
     */
    public function getValidate()
    {
        return $this->_validate;
    }

    /**
     * Sets the validate parameter value
     *
     * @param  mixed $validate
     * @return Zend_Validate_CheckedBox Provides a fluent interface
     */
    public function setValidate($val)
    {
        $this->_validate = $val;
        return $this;
    }
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value equals 1.
     * By default, unchecked status = 0. If $value equals 1, then it's checked.
     *
     * @param  int $value
     * @return boolean
     */
    public function isValid($value)
    {   
        $value = (int)$value;
        $this->_setValue($value);
        
        if ($this->_validate === $value) {
            $this->_error();
            return false;
        }
        return true;
    }

}
