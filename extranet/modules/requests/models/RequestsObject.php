<?php
/**
 * Module Requests
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RequestsCategoriesObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */

/**
 * Manage data from colletion table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RequestsCategoriesObject.php 1303 2013-10-25 20:37:48Z ssoares $id
 */
class RequestsObject extends DataObject
{
    protected $_dataClass       = 'RequestsData';
    protected $_indexClass      = 'RequestsIndex';
    protected $_indexLanguageId = 'REI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'RE_ReportType';
    protected $_titleField      = 'REI_Label';
    protected $_orderBy         = array('');
    protected $_query;
    protected $_addSubFolder    = false;
    protected $_name            = 'requests';
    protected $_reportObj       = null;
    protected $_reType          = array();
    protected $_data            = array();
    protected $_config          = array();

    public function loadModuleConfig()
    {
        $root = dirname(__DIR__) . '/config/config.ini';
        $this->_config = new Zend_Config_Ini($root);
        return $this;
    }

    public function getConfig(){
        return $this->_config;
    }

    public function getReType(){
        return $this->_reType;
    }

    public function setReType($reType){
        $this->_reType = $reType;
        return $this;
    }

    public function getReportObj(){
        return $this->_reportObj;
    }

    public function setReportObj($reportObj){
        $this->_reportObj = $reportObj;
        return $this;
    }

    public function getName(){
        return $this->_name;
    }

    public function getTitleField(){
        return $this->_titleField;
    }

    public function insert($data, $langId){
        $data = parent::_formatInputData($data);
        $data['RE_CreaDate'] = date('Y-m-d H:i:s');
        $id = parent::insert($data, $langId);
        $oRules = new RequestRulesObject();
        $oRules->setRequestId($id)->saveRules($data['filterSet']);
        return $id;
    }

    public function save($id, $data, $langId)
    {
        $data = parent::_formatInputData($data);
        $oRules = new RequestRulesObject();
        $oRules->setRequestId($id)->saveRules($data['filterSet']);
        return parent::save($id, $data, $langId);
    }

    public function delete($id)
    {
        $oRules = new RequestRulesObject();
        $oRules->delete($id);
        parent::delete($id);
    }

    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'xxxx':
                    $data[$field] = explode(',', $values);

                    break;

                default:
                    break;
            }
        }

        return $data;
    }

    public function populate($id, $langId)
    {
        $this->_data = $this->_formatOutputData(parent::populate($id, $langId));
        $oRules = new RequestRulesObject();
        $this->_data['filterSet'] = $oRules->setRequestId($id)->loadRules();
        return $this->_data;
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
        $filePath = $path . '/data/files/' . $module;
        if (!is_dir($filePath)){
            mkdir($filePath);
        }
        if (!is_dir($imgPath)){
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }

    public function _reportTypesSrc()
    {
        $obj = new ReportTypeObject();
        return $obj->getList();
    }

    public function _campaignSrc(){
        $obj = new CampaignsObject();
        return $obj->getList();
    }
    public function _salutationSrc(){
        return Cible_FunctionsGeneral::getSalutations();
    }
    public function _languageSrc(){
        return Cible_FunctionsGeneral::getFilterLanguages();
    }
    public function _teamsSrc(){
        $obj = new TeamsObject();
        return $obj->getList();
    }
    public function _payModeSrc(){
        $obj = new ReferencesObject();
        return $obj->getListValues('modePay');
    }
    public function _donationStatusSrc(){
        $obj = new ReferencesObject();
        return $obj->getListValues('donationStatus');
    }
    public function _typeDonationSrc(){
        $obj = new ReferencesObject();
        return $obj->getListValues('typeDonation');
    }

    public function getFiltersForModule($langId, $moduleId)
    {
        $this->getAll($langId, false);
        $this->_query->joinLeft('ReportsTypes', 'RE_ReportType = RET_Id')
            ->where('RET_ModuleId = ?', $moduleId);

        return $this->_db->fetchAll($this->_query);
    }

    public function buildRequest($cfg)
    {
        $this->_query = $this->_reportObj->setRequestOptions($this->_reType)->getDefaultRequest();
        $whereClause = array();
        foreach($this->_data['filterSet'] as $criteria)
        {
            $where = array();
            foreach($criteria as $crit)
            {
                $fieldId = $crit['filterSet'];
                $obj = $cfg['fields'][$fieldId]['belongsTo'];
                $opId = $crit['operators'];
                $field = $cfg['fields'][$fieldId]['field'];
                $col = empty($cfg['fields'][$fieldId]['column']) ?
                    $this->_reportObj->getColByFilter($field) : $cfg['fields'][$fieldId]['column'];
                $type = $cfg['fields'][$fieldId]['type'];
                $type = ($type == 'numeric' || $type == 'date') ? 'numdate' : $type;
                $op = $cfg['operators'][$type][$opId]['value'];
                $tmp = new $obj();
                $isJoined = array_key_exists($tmp->getDataTableName(), $this->_query->getPart('from'));
                if (!$isJoined){
                    $this->_query = $tmp->setQuery($this->_query)
                        ->joinFetchData(false, $this->_defaultEditLanguage);
                }
                if (($opId == 3 || $opId = 4) && $type == 'varchar'){
                    $crit['filterValue'] = '%' . str_replace(' ', '%', $crit['filterValue']) . '%';
                }
                $where[] = $this->_db->quoteInto($col .' '. $op .' ?', $crit['filterValue']);
            }
            $whereClause[] = implode(' AND ', $where);
        }
        if (!empty($whereClause)){
            $this->_query->where('(' . implode(') OR (', $whereClause) . ')');

        }
        return $this->_query;
    }

}