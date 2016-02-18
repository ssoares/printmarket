<?php
/**
 * Member Profile data
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_MemberProfilesObject
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: MemberProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */

/**
 * Manages Member Profile data.
 *
 * @category  Cible
 * @package   Cible_MemberProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: MemberProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */
class MemberProfilesObject extends DataObject
{

    protected $_dataClass   = 'MemberProfilesData';

    protected $_foreignKey      = 'MP_GenericProfileId';
    protected $_addrField       = 'MP_AddressId';
    protected $_addrShipField   = 'MP_ShippingAddrId';
    protected $_addrDataField   = 'address';
    protected $_addrShipDataField  = 'addressShipping';
    protected $_oGeneric  = null;

    public function getOGeneric(){
        return $this->_oGeneric;
    }
    public function setOGeneric($oGeneric = '')
    {
        if (!empty($oGeneric)){
            $this->_oGeneric = new $oGeneric();
        }else{
            $this->_oGeneric = new GenericProfilesObject();
        }
        return $this;
    }
    public function addProfile($data, $langId = 0)
    {
        if (!$langId && !empty($data['languageId'])){
            $langId = $data['languageId'];
        }elseif(!$langId){
            $langId = Zend_Registry::get('languageID');
        }
        $data = $this->_setAddressData($data);
        return $this->_oGeneric->insert($data, $langId);
    }
    public function save($id, $data, $langId)
    {
        $addrId = 0;
        $genData = $this->_oGeneric->findData(array($this->_oGeneric->getDataId() => $id));
        if ($genData[0]['GP_Language'] != $langId){
            $langId = $genData[0]['GP_Language'];
        }
        $data = $this->_setAddressData($data);
        if (!empty($data['MP_Password'])){
            $data['MP_Password'] = md5($data['MP_Password']);
        }else{
            unset($data['MP_Password']);
        }
        parent::save($id, $data, $langId);
    }

    public function findData($filters = array(), $useQry = false)
    {
        $billAddr = array();
        $shipAddr = array();
        $oAddress = new AddressObject();
        $oGeneric = new GenericProfilesObject();
        if (isset($filters[$this->_foreignKey]))
        {
            $genericData = $oGeneric->populate($filters[$this->_foreignKey], 1);
            $langId   = $genericData['GP_Language'];
        }
        else
            $langId   = Zend_Registry::get('languageID');

        $data = parent::findData($filters);

        if (!empty($data))
        {
            $data = array_merge($genericData, $data[0]);
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
    public function getDateList()
    {
        $select = parent::getAll(null, false);
        $cols = array('MP_DateCreate');
        $select->distinct(true);

        $data = $this->_db->fetchAll($select);
        foreach ($data as $values)
        {
            $date = substr($values['MP_DateCreate'], 0, 10);
            $dates[$date] = $date;
        }

        $dates = array_unique($dates);

        return $dates;
    }

}