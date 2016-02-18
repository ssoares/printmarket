<?php
/**
 * Cible Solutions
 * Retailer management.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Retailer
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: RetailersObject.php 1191 2013-05-14 02:45:42Z ssoares $
 */

/**
 * Manage data for cities
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Retailer
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class RetailersObject extends DataObject
{
    protected $_dataClass   = 'RetailersData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_foreignKey = 'R_GenericProfileId';

    protected $_indexSelectColumns = array();

    public function getRetailerInfos($memberId, $langId = 1)
    {

        $select = $this->getAll($langId, false);

        $select->where('R_GenericProfileId = ?', $memberId);

        $tmpData = $this->_db->fetchRow($select);

        return $tmpData;

    }

    public function save($id, $data, $langId)
    {
        $oAddress = new AddressObject();

        $profile = parent::findData(array('R_GenericProfileId' => $id));
        $currentAddr = $profile[0]['R_RetailerAddressId'];
        $retailerId = $profile[0]['R_RetailerProfileId'];
        $retailer = $data['retailerForm'];
        //If customer doesn't want to add data on website, set to false the field name
        switch ($retailer['isDistributeur'])
        {
            case 1:
                $retailerData = array(
                    'R_Status' => $retailer['isDistributeur']);
                break;
            case 2:
                if ($currentAddr > 0)
                {
                    $retailerData = array(
                        'R_Active' => $retailer['R_Active'],
                        'R_Status' => $retailer['isDistributeur']);
                    $oAddress->save($currentAddr, $retailer, 1);
                    $oAddress->save($currentAddr, $data['retailerFormEn'], 2);
                }
                else
                {
                    $addressId = $oAddress->insert($retailer, 1);
                    $oAddress->save($addressId, $data['retailerFormEn'], 2);
                    $retailerData = array(
                        'R_GenericProfileId' => $id,
                        'R_RetailerAddressId' => $addressId,
                        'R_Status' => $retailer['isDistributeur'],
                        'R_Active' => $retailer['R_Active']
                    );
                }
                break;
            default:
                break;
        }

        parent::save($retailerId, $retailerData, $langId);
    }

    public function findData($filters = array())
    {
        $oAddress = new AddressObject();
        $data     = parent::findData($filters);
        if (!empty($data))
        {
            $data = $data[0];
            $address = $oAddress->getAll(null, true, $data['R_RetailerAddressId']);

            foreach ($address as $addr)
            {
                foreach ($addr as $key => $value)
                {
                    if (preg_match('/^AI_/', $key) && $addr['AI_LanguageID'] == 2 )
                        $data['retailerFormEn'][$key] = $value;
                    else
                        $data['retailerForm'][$key] = $value;
                }

            }

            $data['retailerForm']['isDistributeur'] = $data['R_Status'];
            $data['retailerForm']['R_Active'] = $data['R_Active'];
        }

        return $data;
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