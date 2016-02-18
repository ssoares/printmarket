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
class GenericProfilesAssociationObject extends DataObject
{
    const INSERT = 'insert';
    const DEL = 'delete';
    const SAVE = 'save';

    protected $_dataClass       = 'GenericProfilesAssociationData';
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
    protected $_constraint      = 'GPA_RelatedProfileId';
    protected $_foreignKey      = 'GPA_ProfileId';
    protected $_relationField   = 'GPA_Relationship';
    protected $_seq             = 0;
    protected $_profileId       = 0;
    protected $_cieId           = 0;
    protected $_relProfileId    = 0;
    protected $_relationId      = 20;
    protected $_query;

    public function getRelationId()
    {
        return $this->_relationId;
    }

    public function setRelationId($relationId)
    {
        $this->_relationId = $relationId;
        return $this;
    }

    public function getProfileId()
    {
        return $this->_profileId;
    }

    public function getCieId()
    {
        return $this->_cieId;
    }

    public function getRelProfileId()
    {
        return $this->_relProfileId;
    }

    public function setProfileId($profileId)
    {
        $this->_profileId = $profileId;
        return $this;
    }

    public function setCieId($cieId)
    {
        $this->_cieId = $cieId;
        return $this;
    }

    public function setRelProfileId($relProfileId)
    {
        $this->_relProfileId = $relProfileId;
        return $this;
    }

    public function setConstraint($constraint)
    {
        $this->_constraint = $constraint;
        return $this;
    }

    public function insert($data, $langId)
    {
        if (empty($data[$this->_relationField])){
            $data[$this->_relationField] = $this->_relationId;
        }
        if (!empty($this->_cieId)){
            $data[$this->_constraint] = $this->_cieId;
        }
        if (!empty($this->_relProfileId)){
            $data['GPA_RelatedProfileId'] = $this->_relProfileId;
        }
        if (!empty($this->_profileId)){
            $data[$this->_foreignKey] = $this->_profileId;
        }
        $data['GPA_CreaDate'] = date('Y-m-d H:i:s');
        return parent::insert($data, $langId);
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

    public function getProfileAssocations()
    {
        if (empty($this->_profileId)){
            throw new Zend_Exception('ADP-002 : No profile id given');
        }
        $filter = array($this->_foreignKey => $this->_profileId);
        if ($this->_cieId > 0){
            $filter['GPA_CieId'] = $this->_cieId;
        }
        if ($this->_relProfileId > 0){
            $filter['GPA_RelatedProfileId'] = $this->_relProfileId;
        }

        return $data = parent::findData($filter);
    }

}