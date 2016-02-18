<?php
/**
 * Module CompaniesProfiles
 * Management of the Items.
 *
 * @category  Application_Module
 * @package   Application_Module_CompaniesProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_CompaniesProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsAssociationObject.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class GenericProfilesFollowObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'GenericProfilesFollowData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = '';
    protected $_foreignKey      = 'GPF_ProfileId';
    protected $_relationField   = 'GPF_Relationship';
    protected $_seq             = 0;
    protected $_profileId       = 0;
    protected $_query;

    public function getProfileId()
    {
        return $this->_profileId;
    }

    public function setProfileId($profileId)
    {
        $this->_profileId = $profileId;
        return $this;
    }

    public function setConstraint($constraint)
    {
        $this->_constraint = $constraint;
        return $this;
    }

    public function insert($data, $langId)
    {
        $data = $this->_formatInputData($data);
        $data['GPF_CreaDate'] = date('Y-m-d H:i:s');

        return parent::insert($data, $langId);
    }

    public function save($id, $data, $langId)
    {
        return parent::save($id, $this->_formatInputData($data), $langId);
    }

    public function findData($filters = array(), $useQry = false)
    {
        $oGeneric = new GenericProfilesObject();
        if (isset($filters[$this->_foreignKey])){
            $genericData = $oGeneric->populate($filters[$this->_foreignKey], 1);
            $langId   = $genericData['GP_Language'];
        }
        else{
            $langId = Zend_Registry::get('languageID');
        }
        $data = parent::findData($filters);

        if (!empty($data))
        {
            $data = $data[0];
        }

        return $data;
    }

    public function deleteAssociation($id, $field = '')
    {
        if (empty($field))
            $field = $this->_dataId;
        $association = new $this->_dataClass();
        $where = $this->_db->quoteInto( $field . " = ?", $id);
        $association->delete($where);
    }

    public function getRelated($id, $langId = 1)
    {
        $related = array();
        $selectAssociation = $this->getAll($langId, false);
        $selectAssociation->where($this->_dataId . " = ?", $id);
        $associationFind = $this->_db->fetchAll($selectAssociation);

        foreach ($associationFind as $assocData)
            $related[] = $assocData[$this->_foreignKey];

        return $related;
    }

    protected function _formatInputData(array $data)
    {
        $data = parent::_formatInputData($data);
        if (empty($data[$this->_relationField])){
            $data[$this->_relationField] = $this->_relationId;
        }
        if (empty($data[$this->_foreignKey])){
            $data[$this->_foreignKey] = $data['genericId'];
        }
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'XX_XXXXDate':
                    if (!empty($values)){
                        $data[$field] = date('Y-m-d', strtotime($values));
                    }
                    break;
                default:
                    break;
            }
        }
        return $data;

    }

    public function _adminListSrc()
    {
        $list = array();
        $oAdmin = new ExtranetUsers();
        $data = $oAdmin->populate();
        $list[0] = Cible_Translation::getCibleText('form_select_default_label');
        foreach($data as $admin){
            $list[$admin['EU_ID']] = $admin['EU_FName'] . ' ' . $admin['EU_LName'];
        }

        return $list;
    }

}