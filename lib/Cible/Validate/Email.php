<?php
/**
 * Cible: EDITH
 *
 * @category   Cible
 * @package    Cible_Validate
 * @copyright  Copyright (c) Cibles solutions d'affaires
 * @version    $Id: Email.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Validates the email format according to the regexp
 *
 * @category  Cible
 * @package   Cible_Validate
 * @copyright Copyright (c) Cibles solutions d'affaires
 * @version   $Id: Email.php 1788 2015-06-18 20:07:52Z ssoares $
 */
class Cible_Validate_Email extends Zend_Validate_Regex
{
    protected $_pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,10})$/';
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
        if (!empty ($regexp)){
            $this->_pattern = $regexp;
        }
        $this->_messageTemplates = array(
            self::NOT_MATCH => Cible_Translation::getCibleText('validation_message_emailAddressInvalid')
        );
        parent::__construct($this->_pattern);

    }

}
