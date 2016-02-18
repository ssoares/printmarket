<?php
/**
 * Class FormResponseObject -
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormResponseObject management
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormResponseOptionObject extends DataObject
{
    protected $_dataClass   = 'FormResponseOption';
    protected $_dataId      = 'FRO_ID';
    protected $_dataColumns = array(
            'FRO_QuestionID' => 'FRO_QuestionID',
            'FRO_Type'       => 'FRO_Type',
            'FRO_Seq'        => 'FRO_Seq',
            'FRO_Default'    => 'FRO_Default',
            'FRO_Other'      => 'FRO_Other'
        );

    protected $_indexClass      = 'FormResponseOptionIndex';
    protected $_indexId         = 'FROI_ResponseOptionID';
    protected $_indexLanguageId = 'FROI_LanguageID';
    protected $_indexColumns    = array(
            'FROI_Label' => 'FROI_Label'
    );

    protected $_dataRespOpt    = array();

    /**
     * Class constructor
     *
     * @return void
     */
    public function  __construct()
    {
        parent::__construct();
    }

    /**
     * Get data and build the list for the options in select and choices.
     *
     * @param int $optionId Id of the response option.
     * @param int $langId Id of the language.
     *
     * @return string $html
     */
    public function getOptionsList($questId, $langId)
    {
        $this->_selectResponseOptions($questId, $langId);
        
        return $this->_dataRespOpt;

    }

    protected function _selectResponseOptions($questId, $langId = null)
    {
        $oRespOpt       = new FormResponseOption();
        $oRespOptIndex  = new FormResponseOptionIndex();

        $select = $oRespOpt->select()
                ->setIntegrityCheck(false)
                ->from(array('FRO' => $oRespOpt->info('name')))
                ->joinLeft(
                        array('FROI' => $oRespOptIndex->info('name')),
                        'FROI_ResponseOptionID = FRO_ID',
                        array('FROI_Label'))
                ->where('FRO_QuestionID = ?', $questId);


        if($langId != null)
        {
            $select->where('FROI_LanguageID = ?', $langId);
        }

        $select->order('FRO_Seq');

        $respOptions = $oRespOpt->fetchAll($select)->toArray();

        $this->_dataRespOpt = $respOptions;
    }
}