<?php
/**
 * Module Template
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: TemplateObject.php 356 2013-02-01 04:22:26Z ssoares $
 */

/**
 * Manage data from Template table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: TemplateObject.php 356 2013-02-01 04:22:26Z ssoares $
 */
class TemplateObject extends DataObject
{
    protected $_dataClass       = 'TemplateData';
    protected $_indexClass      = 'TemplateIndex';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = '';
    protected $_description     = '';
    protected $_position        = '';
    protected $_query;
    protected $_blockID = 0;
    protected $_blockParams = array();

    public function setBlockID($blockID)
    {
        $this->_blockID = $blockID;
    }
    public function getBlockParams( $field = '')
    {
        if (!empty($field))
            return $this->_blockParams[$field];

        return $this->_blockParams;

    }

    public function setBlockParams(array $blockParams = array())
    {
        if( $this->_blockID )
        {
            $_params = Cible_FunctionsBlocks::getBlockParameters( $this->_blockID );

            foreach( $_params as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
        else
        {
            foreach( $blockParams as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
    }

}