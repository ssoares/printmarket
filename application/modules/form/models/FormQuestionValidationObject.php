<?php
/**
 * Class FormQuestionValidationObject - Manage validation data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormQuestionValidationObject - Manage validation data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormQuestionValidationObject extends DataObject
{
    protected $_dataClass   = 'FormQuestionValidation';
    protected $_dataId      = 'FQV_QuestionID';
    protected $_dataColumns = array(
            'FQV_QuestionID'     => 'FQV_QuestionID',
            'FQV_TypeID'         => 'FQV_TypeID',
            'FQV_Value'          => 'FQV_Value',
            'FQV_QuestionCondID' => 'FQV_QuestionCondID'
        );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();

    protected $_dataTableName;
    protected $_oData;
    protected $_updateParams = array();

    /**
     * Class constructor
     * 
     * @param array $options
     *
     * @return void
     */
    public function  __construct($options = null)
    {
        parent::__construct();
        $this->_oData          = new $this->_dataClass();
        $this->_dataTableName = $this->_oData->info('name');
        $this->_updateParams  = $options;
    }

    /**
     * Gets the validators for the current question
     *
     * @param int $questionId Id of the question to load.
     *
     * @return array 
     */
    public function getValidators($questionId)
    {
        $select = $this->_db->select()
            ->from(
                $this->_dataTableName,
                array(
                    'FQV_QuestionID',
                    'FQV_TypeID',
                    'FQV_Value',
                    'FQV_QuestionCondID')
                )
            ->joinLeft('Form_QuestionValidationType',
                    'FQVT_ID = FQV_TypeID',
                    'FQVT_Category')
            ->where('FQV_QuestionID = ?', $questionId);
        
        return $this->_db->fetchAll($select);
    }
}