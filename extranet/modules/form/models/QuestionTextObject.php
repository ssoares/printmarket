<?php
/**
 * Class QuestionTextObject - 
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class QuestionTextObject extends FormQuestionObject
{
    const TYPE_QUESTION = 1;
    
    protected $_icon;
    protected $_formElemId = 'element_1';
    protected $_qTextId    = 'question_1';
    protected $_qValidId   = 'questionValidation_1';
    protected $_title      = '';
    protected $_descr      = '';
    protected $_htmlVal    = '';
    protected $_htmlOpt    = '';
    protected $_elemSeq    = 1;
    protected $_label      = 'Label de la question';
    protected $_checked    = '';

    private $_currentElmId = 1;
    private $_currentQstId = 1;
    
    public function  __construct()
    {
        parent::__construct();

        $this->_currentElmId = $this->_increments['0']['AUTO_INCREMENT'];
        $this->_currentQstId = $this->_increments['1']['AUTO_INCREMENT'];

    }

    public function showQuestion($data, $elemId = '', $elemSeq = null, $type = array(), $langId = '')
    {
        $html    = '';
        $htmlVal = '';
        $htmlOpt = '';
        $params  = array();

        $oValidation = new FormQuestionValidationTypeObject();

        // If loading an existing element (page loading)
        if(count($data) > 0)
        {
            // To simplify when settings registred values
            $question   = $data[0];

            //Load data for validation
            $valOpt  = $this->_getValidator($question['FQ_ID'], $langId);
            $htmlVal = $valOpt['htmlVal'];
            $htmlOpt = $valOpt['htmlOpt'];
            
            // set values from db to build the question
            $this->_formElemId = "element_" . $question['FQ_ElementID'];
            $this->_qTextId    = "question_" . $question['FQ_ID'];
            $this->_qValidId   = "questionValidation_" . $question['FQ_ID'];
            $this->_title      = $question['FQTI_Title'];
            $this->_descr      = $question['FQTI_Description'];
            $this->_icon       = $this->_baseUrl . $question['FQT_ImageLink'];
            $this->_htmlVal    = $htmlVal;
            $this->_htmlOpt    = $htmlOpt;
            $this->_elemSeq    = $elemSeq;
            $this->_label      = $question['FQI_Title'];
            $this->_checked    = '';
            // render the question
            $html = $this->_render();
            
        }
        // If adding a new element with this type of question
        else
        {
            $db         = Zend_Registry::get('db');
            $tableElem  = new FormElement();
            $tableQst   = new FormQuestion();
            // Set values to build the new question
            // Defined default values
            $this->_title      = $type['FQTI_Title'];
            $this->_descr      = $type['FQTI_Description'];
            $this->_icon       = $this->_baseUrl . $type['FQT_ImageLink'];
            $this->_formElemId = "element_" . $this->_currentElmId;
            $this->_qTextId    = "question_" . $this->_currentQstId;
            $this->_qValidId   = "questionValidation_" . $this->_currentQstId;
            
            // Set the validation type for this type of question
            $this->_htmlVal = $oValidation->getValidationType(1, $langId);
            // Set the option for this question type
//            $this->_htmlOpt = $oValidation->getValidationType(2, $langId);
//            $this->_htmlOpt .= $oValidation->getValidationType(3, $langId);
            // render the new question
            $html = $this->_render();
        }

        return $html;
    }

    /**
     * Build the html code for the question of type text.
     * This type will represent a label and an input text field for the front end.
     *
     * @access private
     * @param array $params Data nedeed for the fields of the question
     *                      icon       Path to the icon image
     *                      formElemId Id of the element
     *                      elemSeq    Sequence of the element (position)
     *                      label      Value for the question text in front end
     *                      qTextId    Id of the question
     *                      title      Name(type) of this question
     *                      descr      Description of the question
     *                      htmlOpt    The html code for the options of
     *                                 the question in order to make validation
     * @return string
     */
    private function _render()
    {
        // Define the id of the question type, used in the jQuery.
        $typeQuestion = self::TYPE_QUESTION;
        
        $html  = chr(13);
        $html .= "  <div id=\"" . $this->_qTextId ."\" class='question_" . $typeQuestion . "'>" . chr(13);
        $html .= "      <div class='header'>" . chr(13);
        $html .= "          <img class='sortableElement' alt='' src='" . $this->_icon . "' />" . chr(13);
        $html .= "          <p class='title'>" . $this->_title . "" . chr(13);
        $html .= "          <span class='description'>" . $this->_descr . "</span></p>" . chr(13);
        $html .= "          <form id='". $this->_formElemId ."' action='' class='hidden'>" . chr(13);
        $html .= "              <input id='FE_Seq' class='hidden' value='". $this->_elemSeq ."'/>" . chr(13);
        $html .= "          </form>" . chr(13);
        $html .= "          <div class='floatRight'>" . chr(13);
        $html .= "              <p class='links'>" . chr(13);
        $html .= "                  <a class='question_delete_link' elementType='question'>" . Cible_Translation::getCibleText('button_delete') . "</a>" . chr(13);
        $html .= "              </p>" . chr(13);
        $html .= "          </div>" . chr(13);
        $html .= "      </div>" . chr(13);

        $html .= "      <div class='center' style='clear:both;'>" . chr(13);
        $html .= "          <form id='". $this->_qTextId ."' action=''>" . chr(13);
        $html .= "              <span>" . Cible_Translation::getCibleText('form_question_text_label') . "</span>" . chr(13);
        $html .= "              <textarea id=\"FQI_Title\" class=\"questionLabel\">" . $this->_label . "</textarea>" . chr(13);
        $html .= "          </form>" . chr(13);
        $html .= "          <form id='". $this->_qValidId ."' action='' class='qParam'>" . chr(13);
        if ($this->_htmlOpt != '')
        {
            $html .= "              <p class='zoneLbl'>Options</p>" . chr(13);
            $html .="                   " . $this->_htmlOpt . chr(13) ;
        }
        $html .= "              <p class='zoneLbl'>Validations</p>" . chr(13);
        $html .=            $this->_htmlVal . chr(13);
        $html .= "          </form>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "  </div>" . chr(13);

        return $html;
    }
}