<?php
/**
 * Blocks
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_Pages
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BlocksObject.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Manage data from items table.
 *
 * @category  Cible
 * @package   Cible_Pages
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BlocksObject.php 1788 2015-06-18 20:07:52Z ssoares $
 */
class BlocksObject extends DataObject
{

    protected $_dataClass   = 'Blocks';
    protected $_dataColumns = array(
        'B_ID' => 'B_ID',
        'B_PageID' => 'B_PageID',
        'B_ModuleID' => 'B_ModuleID',
        'B_ZoneID' => 'B_ZoneID',
        'B_Position' => 'B_Position',
        'B_ShowHeader' => 'B_ShowHeader',
        'B_Draft' => 'B_Draft',
        'B_Online' => 'B_Online',
        'B_Secured' => 'B_Secured',
        'B_FromSite' => 'B_FromSite',
        'B_DuplicateId' => 'B_DuplicateId',
        'B_LastModified' => 'B_LastModified',
    );
    protected $_indexClass      = 'BlocksIndex';
    protected $_indexColumns = array('BI_BlockID' => 'BI_BlockID',
        'BI_LanguageID' => 'BI_LanguageID',
        'BI_BlockTitle' => 'BI_BlockTitle',
    );
    protected $_indexLanguageId = 'BI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'B_PageID';
    protected $_searchColumns = array('B_PageID' => 'B_PageID');

    protected $_siteOrigin = '';
    protected $_pageId = 0;
    protected $_moduleId = 0;

    public function setSiteOrigin($siteOrigin)
    {
        $this->_siteOrigin = $siteOrigin;
        return $this;
    }

    public function setProperties(array $options)
    {
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            if (in_array($property, get_class_vars(get_class())))
                $this->$property = $value;
        }

        return $this;
    }

    public function getBlocksFromRelatedPage()
    {
        $list = array();
        if ($this->_pageId)
        {
            if (!empty($this->_siteOrigin))
            {
                $defaultAdapter = $this->_db;
                $dbs = Zend_Registry::get('dbs');
                $this->_db = $dbs->getDb($this->_siteOrigin);
            }

            $this->_query = $this->getAll(Cible_Controller_Action::getDefaultEditLanguage(), false);
            $this->_query->joinLeft('Modules', 'B_ModuleID = M_ID')
                ->where($this->_foreignKey . ' = ?', $this->_pageId);

            if ($this->_moduleId > 0)
                $this->_query->where('B_ModuleID = ?', $this->_moduleId) ;

            $blocks = $this->_db->fetchAll($this->_query);

            if (!empty($defaultAdapter))
                $this->_db = $defaultAdapter;
            $oParameters = new ParametersObject();
            foreach ($blocks as $key => $block)
            {
                $param = $block[$this->_dataId];
                $parameterData = $oParameters->setBlockId($param)
                    ->setSiteOrigin($this->_siteOrigin)
                    ->getData();
                $block = array_merge($block, $parameterData);
                $list['options'][$param] = $block['BI_BlockTitle'];
                $list['blocks'][$param] = $block;
            }
        }

        return $list;
    }

    public function getViewByModuleID($moduleID)
    {
        $detailsPageView = '';
        $blocks = $this->getBlocksFromRelatedPage();
        if(isset($blocks['blocks']))
        {
            foreach($blocks['blocks'] as $block)
            {
                if($block['B_ModuleID'] == $moduleID){
                    $detailsPageView = $block['Param999'];
                    break;
                }
            }
        }

        return $detailsPageView;
    }

    public function insert($data, $langId)
    {
        if (!empty($this->_siteOrigin) && $data['B_DuplicateId'] > 0)
            $data['B_FromSite'] = $this->_siteOrigin;

        $data['B_PageID'] = $this->_pageId;
        $blockId = $this->_insert ($data, $langId);

        return $blockId;
    }

    private function _insert($data, $langId)
    {
        // get the new block id
        $blockId = parent::insert($data, $langId);
        // Insert Parameters for the current block
        $oParameters = new ParametersObject();
        $oParameters->setBlockId($blockId)
            ->insert($data, $langId);
        // create new row in blockindex table for each language of the website
        $languages = Cible_FunctionsGeneral::getAllLanguage();

        foreach ($languages as $lang)
        {
            if ($lang["L_ID"] != $langId)
            {
                $data['BI_BlockTitle'] .= "_" . $lang["L_Suffix"];
                parent::save($blockId, $data, $lang["L_ID"]);
            }
        }

        $zone = $data['B_ZoneID'];
        $position = $data['B_Position'];
        $pageId = $data['B_PageID'];
        // update position of all block in the same page
        $where = "(B_Position >= " . $position . ") AND B_PageID = " . $pageId . " AND B_ID <> " . $blockId . " AND B_ZoneID = " . $zone;
        $this->_db->update($this->_oDataTableName, array('B_Position' => new Zend_Db_Expr('B_Position + 1')), $where);



        return $blockId;
    }

    public function linksExist()
    {
        $this->_query = parent::getAll(Zend_Registry::get('languageID'), false);
        $this->_query->where('B_PageID = ?', $this->_pageId);
        $this->_query->where('B_DuplicateId > ?', 0);

        $result = $this->_db->fetchAll($this->_query);
        return count($result);
    }

    public function getDuplicateData($id, $langId)
    {
        $duplicateData = array();
        $data = parent::populate($id, $langId);

        if(!empty($data['B_FromSite']) && $data['B_DuplicateId'] > 0)
            $duplicateData = array(
                'B_FromSite' => $data['B_FromSite'],
                'B_DuplicateId' => $data['B_DuplicateId']
                );

        return $duplicateData;
    }

    public function populate($id, $langId)
    {
        if (!empty($this->_siteOrigin))
        {
            $dbs = Zend_Registry::get('dbs');
            $defaultAdapter = $dbs->getDefaultDb();
            $this->_db = $dbs->getDb($this->_siteOrigin);
        }

        $data = parent::populate($id, $langId);
        if (isset($defaultAdapter))
            $this->_db = $defaultAdapter;

        return $data;
    }
}
