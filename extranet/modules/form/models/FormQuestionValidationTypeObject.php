<?php
/**
 * Class FormQuestionValidationTypeObject - Manage validation type data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormQuestionValidationTypeObject - Manage validation type data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormQuestionValidationTypeObject extends DataObject
{
    protected $_dataClass   = 'FormQuestionValidationType';
    protected $_dataId      = 'FQVT_ID';
    protected $_dataColumns = array(
            'FQVT_TypeName' => 'FQVT_TypeName',
            'FQVT_Category' => 'FQVT_Category',
            'FQVT_Regex'    => 'FQVT_Regex'
        );

    protected $_indexClass      = 'FormQuestionValidationTypeIndex';
    protected $_indexId         = 'FVTI_ValidationTypeID';
    protected $_indexLanguageId = 'FVTI_LanguageID';
    protected $_indexColumns    = array(
            'FVTI_Title'       => 'FVTI_Title',
    );

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
     * Get data and build the type of validation required.
     *
     * @param int $typeId Id of the type of validation.
     * @param int $langId Id of the language.
     *
     * @return string $html
     */
    public function getValidationType($typeId, $langId, $value = null)
    {
        $data = $this->populate($typeId, $langId);
        
        $data['FQVT_TypeID'] = $typeId;

        $typeName = $data['FQVT_TypeName'];
        $funcName = '_render' . $typeName;

        $html = $this->$funcName($data, $value);
      
        return $html;
        
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate if the field is required.
     *
     * Display a checkbox.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderRequired($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of minimum characters.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMinChar($data, $value = null)
    {
        return $html = $this->_renderText($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of maximum characters.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMaxChar($data, $value = null)
    {
        return $html = $this->_renderText($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate if the value is a number.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderNumeric($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the email format.
     *
     * Display a checkbox.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderEmail($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the phone number format.
     *
     * Display a checkbox.
     *
     * <b>Important:</b> Validate only US numbers and canadian numbers.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderPhoneNum($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the zip code format.
     *
     * Display a checkbox.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderZipCode($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of minimum choices to tick.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMinChoices($data, $value = null)
    {
        return $html = $this->_renderText($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of maxnimum choices to tick.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMaxChoices($data, $value = null)
    {
        return $html = $this->_renderText($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of date minimum.
     *
     * Display an jquery ui calendar.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMinDate($data, $value = null)
    {
        $html  = $data['FVTI_Title'];
        //@TODO : Set the html code for the calendar
        throw new Exception('Not yet implememted');
        
        return $html;
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of date maximum.
     *
     * Display an jquery ui calendar.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderMaxDate($data, $value = null)
    {
        $html  = $data['FVTI_Title'];
        //@TODO : Set the html code for the calendar
        throw new Exception('Not yet implememted');

        return $html;
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate if the title is required.
     *
     * Display a checkebox.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderTitleRequired($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate if the description is required.
     *
     * Display a checkebox.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderDescrRequired($data, $value = null)
    {
        return $html = $this->_renderCheckbox($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of minimum choices to tick.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderHeight($data, $value = null)
    {
        return $html = $this->_renderText($data, $value);
    }

    /**
     * Create the html code to be included into the parameters form of the
     * question to validate the number of minimum choices to tick.
     *
     * Display an input text field.
     *
     * @param array $data Data from db for the validation type according to the
     *                    language.
     *
     * @return string $html
     */
    protected function _renderSort($data, $value = null)
    {
        return $html = $this->_renderSelect($data, $value);
    }

    /**
     * Render a type checkbox input to display the validator.
     *
     * @param array $data
     * @param mixed $value
     *
     * @return string $html
     */
    private function _renderCheckbox($data, $value = null)
    {
        $checked = '';
        if ($value)
            $checked = 'checked="checked"';

        $html  = "<fieldset class='lineParam'>" . chr(13);
        $html .= "    <p class='nameParam'>" . $data['FVTI_Title'] . "</p>";
        $html .= "    <p class='valueParam'>";
        $html .= "         <input id='FQV_Value' type='checkbox' class=\"numeric\" "
                            . $checked
                            . "validator='" . $data['FQVT_TypeID']. "'"
                            . "category='" . $data['FQVT_Category'] . "'/>" . chr(13);
        $html .= "    </p>" . chr(13);
        $html .= "</fieldset>" . chr(13);

        return $html;
    }

    /**
     * Render a type text input to display the validator.
     *
     * @param array $data
     * @param mixed $value
     * 
     * @return string $html
     */
    private function _renderText($data, $value = null)
    {
        $html  = "<fieldset class='lineParam'>" . chr(13);
        $html .= "    <p class='nameParam'>" . $data['FVTI_Title'] . "</p>";
        $html .= "    <p class='valueParam'>";
        $html .= "         <input id='FQV_Value' type='text' class=\"numeric text\" "
                            . "value='" . $value . "'"
                            . "validator='" . $data['FQVT_TypeID']. "'"
                            . "category='" . $data['FQVT_Category'] . "'/>" . chr(13);
        $html .= "    </p>" . chr(13);
        $html .= "</fieldset>" . chr(13);

        return $html;
    }

    /**
     * Render a type select input to display the validator.
     *
     * @param array $data
     * @param mixed $value
     *
     * @return string $html
     */
    private function _renderSelect($data, $value = null)
    {
        throw new Exception('Not yet implemeted');
    }

    /**
     * Render a type radio button input to display the validator.
     *
     * @param array $data
     * @param mixed $value
     *
     * @return string $html
     */
    private function _renderRadioButton($data, $value = null)
    {
        throw new Exception('Not yet implemeted');
    }
}