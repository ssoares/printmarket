<?php

class RepertoireCollection extends DataObject
{

    protected $_db;
    protected $_current_lang;
    protected $_blockID;
    protected $_blockParams;
    protected $_dataClass = 'RepertoireData';
    protected $_indexClass = 'RepertoireIndex';
    protected $_indexLanguageId = 'RI_LanguageID';
    protected $_foreignKey = 'RD_Region';
    protected $_addrField       = 'RD_AddressId';
    protected $_addrShipField   = 'RD_ShippingAddrId';
    protected $_addrDataField   = 'address';
    protected $_addrShipDataField  = 'addressShipping';

    public function __construct($blockID = null)
    {
        parent::__construct();
        $this->_current_lang = Zend_Registry::get('languageID');

        if ($blockID)
        {

            $this->_blockID = $blockID;
            $_params = Cible_FunctionsBlocks::getBlockParameters($blockID);

            foreach ($_params as $param)
            {
                $this->_blockParams[$param['P_Number']] = $param['P_Value'];
            }
        }
        else
        {

            $this->_blockID = null;
            $this->_blockParams = array();
        }
    }

    public function getDetails($id)
    {
        $select = $this->_db->select();

        $select->from('RepertoireData', array('RD_ID', 'RD_Nom', 'RD_SharedAccount1', 'RD_SharedAccount2', 'RD_SharedAccount3', 'RD_Email'))
            ->distinct()
            ->join('RepertoireIndex', 'RepertoireIndex.RI_RepertoireDataID = RepertoireData.RD_ID')
            ->where('RepertoireIndex.RI_LanguageID = ?', $this->_current_lang)
            ->where('RepertoireData.RD_ID = ?', $id)
            ->where('RepertoireIndex.RI_Status = ?', 1);

        $repertoires = $this->_db->fetchAll($select);

        return $repertoires;
    }

    public function getList($limit = null, $options = null)
    {
        $select = $this->getAll($this->_current_lang, false);
        $select->distinct()
            ->joinLeft('AddressData', 'A_AddressId = RD_AddressId')
            ->joinLeft('AddressIndex', 'AI_AddressId = A_AddressId')            
            ->where('AI_LanguageID = ?', $this->_current_lang)
            ->where('RI_Name LIKE "' . $options['name'] . '%"')
            ->where('RI_Surname LIKE "' . $options['surname'] . '%"')
            ->group('RI_RepertoireDataID')
            ->order('RI_Name');
        if ($limit)
            $select->limit($limit);
        $repertoires = $this->_db->fetchAll($select);
        return $repertoires;
    }

    public function getOtherRepertoires($limit = null, $not_ID)
    {
        $select = $this->_db->select();

        $select->from('RepertoireData', array('RD_ID', 'RD_Nom'))
            ->distinct()
            ->join('RepertoireIndex', 'RepertoireIndex.RI_RepertoireDataID = RepertoireData.RD_ID')
            ->where('RepertoireIndex.RI_LanguageID = ?', $this->_current_lang)
//            ->where('RepertoireData.RD_CategoryID = ?', $this->_blockParams[1])
            ->where('RepertoireIndex.RI_Status = ?', 1)
            ->where('RepertoireData.RD_ID <> ?', $not_ID)
            ->order($this->_blockParams[4]);

        if ($limit)
            $select->limit($limit);

        return $this->_db->fetchAll($select);
    }

    public function getBlockParam($param_name)
    {
        return $this->_blockParams[$param_name];
    }

    public function getBlockParams()
    {
        return $this->_blockParams;
    }

    /**
     * Fetch the id of a repertoires according the formatted string from URL.
     *
     * @param string $string
     *
     * @return int Id of the searched repertoires
     */
    public function getIdByName($string)
    {
        $select = $this->_db->select();
        $select->from('RepertoireIndex', 'RI_RepertoireDataID')
            ->where("RI_ValUrl = ?", $string);
        $id = $this->_db->fetchRow($select);
        return $id['RI_RepertoireDataID'];
    }

    /**
         * Fetch the valUrl of a repertoires for the language switcher.
         *
         * @param int $id
         *
         * @return string $valUrl
         */
    public function getValUrl($id, $lang = 1){
        $select = $this->_db->select();
        $select->from('RepertoireIndex','RI_ValUrl')
                ->where("RI_RepertoireDataID = ?", $id)
                ->where("RI_LanguageID = ?", $lang);

        $valUrl = $this->_db->fetchOne($select);
        return $valUrl;
    }

    public function getNameFirstLetter($iCategorieID = null)
    {
        $arrayLetter = array();
        $select = $this->_db->select()
                       ->distinct()
                       ->from("RepertoireData", array())
                       ->columns(array("Letters" => "UPPER(SUBSTRING(RI_Name,1,1))"))
                       ->join("RepertoireIndex", "RD_ID = RI_RepertoireDataID", array());

        if(!empty($iCategorieID))
            $select->where("RD_Region = ?", $iCategorieID);

        $arrayTmp = $this->_db->fetchAll($select);

        foreach($arrayTmp as $value)
            $arrayLetter[] = $value["Letters"];

        return $arrayLetter;
    }

    public function _regionGrpSrc($grpId = null)
    {
        $oRegion = new RegionObject();
        $list = $oRegion->getList($grpId);

        return $list;
    }

}