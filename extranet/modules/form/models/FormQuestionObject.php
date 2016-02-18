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
    protected $_increments = array();


    public function  __construct()
    {
        $this->_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        parent::__construct();

        $query = $this->_db->select()
            ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
            ->where( $this->_db->quoteInto('TABLE_NAME = ?', 'Form_Element')
                .' OR '
                . $this->_db->quoteInto('TABLE_NAME = ?', 'Form_Question'))
            ->where('TABLE_SCHEMA = ?', $this->_schema);

        $this->_increments = $this->_db->fetchAll($query);
    }

    public function show($elemId = '', $questionType = null, $langId = 1, $elemSeq = null)
    {
        $question = array();

        if ($elemId != '')
        {
            $question = $this->_selectQuestions($elemId, $langId);

            if (empty ($question))
            {
                $question = $this->_selectQuestions($elemId, Cible_Controller_Action::getDefaultEditLanguage());
            }

            $questionType = $question[0]['FQ_TypeID'];
            $className    = 'Question' . $question[0]['FQT_TypeName'] . 'Object';

            $oQuestion    = new $className();


           $html = '<li questiontype="text" elementtype="question"
                class="element element_question section_element_question_li"
                style="display: list-item;" id="element_'. $elemId .'">';
            $html .= $oQuestion->showQuestion(
                        $question,
                        $elemId,
                        $elemSeq,
                        null,
                        $langId
                    );
            $html .= "</li>";

        }
        elseif ($questionType != null)
        {
            $oQuestTyp = new FormQuestionTypeObject();
            $types     = $oQuestTyp->populate($questionType, $langId);
            $className = 'Question' . $types['FQT_TypeName'] . 'Object';
            $oQuestion = new $className();

            $html = $oQuestion->showQuestion(
                        $question,
                        $elemId,
                        $elemSeq,
                        $types,
                        $langId
                    );
        }

        return $html;
    }

    /**
     * Delete questions and linked data.
     *      If the process is called from the element each question will
     *      be processed and relateded data will be deleted too.
     *
     * @param int     $id  Id of the element or of the question itself.
     * @param boolean $all Set the process to use.
     *                     If false then id = question id.
     *
     * @return boolean
     */
    public function deleteAll ($id, $all = false)
    {
        $deleted     = true;
        $del         = 0;
        $oOptions    = new FormQuestionOption();
        $oValidation = new FormQuestionValidation();
        $oRespOption = new FormResponseOptionObject();

        // If the method is called with wrong parameters
        if ((!$all && empty($id)))
            throw new Exception('Erreur de paramètres');

        // If deletion is called by deletion of element

        if ($all)
        {
            //Select all the elements of the section
            $questions = $this->_selectQuestions($id);

            if (count($questions) > 0)
            {
                //Delete linked data
                foreach ($questions as $question)
                {
                    //options
                    $del += $oOptions->delete('FQO_QuestionID = "'
                        . $question['FQ_ID'] . '"');
                    //Validation
                    $del += $oValidation->delete('FQV_QuestionID = "'
                        . $question['FQ_ID'] . '"');
                    //ResponseOption
                    $del += $oRespOption->deleteAll($question['FQ_ID']);

                   if ($del == 0) // || $delRespOpt == 0
                    {
                        $deleted = false;
                        break;
                    }else{
                        $this->delete($question['FQ_ID']);
                    }
                }
            }
        // If deletion is done for only a defined section
        }else
        {
            // call the elements to be deleted
            $delOpt = $oOptions->delete('FQO_QuestionID = "' . $id . '"');
            //Validation
            $delVal = $oValidation->delete('FQV_QuestionID = "' . $id . '"');
            //ResponseOption
//            $delRespOpt = $oRespOption->delete($id);

            if ($delOpt == 0 || $delVal == 0 ) //|| $delRespOpt == 0
            {
                $deleted = false;
                break;
            }else{
                $this->delete($id);
            }

        }

        return $deleted;
    }

    protected function _selectQuestions($elemId, $langId = null, $questType = null)
    {
        $oQuestion  = new FormQuestion();
        $oQstIndex  = new FormQuestionIndex();
        $oType      = new FormQuestionType();
        $oTypeIndex = new FormQuestionTypeIndex();

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
                ->joinRight(
                        $oTypeIndex->info('name'),
                        'FQTI_QuestionTypeID = FQT_ID',
                        array('FQTI_Title', 'FQTI_Description')
                        )
                ->where('FQ_ElementID = ?', $elemId);


        if($langId != null)
        {
            $select->where('FQI_LanguageID = ?', $langId);
            $select->where('FQTI_LanguageID = ?', $langId);
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
    protected function _getValidator($questionId, $langId = 1)
    {
        $htmlVal = '';
        $htmlOpt = '';
        $data    = array();

        $oValidation  = new FormQuestionValidationTypeObject();
        $oValidator   = new FormQuestionValidationObject();

        //List of validators for the question.
        $validators = $oValidator->getValidators($questionId);

        foreach ($validators as $validator)
        {
            if ($validator['FQVT_Category'] == "VAL")
            {
                $htmlVal .= $oValidation->getValidationType(
                    $validator['FQV_TypeID'],
                    $langId,
                    $validator['FQV_Value']);
            }
            else
            {
                $htmlOpt .= $oValidation->getValidationType(
                    $validator['FQV_TypeID'],
                    $langId,
                    $validator['FQV_Value']);
            }
        }

        $data['htmlVal'] = $htmlVal;
        $data['htmlOpt'] = $htmlOpt;

        return $data;
    }

    protected function _getResponseOption($questionId, $langId = 1)
    {

    }
}