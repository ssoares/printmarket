<?php
/**
 * Companies Profile data
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_CompaniesProfilesObject
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CompaniesProfilesObject.php 1372 2013-12-27 22:07:54Z ssoares $id
 */

/**
 * Manages Companies Profile data.
 *
 * @category  Cible
 * @package   Cible_CompaniesProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CompaniesProfilesObject.php 1372 2013-12-27 22:07:54Z ssoares $id
 */
class CompaniesObject extends DataObject
{

    protected $_dataClass   = 'CompaniesData';

    protected $_foreignKey      = 'CIE_AddressId';
    protected $_addrField       = 'CIE_AddressId';
    protected $_addrShipField   = '';
    protected $_addrDataField   = 'address';
    protected $_addrShipDataField  = '';
    protected $_id  = 0;

    public function getAddrField(){
        return $this->_addrField;
    }

    public function getAddrDataField(){
        return $this->_addrDataField;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function insert($data, $langId)
    {
        $data['CIE_CreaDate'] = date('Y-m-d H:i:s');
        $oAddress = new AddressObject();
        $cieExists = $oAddress->findData($data[$this->_addrDataField]);
        if (empty($cieExists))
        {
            $addrId = $oAddress->insert($data[$this->_addrDataField], $langId);
            $data[$this->_addrField] = $addrId;
            if (isset($data[$this->_addrShipDataField]['duplicate'])
                && $data[$this->_addrShipDataField]['duplicate'] == 1){
                $data[$this->_addrDataField]['A_Duplicate'] = $addrId;
                $shipId = $oAddress->insert($data[$this->_addrDataField], $langId);
                $data[$this->_addrShipField] = $shipId;
            }elseif (!empty($data[$this->_addrShipDataField])){
                $data[$this->_addrShipDataField]['A_Duplicate'] = 0;
                $shipId = $oAddress->insert($data[$this->_addrShipDataField], $langId);
                $data[$this->_addrShipField] = $shipId;
            }
            $this->_id = parent::insert($data, $langId);
        }else{
            $cie = parent::findData(array($this->_addrField => $cieExists[0][$oAddress->getDataId()]));
            $this->_id = $cie[0][$this->_dataId];
        }

        $profileId = isset($data['genericId'])?$data['genericId']:$data['CDO_ProfileId'];
        $oRelation = new GenericProfilesAssociationObject();
        $oRelation->setCieId($this->_id)
            ->setConstraint('GPA_CieId')
            ->setProfileId($profileId)
            ->insert(array(), $langId);

        return $this->_id;
    }

    public function save($id, $data, $langId)
    {
        $oGP = new GenericProfilesObject();
        if (isset($data['genericId'])){
            $data['CDO_ProfileId'] = $data['genericId'];
        }
        $genData = $oGP->findData(array($oGP->getDataId() => (int)$data['CDO_ProfileId']));
        if ($genData[0]['GP_Language'] != $langId){
            $langId = $genData[0]['GP_Language'];
        }

        $langId <= 0 ? $langId = 1 : '';
        $data = $this->_setAddressData($data, $langId, $data[$this->_addrField]);
        parent::save($id, $data, $langId);
    }

    public function recordExists($tmpArray, $langId = null, $data = true)
    {
        if (empty($data['CDO_CieId'])){
            $exists = false;
        }else{
            $tmpArray = array($this->_dataId => $data['CDO_CieId']);
            $exists = parent::recordExists($tmpArray, $langId, $data);
        }
        return $exists;
    }

    public function findData($filters = array(), $useQry = false)
    {
        $billAddr = array();
        $shipAddr = array();
        $oAddress = new AddressObject();
        if (isset($filters['langId'])){
            $langId   = $filters['langId'];
            unset($filters['langId']);
        }else{
            $langId   = Zend_Registry::get('languageID');
        }
        $data = parent::findData($filters);

        if (!empty($data))
        {
            $data = $data[0];
            $data['currentLanguage'] = $langId;
            $addrId = $data[$this->_addrField];
            if (!empty($data[$this->_addrShipField]))
                $shipId = $data[$this->_addrShipField];

            if (!empty($shipId))
            {
                $shipAddr = $oAddress->getAll($langId, true, $shipId);
                $shipAddr = $shipAddr[0];
                $shipAddr[$this->_addrShipField] = $shipId;
            }

            if (!empty($addrId))
            {
                $billAddr = $oAddress->getAll($langId, true, $addrId);
                $billAddr = $billAddr[0];
                $billAddr[$this->_addrField] = $addrId;
            }

            if (isset($shipAddr['A_Duplicate']) && !$shipAddr['A_Duplicate'])
                $shipAddr['duplicate'] = 0;

            $data[$this->_addrDataField] = $billAddr;
            $data[$this->_addrShipDataField] = $shipAddr;
        }

        return $data;
    }

    public function populate($id, $langId)
    {
        $data = parent::populate($id, $langId);
        if (!empty($data))
        {
            $oAddress = new AddressObject();
            $data['currentLanguage'] = $langId;
            $addrId = $data[$this->_addrField];
            if (!empty($data[$this->_addrShipField]))
                $shipId = $data[$this->_addrShipField];

            if (!empty($shipId)){
                $shipAddr = $oAddress->populate($shipId, $langId);
                $shipAddr[$this->_addrShipField] = $shipId;
                if (isset($shipAddr['A_Duplicate']) && !$shipAddr['A_Duplicate'])
                    $shipAddr['duplicate'] = 0;
                $data[$this->_addrShipDataField] = $shipAddr;
            }

            if (!empty($addrId)){
                $billAddr = $oAddress->populate($addrId, $langId);
                $billAddr[$this->_addrField] = $addrId;
                $data[$this->_addrDataField] = $billAddr;
            }
        }

        return $data;

    }

    public function delete($id)
    {

        return parent::delete($id);
    }
}