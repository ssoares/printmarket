<?php
/**
 * Module Utilities
 * Management of the references data.
 *
 * @category  Application_Module
 * @package   Application_Module_Utilities
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */

/**
 * Manage data from references table.
 *
 * @category  Application_Module
 * @package   Application_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */
class ParametersObject extends DataObject
{
    protected $_dataClass   = 'ParametersData';
    
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
   // protected $_constraint      = 'CP_FreeItemID';

    /**
     * Return the parameter value from database.
     *
     * @param string $name The field name in the database
     *
     * @return Mixed
     */
    public function getValueByName($name)
    {
        $select = $this->getAll(null, true);

        $value = $select[0][$name];

        return $value;
    }
}