<?php

class RepertoireObject extends DataObject
{

    protected $_dataClass = 'RepertoireData';
    protected $_indexClass = 'RepertoireIndex';
    protected $_indexLanguageId = 'RI_LanguageID';
    protected $_foreignKey = 'RD_Region';
    protected $_addrField       = 'RD_AddressId';
    protected $_addrShipField   = 'RD_ShippingAddrId';
    protected $_addrDataField   = 'address';
    protected $_addrShipDataField  = 'addressShipping';


    public function _repDistSrc()
    {
        return array(
            1 => Cible_Translation::getCibleText('form_label_isRep'),
            2 => Cible_Translation::getCibleText('form_label_isDistributor'));
    }
    public function _groupsSrc()
    {
        $obj = new GroupeObject();
        $list = $obj->groupsList();

        return $list;
    }
    public function _regionGrpSrc()
    {
        $oRegion = new RegionObject();
        $list = $oRegion->getList();

        return $list;
    }

    public function insert($data, $langId)
    {
        if (isset($data[$this->_addrDataField]))
        {
            $oAdress = new AddressObject();
            $addrBill = $data[$this->_addrDataField];
            if (!empty($data[$this->_addrShipDataField]))
                $addrShip = $data[$this->_addrShipDataField];
        }
        if (!empty($addrBill))
        {
            $addrId = $oAdress->save($data[$this->_addrDataField][$this->_addrField], $addrBill, $langId);

            if (isset($addrShip['duplicate']) && $addrShip['duplicate'] == 1)
            {
                $addrBill['A_Duplicate'] = $billId;
                $shipId = $oAdress->save($addrShip[$this->_addrShipDataField][$this->_addrShipField], $addrBill, $langId);
            }
            elseif (!empty($data[$this->_addrShipDataField]))
            {
                $addrShip['A_Duplicate'] = 0;
                $shipId = $oAdress->save($addrShip[$this->_addrShipDataField][$this->_addrShipField], $addrShip, $langId);
            }
            $data[$this->_addrField] = $addrId;
        }

        $data['RD_ValUrl'] = Cible_FunctionsGeneral::formatValueForUrl($data['RI_Name']);

        $id = parent::insert($data, $langId);

        return $id;
    }
    public function save($id, $data, $langId)
    {
        if (isset($data[$this->_addrDataField]))
        {
            $oAdress = new AddressObject();
            $addrBill = $data[$this->_addrDataField];
            if (!empty($data[$this->_addrShipDataField]))
                $addrShip = $data[$this->_addrShipDataField];
        }
        if (!empty($addrBill))
        {
            $addrId = $oAdress->save($data[$this->_addrDataField][$this->_addrField], $addrBill, $langId);

            if (isset($addrShip['duplicate']) && $addrShip['duplicate'] == 1)
            {
                $addrBill['A_Duplicate'] = $billId;
                $shipId = $oAdress->save($addrShip[$this->_addrShipDataField][$this->_addrShipField], $addrBill, $langId);
            }
            elseif (!empty($data[$this->_addrShipDataField]))
            {
                $addrShip['A_Duplicate'] = 0;
                $shipId = $oAdress->save($addrShip[$this->_addrShipDataField][$this->_addrShipField], $addrShip, $langId);
            }
            $data[$this->_addrField] = $addrId;
        }

        $data['RD_ValUrl'] = Cible_FunctionsGeneral::formatValueForUrl($data['RI_Name']);

        parent::save($id, $data, $langId);
    }

    public function populate($id, $langId)
    {
        $oAddr = new AddressObject();
        $data = parent::populate($id, $langId);

        $addrId = $data[$this->_addrField];
        $address = $oAddr->populate($addrId, $langId);
        $address['selectedState'] = $address['A_StateId'];
        $data[$this->_addrDataField] = $address;
        $data[$this->_addrDataField][$this->_addrField] = $addrId;

        return $data;
    }

    public function setIndexationData()
    {

        return $this;
    }

    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
}
