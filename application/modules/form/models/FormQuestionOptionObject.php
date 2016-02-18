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
     * Gets the validators for the current question
     *
     * @param int $questionId Id of the question to load.
     *
     * @return array 
     */
    public function getOptions($questionId)
    {
        $select = $this->_db->select()
            ->from(
                $this->_dataTableName,
                array(
                    'FQO_QuestionID',
                    'FQO_TypeID',
                    'FQO_Value')
                )
            ->where('FQO_QuestionID = ?', $questionId);
        
        return $this->_db->fetchAll($select);
    }
}