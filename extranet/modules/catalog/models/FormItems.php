<?php

/**
 * Module Catalog
 * Management of the catalog.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormItems.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Form to add a new item.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormItems.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class FormItems extends Cible_Form_GenerateForm
{

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }
        parent::__construct($options);
        
    }

}