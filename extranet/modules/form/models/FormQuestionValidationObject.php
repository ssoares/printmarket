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
     * Insert into database data of the new validator
     *
     * @param array $data Data of the new validator
     * @param int   $lang Langauge id
     *
     * @return integer
     */
    public function insert($data, $lang)
    {
        if (empty($data))
            Throw new Exception('Parameter data is empty.');

        // Creates the data entry in the ObjectData Table
        $dataObject = new FormQuestionValidation();

        $_row = $dataObject->createRow($data);
        
        $_row->save();

        $_dataId = $this->_dataId;
        $_insertedId = $_row->$_dataId;

        return $_insertedId;
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

    public function update()
    {
        foreach ($this->_updateParams as $key => $value) {
            if($key == 'FQV_QuestionValidationID' || $key == 'FQV_ID')
            {
                $this->_updateParams['FQV_QuestionID'] = $value;
            }
        }
        $updates = $this->cleanup($this->_updateParams);
        
        $db = $this->_db;
        $nb = $db->update(
            $this->_oData->info('name'),
            $updates,
            array(
                'FQV_QuestionID = "' . $updates['FQV_QuestionID'] . '"' ,
                'FQV_TypeID = "' . $updates['FQV_TypeID'] .'"')
            );

        return $nb;
    }
}