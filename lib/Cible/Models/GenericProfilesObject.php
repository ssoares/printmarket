<?php
/**
 * Generic Profile data
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_GenericProfilesObject
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: GenericProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */

/**
 * Manages Generic Profile data.
 *
 * @category  Cible
 * @package   Cible_GenericProfiles
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: GenericProfilesObject.php 1788 2015-06-18 20:07:52Z ssoares $id
 */
class GenericProfilesObject extends DataObject
{

    protected $_dataClass   = 'GenericProfilesData';
    protected $_indexClass      = '';
    protected $_constraint      = '';
    protected $_foreignKey      = '';
    protected $_searchColumns = array(
        'data' => array(
            'GP_LastName',
            'GP_FirstName',
            'GP_Email')
        );
    protected $_keywords = array();
    protected $_filters = array();
    protected $_profileId = 0;
    protected $_profileType = 0;
    protected $_delField = 'GP_Deleted';
    protected $_forceExact = false;
    protected $_nbDayLimit = 0;
    public function getNbDayLimit(){
        return $this->_nbDayLimit;
    }

    public function setNbDayLimit($nbDayLimit){
        $this->_nbDayLimit = $nbDayLimit;
        return $this;
    }

    public function getDelField()
    {
        return $this->_delField;
    }

    public function setDelField($delField)
    {
        $this->_delField = $delField;
        return $this;
    }

    public function getProfileType(){
        return $this->_profileType;
    }

    public function setProfileType($profileType){
        $this->_profileType = $profileType;
        return $this;
    }

    public function getProfileId()
    {
        return $this->_profileId;
    }

    public function setProfileId($profileId)
    {
        $this->_profileId = (int)$profileId;
        return $this;
    }

    public function getKeywords(){
        return $this->_keywords;
    }

    public function setKeywords($keywords)
    {
//        if (is_array($keywords)){
//            $this->_keywords = array_merge($this->_keywords, $keywords);
//        }else{
//            array_push($this->_keywords, $keywords);
//        }
        $this->_keywords =  $keywords;
        return $this;
    }

    public function setFilters($keywords = array())
    {
        foreach($keywords as $value)
        {
            foreach($this->_searchColumns['data'] as $key){
                $this->_filters[$key] = $value;
            }
        }
    }

    public function autocompleteSearch()
    {
        $result = array();
        $this->_query = parent::getAll(null, false);
        $this->setWhereClause(array('data' => $this->_keywords));
        $data = parent::findData(array(), true);
        foreach($data as $key => $value){
            $result[] = array('id' => $value[$this->_dataId],
                'label' => $value['GP_FirstName'] . ' ' . $value['GP_LastName']
                . ' (' . $value['GP_Email'] . ')',
                'value' => $value['GP_FirstName'] . ' ' . $value['GP_LastName']
                );
        }

        return $result;
    }

    public function saveProfile($data, $langId)
    {
        $filters = array('GP_Email' => $data['GP_Email']);
        $profile = $this->findData($filters);
        if (!empty($profile)){
            $this->_profileId = (int)$profile[0][$this->_dataId];
            if (empty($data[$this->_dataId])){
                $data[$this->_dataId] = $this->_profileId;
            }
            $this->save($this->_profileId, $data, $langId);
        }else{
            $this->_profileId = $this->insert($data, $langId);
        }

        return $this->_profileId;
    }

    public function insert($data, $langId)
    {
        $data = $this->_formatInputData($data);
        unset($data[$this->_dataId]);
        $data['GP_CreaDate'] = date('Y-m-d H:i:s');
        return parent::insert($data, $langId);
    }

    public function save($id, $data, $langId)
    {
        $data = $this->_formatInputData($data);
        parent::save($id, $data, $langId);
    }

    protected function _formatInputData(array $data)
    {
        $data = parent::_formatInputData($data);
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'GP_Password':
                    if (empty($values)){
                        unset($data[$field]);
                    }else{
                        $data[$field] = md5($values);
                    }
                    break;
                default:
                    break;
            }
        }
        return $data;
    }

    public function delete($id)
    {
        $this->save($id, array($this->_delField => 1), 1);
    }

    /**
     * Manage donors data for profiles
     * @param array Data to build profile.
     * @return int Company Id.
     * @throws Exception No profile id.
     */
    public function addDonorProfile($data)
    {
        if (empty($this->_profileId)){
            throw new Exception('No generic profile id defined.', 'ADP-001', null);
        }

        if ($data['DO_TypeDonor'] == 22){
            $oDonor = new DonorsObject();
            $subForm = 'donor';
            $getCie = false;
        }else{
            $oDonor = new CompaniesDonorObject();
            $subForm = 'companiesDonor';
            $getCie = true;
            if (isset($data[$subForm]))
            {
                $contact = $data[$subForm]['contact'];
                //define profile data if contact (2) or not (1)
                // (1) profile id = main contact if contact empty
                // (2) else profile contact = main and related profile = current
                if ($data[$subForm]['isContact'] == 2){
                    $profileId = $this->_profileId;
                    // (2) contact relationship = main (20)
                    $this->saveProfile($contact, 1);
                    // and profile = employee (21)
                    $oRelation = new GenericProfilesAssociationObject();
                    $oRelation->setRelProfileId($this->_profileId)
                        ->setProfileId($profileId);
                    $relExists = $oRelation->getProfileAssocations();
                    empty($relExists) ? $oRelation->setRelationId(21)
                        ->insert(array(), 1) : null;
                }
            }
            // Save profile association with company ID where main id and
            // related are defined
        }
        $addrField = $oDonor->getAddrField();
        if (SESSIONNAME === 'application'){
            $addrDataField = $oDonor->getAddrDataField();
            $donor[$addrDataField] = $data[$subForm];
            $donor[$addrField] = $data[$subForm][$addrField];
            $donor[$oDonor->getForeignKey()] = $this->_profileId;
        }else{
            $donor = $data;
        }

        $donorExist = $oDonor->populate($this->_profileId, 1);
        if (!empty($donorExist)){
            isset($donorExist['CDO_CieId']) ? $donor['CDO_CieId'] = $donorExist['CDO_CieId'] : null;
            isset($donorExist['CIE_AddressId']) ? $donor['CIE_AddressId'] = $donorExist['CIE_AddressId'] : null;
            isset($donorExist[$addrField]) ? $donor[$addrField] = $donorExist[$addrField] : null;
            $oDonor->save($this->_profileId, $donor, 1);
        }else{
            $oDonor->insert($donor, 1);
            $oDonor->save($this->_profileId, $donor, 1);
        }
        $cieId =  null;
        if ($getCie){
            $cieId = $oDonor->getCieId();
        }
        return $cieId;
    }

    /**
     * Test if profile already exists for form validation and defined if name
     * is the same.
     *
     * @param array $data form data
     * @return boolean
     */
    public function validateExistingAccount($data)
    {
        $existsButDifferent = false;
        $filters = array('GP_Email' => $data['GP_Email']);
        $this->_forceExact = true;
        $profile = $this->findData($filters);
        if (!empty($profile)){
            $lname = $profile[0]['GP_LastName'];
            $fname = $profile[0]['GP_FirstName'];
            if ($lname != $data['GP_LastName']
                || $fname != $data['GP_FirstName']){
                $existsButDifferent = true;
            }
        }

        return $existsButDifferent;
    }

    public function findProfilesData()
    {
        if (empty($this->_profileId) || empty($this->_profileType)){
            throw new Zend_Exception('No profile id or no type of profile defined');
        }
        $data = array();
        switch($this->_profileType)
        {
            case 21:

                break;
            case 23:
                $oDonor = new CompaniesDonorObject();
                $data = $oDonor->populate($this->_profileId, 1);
                break;

            default: // 22
                $oDonor = new DonorsObject();
                $data = $oDonor->populate($this->_profileId, 1);
                break;
        }
        if (!empty($data)){
            $data = array_merge($data, $this->populate($this->_profileId, 1));
        }

        return $data;
    }

    public function getNewDonors($cumul = false)
    {
        if ($this->_nbDayLimit != 0){
            $this->_query->reset('where');

            $date = new Zend_Date();
            $week = $date->addWeek($this->_nbDayLimit);
            $weekNum = (int)$week->toValue(Zend_Date::WEEK);
            $dow = $week->toString('e');
            $week->subDay($dow);
            $firstDay = $week->toString('dd-MM-YYYY');
            $firstStr = $date->toString('YYYY-MM-dd');
            $week->addDay(6);
            $lastDay = $week->toString('dd-MM-YYYY');
            $lastStr = $date->toString('YYYY-MM-dd');
            $this->_query->where('(DTR_ResponseDate >= "' . $firstStr . '" '
                . 'AND DTR_ResponseDate <= '.$lastStr.') '
                . 'OR (RT_PlannedDate >= "' . $firstStr . '" AND '
                . 'RT_PlannedDate < '.$lastStr.')')
            ->where('GP_CreaDate >= '.$firstStr.' AND GP_CreaDate <= ' . $lastStr)
            ;

        }
        if ($cumul){
            $this->_query->reset('columns')
                ->columns(array('COUNT('.$this->_dataId.')'));
            $data['##X##'] = $weekNum;
            $data['##START##'] = $firstDay;
            $data['##END##'] = $lastDay;
            $data['##TOTAL##'] = $this->_db->fetchOne($this->_query);
        }else{
            $this->_query->reset('columns')
                ->columns('*');
            $data = $this->_db->fetchAll($this->_query);
        }

        return $data;
    }

}