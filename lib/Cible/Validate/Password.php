<?php
/**
 * Cible: EDITH
 *
 * @category   Cible
 * @package    Cible_Validate
 * @copyright  Copyright (c) Cibles solutions d'affaires
 * @version    $Id: Password.php 1330 2013-11-15 18:56:06Z ssoares $
 */

/**
 * Validates the email format according to the regexp
 *
 * @category  Cible
 * @package   Cible_Validate
 * @copyright Copyright (c) Cibles solutions d'affaires
 * @version   $Id: Password.php 1330 2013-11-15 18:56:06Z ssoares $
 */
class Cible_Validate_Password extends Zend_Validate_Regex
{
    protected $_pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#-+!*$@%_])([#-+!*$@%_\w]{8,255})$/';
    /**
     * Class constructor.
     * Set the value of the pattern and validate the email.
     *
     * @param  string $regexp <Optional> Regular expression to validate the value.
     *
     * @return void
     */
    public function __construct($regexp = "")
    {
        if (!empty ($regexp))
            $this->_pattern = $regexp;
        $message = Cible_Translation::getCibleText('validation_message_passwordInvalid');
        $this->setMessage($message, 'regexNotMatch');
        parent::__construct($this->_pattern);

    }

    public function isValid($value)
    {
        return parent::isValid($value);
    }

}
