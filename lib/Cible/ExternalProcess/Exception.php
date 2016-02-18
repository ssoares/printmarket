<?php
/**
 * Zend_Exception
 */
require_once 'Zend/Exception.php';

/**
 * Exception class for Cible_ExternalProcess
 *
 * @category   Cible
 * @package    Cible_ExternalProcess
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_ExternalProcess_Exception extends Zend_Exception
{
    protected $_fileerror = null;

    public function __construct($message, $fileerror = 0)
    {
        $this->_fileerror = $fileerror;
        parent::__construct($message);
    }

    /**
     * Returns the transfer error code for the exception
     * This is not the exception code !!!
     *
     * @return integer
     */
    public function getFileError()
    {
        return $this->_fileerror;
    }
}
