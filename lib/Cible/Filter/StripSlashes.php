<?php

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';
/**
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

/**
 * @category   Cible
 * @package    Cible_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 */
class Cible_Filter_StripSlashes implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, removing all but alphabetic characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        return stripslashes($value);
    }
}
