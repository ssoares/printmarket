<?php

/**
 * Module Catalog
 * Management of the products.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormCategories.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Form to add a new collection.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormCategories.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class FormCategories extends Cible_Form_GenerateForm
{

    protected $_imageSrcL;
    protected $_imageSrcS;
    protected $_isNewImageL;
    protected $_isNewImageS;
    protected $_dataId;
    protected $_moduleName;
    protected $_filePath;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($options)
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
