<?php
/**
 * Module Utilities
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerFeaturedImageObject.php 160 2011-07-05 04:21:41Z ssoares $
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: BannerFeaturedImageObject.php 160 2011-07-05 04:21:41Z ssoares $
 */
class BannerFeaturedImageObject extends DataObject
{
    protected $_dataClass   = 'BannerFeaturedImageData';

    protected $_indexClass      = 'BannerFeaturedImageIndex';
    protected $_indexLanguageId = 'IFI_LanguageID';

    protected $_constraint      = '';
    protected $_foreignKey      = '';
    protected $_query;

    public function setQuery( Zend_Db_Select $query)
    {
        $this->_query = $query;
    }

    public function getData($langId = null, $bannerId = null)
    {
        $select = parent::getAll($langId, false);
        $select->order('IF_ImgID ASC');

        if ($bannerId)
            $select->where('IF_DataID = ?', $bannerId);



        $data = $this->_db->fetchAll($select);

        return $data;
    }

    public function completeQuery($langId = null, $array = true)
    {
        $select = '';

        if (!empty($this->_query))
        {
            $select = $this->_query;
            $select->joinLeft($this->_oDataTableName, 'BF_ID = IF_DataID')
                ->joinLeft($this->_oIndexTableName, $this->_dataId . ' = IFI_ImgDataID')
                ->order('IF_ImgID ASC');

            if (!is_null($langId))
                $select->where($this->_indexLanguageId . ' = ?', $langId);

            if ($array)
            {
                $data = $this->_db->fetchAll($select);
                if (empty ($data))
                {
                    $where = $select->getPart('where');
                    $select->reset(Zend_Db_Select::WHERE);
                    $select->where($where[0]);
                    $data = $this->_db->fetchAll($select);
                }

                return $data;
            }
            else
            {
//                return $select;
            }
        }
    }

    public function delAssociatedImg($dataId)
    {
        if (empty($dataId))
            Throw new Exception('Parameter id is empty.');

        $db = $this->_db;

        if (!empty($this->_indexClass))
        {
            $data = $this->getData(null, $dataId);
            foreach ($data as $img)
            {
                $db->delete($this->_oIndexTableName, $db->quoteInto("{$this->_indexId} = ?", $img['IF_ID']));
            }
        }

        $db->delete($this->_oDataTableName, $db->quoteInto("IF_DataID = ?", $dataId));
    }
}