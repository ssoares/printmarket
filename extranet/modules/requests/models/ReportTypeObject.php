<?php
/**
 * Module Requests
 * Management of the ReportType.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ReportTypeObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */

/**
 * Manage data from colletion table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ReportTypeObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */
class ReportTypeObject extends DataObject
{
    protected $_dataClass       = 'ReportTypeData';
    protected $_constraint      = '';
    protected $_foreignKey      = 'RE_ReportType';
    protected $_titleField      = 'RET_Label';
    protected $_orderBy         = array('RET_Label');
    protected $_query;
    protected $_name            = '';

    public function getName()
    {
        return $this->_name;
    }

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function insert($data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::insert($data, $langId);
    }

    public function save($id, $data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::save($id, $data, $langId);
    }


    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {

                default:
                    break;
            }
        }

        return $data;
    }

    public function populate($id, $langId)
    {
        $data = $this->_formatOutputData(parent::populate($id, $langId));
//        $data[$this->_titleField] = Cible_FunctionsGeneral::formatValueForUrl($data[$this->_titleField]);
        return $data;
    }

}