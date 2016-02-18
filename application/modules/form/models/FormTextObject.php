<?php
/**
 * Class FromTextObject - Manage texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormTextObject - Manage Texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormTextObject extends DataObject
{
    protected $_dataClass   = 'FormTextData';
    protected $_dataId      = 'FT_ID';
    protected $_dataColumns = array(
            'FT_ElementID' => 'FT_ElementID'
        );

    protected $_indexClass      = 'FormTextIndex';
    protected $_indexId         = 'FTI_FormTextID';
    protected $_indexLanguageId = 'FTI_LanguageID';
    protected $_indexColumns    = array(
            'FTI_Text' => 'FTI_Text'
    );

    protected $_elementId;
    protected $_langId;

    /**
     * Returns all the texts for the current element
     *
     * @param int $elemId Id of the current element
     * @param int $langId Id of the language
     *
     * @return array $txtList Array with the texts data
     */
    public function getTexts($elemId, $langId)
    {
        $txtList = array();

        $this->_elementId = $elemId;
        $this->_langId    = $langId;

        $txtList = $this->_selectTexts();

        return $txtList;
    }

    /**
     * Find data from the text tables.
     *
     * @param int $elemId The text id to find and get its data
     * @param int $langId The language id
     *
     * @return array $textList
     */
    protected function _selectTexts()
    {
        $oText    = new FormTextData();
        $oTxtType = new FormTextIndex();

        $select = $oText->select()
                ->setIntegrityCheck(false)
                ->from(array('FT' => $oText->getName()))
                ->joinInner(
                        array('FTI' => $oTxtType->info('name')),
                        'FTI_FormTextId = FT_ID',
                        'FTI_Text')
                ->where('FT_ElementID = ?', $this->_elementId);

        if($this->_langId != null)
        {
            $select->where($this->_indexLanguageId . ' = ?', $this->_langId);
        }

        $txtList = $oText->fetchRow($select)->toArray();

        return $txtList;
    }
}