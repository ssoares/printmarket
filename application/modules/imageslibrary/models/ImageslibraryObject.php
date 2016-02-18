<?php
/**
 * Module Imageslibrary
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryObject.php 356 2013-02-01 04:22:26Z ssoares $
 */

/**
 * Manage data from Imageslibrary table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryObject.php 356 2013-02-01 04:22:26Z ssoares $
 */
class ImageslibraryObject extends DataObject
{
    protected $_dataClass       = 'ImageslibraryData';
    protected $_indexClass      = 'ImageslibraryIndex';
    protected $_indexLanguageId = 'ILI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'IL_ID';
    protected $_description     = 'ILI_Description';
    protected $_position        = 'IL_Seq';
    protected $_query;
    protected $_blockID = 0;
    protected $_blockParams = array();

    public function setBlockID($blockID)
    {
        $this->_blockID = $blockID;
    }
    public function getBlockParams( $field = '')
    {
        if (!empty($field))
            return $this->_blockParams[$field];

        return $this->_blockParams;

    }

     public function setBlock(array $block = null)
    {
        if ($block)
            $this->_block = $block;
        else
        {
            $oBlock = new BlocksObject();
            $this->_block = $oBlock->setSiteOrigin($this->_fromSite)
                ->populate($this->_blockID, Zend_Registry::get('languageID'));
        }

        $this->_blockParams = $this->_blockParams + $this->_block;

        return $this;
    }

    public function getBlock()
    {
        return $this->_block;
    }


    public function setBlockParams(array $blockParams = array())
    {
        if( $this->_blockID )
        {
            $_params = Cible_FunctionsBlocks::getBlockParameters( $this->_blockID );

            foreach( $_params as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
        else
        {
            foreach( $blockParams as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
    }

    public function populate($id, $langId)
    {
        $data = array();
        $oRef = new ImageslibraryKeywordsObject();
        $data = parent::populate($id, $langId);
        $refIds = $oRef->getData($id, $langId);
        $data = array_merge($data, $refIds);

        return $data;
    }

    public function getAllCollections($lang)
    {
        $select = $this->getAll($lang, false);
        $select->joinLeft('L_ProductLines', 'C_ProductLineID = PL_ID', array())
            ->joinLeft('L_ProductLinesIndex', 'PL_ID = PLI_ProductLineID')
            ->where('CI_LanguageID = ?', $lang)
            ->where('PLI_LanguageID = ?', $lang)
            ->order('PLI_Name ASC')
            ->order('PLI_ORDER ASC')
            ->order('CI_Name ASC');

        $collections = $this->_db->fetchAll($select);


        $prev = '';
        foreach ($collections as $collection)
        {
            if ($prev == $collection['PLI_Name'] || empty($prev))
                $col[$collection['C_ID']] = $collection['CI_Name'];
            else
            {
                $data[$prev] = $col;
                $col = array();
                $col[$collection['C_ID']] = $collection['CI_Name'];
            }

            $prev = $collection['PLI_Name'];
        }

        return $data;
    }


    public function getAllImagelibrary($select)
    {
        return $this->_db->fetchAll($select);
    }

    public function insert($data, $langId)
    {
        $id = parent::insert($data, $langId);
        $oRef = new ImageslibraryKeywordsObject();
        $oRef->save($id, $data, $langId);

        return $id;
    }

    public function save($id, $data, $langId)
    {
        $oRef = new ImageslibraryKeywordsObject();
        $oRef->save($id, $data, $langId);
        return parent::save($id, $data, $langId);
    }

    public function delete($id)
    {
        $oRef = new ImageslibraryKeywordsObject();
        $oRef->delete($id);
        parent::delete($id);
    }
}