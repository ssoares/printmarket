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
 * @version    $Id: DateRange.php 422 2011-03-24 03:25:10Z ssoares $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cible_Validate_DateRange extends Zend_Validate_Date
{
     /**
     * Validation failure message key for when the value does not fit the given dateformat or locale
     */
    const END_DATE_EARLIER    = 'endDateEarlier';

    /**
     * Sets validator options
     *
     * @param  string             $format OPTIONAL
     * @param  string|Zend_Locale $locale OPTIONAL
     * @return void
     */
    public function __construct($format = null, $locale = null)
    {
        parent::__construct($format, $locale);
        
        $this->_messageTemplates[self::END_DATE_EARLIER]= "end date is earlier then start date";
    }
    
    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if $value is a valid date of the format YYYY-MM-DD
     * If optional $format or $locale is set the date format is checked
     * according to Zend_Date, see Zend_Date::isDate()
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {   
        $result = true;
        
        $date_validator = new Zend_Validate_Date($this->_format);
        
        if( !$date_validator->isValid($value['from']) || !$date_validator->isValid($value['to']) )
        {
            $this->_error(self::NOT_YYYY_MM_DD);
            $result = false;
        }
        
        $from_date = new Zend_Date( $value['from'] );
        $to_date = new Zend_Date( $value['to'] );
        
        if( $to_date->isEarlier($from_date)){
            $this->_error(self::END_DATE_EARLIER);
            $result = false;
        }
            
        return $result;
    }

}
