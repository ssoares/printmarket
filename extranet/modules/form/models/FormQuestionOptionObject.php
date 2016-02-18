<?php
/**
 * Class FormQuestionOptionObject - Manage Option data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormQuestionOptionObject - Manage Option data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormQuestionOptionObject extends DataObject
{
    protected $_dataClass   = 'FormQuestionOption';
    protected $_dataId      = 'FQO_ID';
    protected $_dataColumns = array(
            'FQO_QuestionID'     => 'FQO_QuestionID',
            'FQO_TypeID'         => 'FQO_TypeID',
            'FQO_Value'          => 'FQO_Value'
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
        $dataObject = new FormQuestionOption();

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
                    'FQO_QuestionID',
                    'FQO_TypeID',
                    'FQO_Value')
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
            if($key == 'FQV_QuestionValidationID')
            {
                $this->_updateParams['FQO_QuestionID'] = $value;
                unset($this->_updateParams[$key]);
            }
            else
            {
                $newKey = str_replace("FQV_", "FQO_", $key);
                $this->_updateParams[$newKey] = $value;
                unset($this->_updateParams[$key]);
            }

        }
        $updates = $this->cleanup($this->_updateParams);
        
        $db = $this->_db;
        $db->update(
            $this->_oData->info('name'),
            $updates,
            array(
                'FQO_QuestionID = "' . $updates['FQO_QuestionID'] . '"' ,
                'FQO_TypeID = "' . $updates['FQO_TypeID'] .'"')
            );
        exit;
    }
}