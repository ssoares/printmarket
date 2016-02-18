<?php
/**
 * Class FromQuestionObject - Manage questions data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormQuestionObject - Manage questions data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormQuestionObject extends DataObject
{
    protected $_dataClass   = 'FormQuestion';
    protected $_dataId      = 'FQ_ID';
    protected $_dataColumns = array(
            'FQ_ElementID' => 'FQ_ElementID',
            'FQ_TypeID'    => 'FQ_TypeID',
        );

    protected $_indexClass      = 'FormQuestionIndex';
    protected $_indexId         = 'FQI_QuestionID';
    protected $_indexLanguageId = 'FQI_LanguageID';
    protected $_indexColumns    = array(
            'FQI_Title' => 'FQI_Title'
    );
    protected $_baseUrl;


    public function  __construct()
    {
        $this->_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        parent::__construct();
    }

    public function show($elemId = '', $questionType = null, $langId = 1, $elemSeq = null)
    {
        $question = array();

        if ($elemId != '')
        {
            $questions   = $this->_selectQuestions($elemId, $langId);
            // Put data for question management into the questions array
            foreach ($questions as $index => $question)
            {
                $questionId = $question['FQ_ID'];
                //get the validations to perform before form validation
                $questions[$index]['validators'] = $this->_getValidationData($questionId);
                //get options to display a question (order ...)
                $questions[$index]['options']    = $this->_getQuestionOptions($questionId);
                //get response options to create select list, choices list
                $questions[$index]['responseOption'] = $this->_getResponseOption(
                    $questionId,
                    $langId
                    );

            }
        }

        return $questions;
    }

    /**
     * Get questions data from db
     *
     * @param int $elemId
     * @param int $langId
     * @param int $questType
     *
     * @return array
     */
    protected function _selectQuestions($elemId, $langId = 1, $questType = null)
    {
        $oQuestion  = new FormQuestion();
        $oQstIndex  = new FormQuestionIndex();
        $oType      = new FormQuestionType();

        $select = $oQuestion->select()
                ->setIntegrityCheck(false)
                ->from(array('FQ' => $oQuestion->info('name')))
                ->joinLeft(
                        array('FQI' => $oQstIndex->info('name')),
                        'FQI_QuestionID = FQ_ID',
                        array('FQI_Title'))
                ->joinRight(
                        $oType->info('name'),
                        'FQ_TypeID = FQT_ID',
                        array('FQT_TypeName','FQT_ImageLink')
                        )
                ->where('FQ_ElementID = ?', $elemId);


        if($langId != null)
        {
            $select->where('FQI_LanguageID = ?', $langId);
//            $select->where('FQTI_LanguageID = ?', $langId);
        }

        if($questType != null)
        {
            $select->where('FQ_TypeID = ?', $questType);
        }

        $questions = $oQuestion->fetchAll($select)->toArray();

        return $questions;
    }

    /**
     * Get the validators on page loading
     *
     * @param int $questionId
     * @param int $langId
     *
     * @return array
     */
    protected function _getValidationData($questionId, $langId = 1)
    {
        $oValidator   = new FormQuestionValidationObject();
        $validators   = $oValidator->getValidators($questionId);

        return $validators;
    }

     /**
     * Get the question options on page loading
     *
     * @param int $questionId
     * @param int $langId
     *
     * @return array
     */
    protected function _getQuestionOptions($questionId, $langId = 1)
    {
        $data     = array();
        $oOptions = new FormQuestionOptionObject();
        $data  = $oOptions->getOptions($questionId);

        return $data;
    }

    /**
     * Get the list of vlaues for select list or multi choices.
     *
     * @param int $optionId
     * @param int $langId
     *
     * @return array
     */
    protected function _getResponseOption($optionId, $langId = 1)
    {
        $oResponseoption  = new FormResponseOptionObject();
        $optionsList      = $oResponseoption->getOptionsList($optionId, $langId);

        return $optionsList;

    }
}