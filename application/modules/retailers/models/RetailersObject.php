<?php
/**
 * Cible Solutions -
 * Orders management.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: RetailersObject.php 694 2011-10-28 18:19:58Z ssoares $
 */

/**
 * Manage data in database for the orderss.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class RetailersObject extends DataObject
{
    protected $_dataClass   = 'RetailersData';
    protected $_dataId      = '';
//    protected $_dataColumns = array();
    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_orderField = 'MP_CompanyName';

    protected $_table    = 'MemberProfiles';
    protected $countryId = "A_CountryId";
    protected $stateId   = "A_StateId";
    protected $cityId    = "A_CityId ";

    protected $countriesTable = 'CountriesIndex';
    protected $statesTable    = 'StatesIndex';
    protected $citiesTable    = 'Cities';

    protected $adresseTable = 'AddressIndex';
    protected $adresseTableData = 'AddressData';


    protected $_query;

    public function setOrderField($value)
    {
        $this->_orderField = $value;
    }

    /**
     * Fetch data from retailer table and return an array for the selected
     * one.
     *
     * @param int $memberId The customer id to link with generic profile
     * @param int $langId   The language id
     *
     * @return array
     */
    public function getRetailerInfos($memberId, $langId = 0)
    {
        if (empty($langId))
            Zend_Registry::get('defaultEditLanguage');

        $select = $this->getAll($langId, false);

        $select->where('R_GenericProfileId = ?', $memberId);

        $tmpData = $this->_db->fetchRow($select);
        return $tmpData;

    }

    /**
     * Build a query to find all the retailers to display on web according filter
     *
     * @param array $params     Contains parameters that allow to build a query.
     * @param int   $retailerId Only required if we want to fetch data for a single retailer.
     * @param int   $langId     The language id.
     *
     * @return array
     */
    public function getRetailersDataByCities(array $params, $retailerId = null, $langId = null)
    {
        if (is_null($langId))
            $langId = Zend_Registry::get ('languageID');

        $filters = array(
            'countryId' => array(
                'table'  => $this->countriesTable,
                'joinOn' => $this->countriesTable . '.CI_CountryID = ' . $this->countryId,
                'fields' => array('countryName' => $this->countriesTable . '.CI_Name'),
                'langFilter'=> ' AND ' . $this->countriesTable . '.CI_LanguageID = ' . $langId
            ),
            'stateId' => array(
                'table'  => $this->statesTable,
                'joinOn' => $this->stateId . ' = SI_StateID',
                'fields' => array('stateName' => $this->statesTable . '.SI_Name'),
                'langFilter'=> ' AND ' . $this->statesTable . '.SI_LanguageID = ' . $langId
            ),
            'cityId' => array(
                'table'  => $this->citiesTable,
                'joinOn' => $this->cityId . ' = '.  $this->citiesTable .'.C_ID',
                'fields' => array('cityName' => $this->citiesTable . '.C_Name'),
                'langFilter'=> ' AND ' . $this->citiesTable . '.CI_LanguageID = ' . $langId
            ),
        );
        $select = $this->getAll($langId,false);

        $select->join(
            $this->_table,
            'MP_GenericProfileMemberID=R_GenericProfileId',
            array(
                'RetailerId'  => 'MP_GenericProfileMemberID',
                'CompanyName' => 'MP_CompanyName')
            );
        $select->join(
            $this->adresseTable,
            'AI_AddressId=R_AddressId AND AI_LanguageID = ' . $langId,
            array(
                'Name'          => 'AI_Name',
                'AddressId'     => 'AI_AddressId',
                'FirstAddress'  => 'AI_FirstAddress',
                'SecondAddress' => 'AI_SecondAddress',
                'FirstTel'      => 'AI_FirstTel',
                'FirstExt'      => 'AI_FirstExt',
                'SecondTel'     => 'AI_SecondTel',
                'SecondExt'     => 'AI_SecondExt',
                'Website'       => 'AI_WebSite')
            );
        $select->join(
            $this->adresseTableData,
            'AI_AddressId=A_AddressId AND AI_LanguageID = ' . $langId,
             array(
                'ZipCode' => 'A_ZipCode',
                'Fax'     => 'A_Fax',
                'Email'   => 'A_Email')
            );

        $tmpData = $filters[$params['field']];
        switch ($params['field'])
        {
            case 'countryId':
                $hasStates = Cible_FunctionsGeneral::getStateByCode($params['value'], null, $langId);
                if(count($hasStates) > 0){
                    $select->joinLeft(
                        $this->statesTable,
                        $this->stateId . ' = SI_StateID' . $filters['stateId']['langFilter'],
                        array('stateName' => $this->statesTable . '.SI_Name'))
                    ->joinLeft(
                            $this->citiesTable,
                            $this->cityId . ' = '.  $this->citiesTable .'.C_ID',
                            array('cityName' => $this->citiesTable .'.C_Name'));
                }
                $select->joinLeft(
                    $tmpData['table'],
                    $tmpData['joinOn'] . $tmpData['langFilter'],
                    $tmpData['fields'])
                ->distinct();
                break;
           case 'stateId':
               $select->joinLeft(
                        $this->citiesTable,
                        $this->cityId . ' = '.  $this->citiesTable .'.C_ID',
                        array('cityName' => $this->citiesTable .'.C_Name'))
                ->joinLeft(
                    $tmpData['table'],
                    $tmpData['joinOn'] . $tmpData['langFilter'],
                    $tmpData['fields'])
               ->distinct();
                break;
            case 'cityId':
                $select->joinLeft(
                        $this->statesTable,
                        $this->stateId . ' = SI_StateID' . $filters['stateId']['langFilter'],
                        array('stateName' => $this->statesTable . '.SI_Name'))
                ->joinLeft(
                    $tmpData['table'],
                    $tmpData['joinOn'],
                    $tmpData['fields'])
                 ->distinct();
                break;
            default:
                break;
        }


        $select->where('MP_Status = 2')
            ->where('R_Status = 2')
            ->where( $this->$params['field'] . ' = ?', $params['value'])
            ->order($this->_orderField);

        if($retailerId)
            $select->where ('MP_GenericProfileMemberID = ?', $retailerId);

        $retailers = $this->_db->fetchAll($select);

        return $retailers;
    }
}