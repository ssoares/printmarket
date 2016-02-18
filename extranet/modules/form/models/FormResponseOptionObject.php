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
    protected $_indexClass      = 'FormResponseOptionIndex';
    protected $_indexLanguageId = 'FROI_LanguageID';
    protected $_foreignKey      = 'FROI_ResponseOptionID';
    
    protected $_dataRespOpt    = array();
    protected $_typeName       = "";
    protected $_sequence       = 0;
    protected $_defaultChecked = '';
    protected $_detailsChecked = '';
    protected $_label          = 'A renseigner';
    protected $_currentId      = 1;
    protected $_icoMove;

    /**
     * Class constructor
     *
     * @return void
     */
    public function  __construct()
    {
        parent::__construct();
        $query = $this->_db->select()
                        ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
                        ->where('TABLE_NAME = ?', 'Form_ResponseOption')
                        ->where('TABLE_SCHEMA = ?', $this->_schema);

        $increment           = $this->_db->fetchAll($query);
        $this->_currentId = $increment['0']['AUTO_INCREMENT'];

        $baseDir        = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_icoMove = $baseDir
                . "/form/index/get-icon/format/16x16/ext/png/prefix/icon-fleche";
    }

    /**
     * Get data and build the list for the options in select and choices.
     *
     * @param int $optionId Id of the response option.
     * @param int $langId Id of the language.
     *
     * @return string $html
     */
    public function getOptionsList($optionId, $langId, $value = null)
    {
        $this->_selectResponseOptions($optionId, $langId);
        $nbDefaultLine = count($this->_dataRespOpt);

        if ($nbDefaultLine == 0)
            $this->_selectResponseOptions($optionId, Cible_Controller_Action::getDefaultEditLanguage());

        if (count($this->_dataRespOpt) == 0 && $langId == Cible_Controller_Action::getDefaultEditLanguage())
        {
            $languages = Cible_FunctionsGeneral::getAllLanguage();
            foreach ($languages as $key => $lang)
            {
                if ($lang['L_ID'] != $langId)
                {
                    $this->getOptionsList ($optionId, $lang['L_ID']);
                    break;
                }
            }
        }

        if (count($this->_dataRespOpt))
            $this->_typeName = $this->_dataRespOpt[0]['FRO_Type'];

        if ($this->_typeName == "select")
            $html = $this->selectValuesList($nbDefaultLine);
        else
            $html = $this->choiceValuesList($nbDefaultLine);

        return $html;

    }

    public function selectValuesList($nbDefaultLine = 2)
    {
        $btnValue   = Cible_Translation::getCibleText('form_response_option_add_button');

        $html  = $this->_addButton($btnValue);
        $html .= "<table class='select_options_drop_zone'>" . chr(13);
        $html .= "    <thead>" . chr(13);
        $html .= "    <tr>" . chr(13);
        $html .= "        <th class='move'>&nbsp;</th>" . chr(13);
        $html .= "        <th class='value'>Titre</th>" . chr(13);
        $html .= "        <th class='default'>Défaut</th>" . chr(13);
        $html .= "        <th class='action'>Actions</th>" . chr(13);
        $html .= "    </tr>" . chr(13);
        $html .= "    <tr>" . chr(13);
        $html .= "        <td>-----" . chr(13);
        $html .= "        </td>" . chr(13);
        $html .= "        <td>" . chr(13);
        $html .= "            --- Choisir ---" . chr(13);
        $html .= "        </td>" . chr(13);
        $html .= "        <td>" . chr(13);
        $html .= "            <input id='FRO_Default' type='radio' class='radio' name='default' value='1' checked='checked' />" . chr(13);
        $html .= "        </td>" . chr(13);
        $html .= "        <td>" . chr(13);
        $html .= "            -----" . chr(13);
        $html .= "        </td>" . chr(13);
        $html .= "    </tr>" . chr(13);
        $html .= "    </thead>" . chr(13);
        $html .= "    <tbody>" . chr(13);

        if (count($this->_dataRespOpt))
        {
            foreach ($this->_dataRespOpt as $respOpt)
            {
                $this->_defaultChecked = '';
                $this->_label          = $respOpt['FROI_Label'];
                $this->_sequence       = $respOpt['FRO_Seq'];

                if ($respOpt['FRO_Default'] == 1)
                    $this->_defaultChecked = 'checked="checked"';

                $html .= $this->addNewSelectLine($respOpt['FRO_ID']);
            }
        }
        else
        {
            $optionId = $this->_currentId;

            for ($i = 1; $i <= $nbDefaultLine; $i++)
            {
                $html .= $this->addNewSelectLine($optionId);
                $optionId++;
            }
        }
        $html .= "    </tbody>" . chr(13);
        $html .= "</table>" . chr(13);

        return $html;

    }

    public function choiceValuesList($nbDefaultLine = 2, $type = '')
    {
        $btnValue   = Cible_Translation::getCibleText('form_response_option_add_button');

        $html  = $this->_addButton($btnValue);
        $html .= "<table class='select_options_drop_zone'>" . chr(13);
        $html .= "    <thead>" . chr(13);
        $html .= "    <tr>" . chr(13);
        $html .= "        <th class='move'></th>" . chr(13);
        $html .= "        <th class='value'>Titre</th>" . chr(13);
        $html .= "        <th class='default'>Défaut</th>" . chr(13);
        $html .= "        <th class='details'>Précisez</th>" . chr(13);
        $html .= "        <th class='action'>Actions</th>" . chr(13);
        $html .= "    </tr>" . chr(13);
        $html .= "    </thead>" . chr(13);
        $html .= "    <tbody>" . chr(13);

        if (count($this->_dataRespOpt))
        {
            foreach ($this->_dataRespOpt as $respOpt)
            {
                $this->_detailsChecked = '';
                $this->_defaultChecked = '';
                $this->_label          = $respOpt['FROI_Label'];
                $this->_sequence       = $respOpt['FRO_Seq'];

                if ($respOpt['FRO_Other'])
                    $this->_detailsChecked = 'checked="checked"';
                if ($respOpt['FRO_Default'])
                    $this->_defaultChecked = 'checked="checked"';

                $html .= $this->addNewChoiceLine($respOpt['FRO_ID']);
            }
        }
         else
        {
            $this->_typeName = $type;
            $optionId = $this->_currentId;

            for ($i = 1; $i <= $nbDefaultLine; $i++)
            {
                $html .= $this->addNewChoiceLine($optionId);
                $optionId++;
            }
        }
        $html .= "    </tbody>" . chr(13);
        $html .= "</table>" . chr(13);

        return $html;
    }

    public function addNewSelectLine($optionId = 0)
    {
        if ($optionId == 0)
            $optionId = $this->_currentId;

        $file = new Cible_View_Helper_BaseUrl();
        $deleteTitle = Cible_Translation::getCibleText('button_delete');

        $html  = "<tr id='responseOption_" . $optionId . "' class='ui-state-default option option_response ui-draggable'>" . chr(13);
        $html .= "    <td id='FRO_Seq'>" . chr(13);
        $html .= "        " . chr(13);
        $html .= "        <input id='FRO_Seq' type='hidden' value='" . $this->_sequence . "' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input id='FROI_Label' type='text' class='optionValue' value='" . $this->_label . "' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input id='FRO_Default' type='radio' " . $this->_defaultChecked . " class='radio' name='default' value='0' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input type='button' id='select_" . $optionId
                            . "' class='delete' title='" . $deleteTitle . "' elementtype='option' value='' />"
                            . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "</tr>" . chr(13);

        return $html;
    }

    public function addNewChoiceLine($optionId = 0, $type = '')
    {
        $inputType = 'radio';

        if ($optionId == 0)
            $optionId = $this->_currentId;

        if ($this->_typeName == 'multi')
            $inputType = 'checkbox';

        $file = new Cible_View_Helper_BaseUrl();
        $deleteTitle = Cible_Translation::getCibleText('button_delete');

        $html  = "<tr id='responseOption_" . $optionId . "' class='ui-state-default option option_response ui-draggable'>" . chr(13);
        $html .= "    <td id='FRO_Seq'>" . chr(13);
        $html .= "       " . chr(13);
        $html .= "        <input id='FRO_Seq' type='hidden' value='" . $this->_sequence . "' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input id='FROI_Label' type='text' class='optionValue' value=\"" . $this->_label . "\"/>" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input id='FRO_Default' type='" . $inputType
                            . "' class='radio' "
                            . $this->_defaultChecked
                            . " name='default' value='' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input id='FRO_Other' type='checkbox' class='radio' "
                            . $this->_detailsChecked
                            . " name='details' value='' />" . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "    <td>" . chr(13);
        $html .= "        <input type='button' id='"
                            . $this->_typeName ."_" . $optionId
                            . "' class='delete' title='" . $deleteTitle
                            . "' elementtype='option' value=' ' />"
                            . chr(13);
        $html .= "    </td>" . chr(13);
        $html .= "</tr>" . chr(13);

        return $html;
    }

    public function deleteAll($id)
    {
        if (empty($id))
            Throw new Exception('Parameter id is empty.');

        $db = $this->_db;

        $_objectData = new $this->_dataClass();
        $_objectDataTableName = $_objectData->info('name');
        $query = $db->select()
                ->from($_objectDataTableName, 'FRO_ID')
                ->where("FRO_QuestionID = ?", $id);

        $tmpOptions = $db->fetchAll($query);

        $deleted = $db->delete(
                $_objectDataTableName,
                $db->quoteInto(
                        "FRO_QuestionID = ?", $id)
             );

        if (!empty( $this->_indexClass ) && count($tmpOptions) > 0 )
        {
            $oIndex          = new $this->_indexClass();

            foreach ($tmpOptions as $option)
            {
                $deleted = $oIndex->delete(
                        $db->quoteInto(
                                "FROI_ResponseOptionID = ?", $option['FRO_ID'])
                        );
            }
        }

        if ($deleted > 0)
            return $deleted;
        else
            return 0;
    }

    protected function _selectResponseOptions($questionId, $langId = null)
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
                ->where('FRO_QuestionID = ?', $questionId);


        if($langId != null)
        {
            $select->where('FROI_LanguageID = ?', $langId);
        }

        $select->order('FRO_Seq');

        $respOptions = $oRespOpt->fetchAll($select)->toArray();

        $this->_dataRespOpt = $respOptions;
    }

    public function getNewOptionLine($optionId, $type, $sequence)
    {
        $this->_sequence = $sequence;
        $this->_typeName = $type;

        if ($this->_typeName == 'select')
        {
            $html = $this->addNewSelectLine($optionId);
        }
        else
        {
            $html = $this->addNewChoiceLine($optionId);
        }

        return $html;
    }

    /**
     * Create a new input button.
     *
     * @param <type> $btnValue Value to display in the button.
     * @return string $html Code for a new button.
     */
    private function _addButton($btnValue = "")
    {
        $html  = "<input type='button' id='addLine' value='" . $btnValue;
        $html .=  "' class='addLine'/>";

        return $html;
    }

    public function save($id, $data, $langId)
    {
        if (empty($id))
            Throw new Exception('Parameter id is empty.');

        if (empty($data))
            Throw new Exception('Parameter data is empty.');

        if (empty($langId))
            Throw new Exception('Parameter langId is empty.');

        $db = $this->_db;
        $saved = false;
        $data_object = array();

        foreach ($this->_dataColumns as $_key => $_val)
        {
            if (isset($data[$_key]))
                $data_object[$_val] = $data[$_key];
        }

            if(!empty($data_object)){
                $_objectData = new $this->_dataClass();
                $_objectDataTableName = $_objectData->info('name');

                if (key_exists('FRO_Default', $data_object))
                    $this->_resetDefault($id, $data_object);

                $db->update($_objectDataTableName, $data_object, $db->quoteInto("{$this->_dataId} = ?", $id));
                $saved = true;

        }
        $index_object = array();

        foreach ($this->_indexColumns as $_key => $_val)
        {
            if (!empty($data[$_key]))
                $index_object[$_val] = $data[$_key];
        }

            if(!empty($index_object)){
                $_objectIndex = new $this->_indexClass();
                $_objectIndexTableName = $_objectIndex->info('name');

                $found = $db->fetchCol("SELECT true FROM $_objectIndexTableName WHERE {$this->_indexId} = '$id' AND {$this->_indexLanguageId} = '$langId'");
                //$found = $db->fetchCol("SELECT true FROM {$this->_indexClass} WHERE {$this->_indexId} = '$id' AND {$this->_indexLanguageId} = '$langId'");

                if( $found  ){

                $where = array();

                $where[] = $db->quoteInto("{$this->_indexId} = ?", $id);
                $where[] = $db->quoteInto("{$this->_indexLanguageId} = ?", $langId);

                    $n = $db->update($_objectIndexTableName, $index_object, $where);
                    //$n = $db->update($this->_indexClass, $index_object, $where);

                } else {

                $index_object[$this->_indexId] = $id;
                $index_object[$this->_indexLanguageId] = $langId;

                    $db->insert($_objectIndexTableName, $index_object);
                    //$db->insert($this->_indexClass, $index_object);
            }

            $saved = true;
        }

        return $saved;
    }

    private function _resetDefault($id, $data)
    {
        $optData = array();
        // Trouver questionId pour cette options
        // Reset les champs pour cette question et cette colonne
        $oData     = new $this->_dataClass();
        $keyExists = key_exists("FRO_QuestionID", $data);

        if ($keyExists)
        {
            $questionID = $data["FRO_QuestionID"];
        }else{

            $select = $this->_db->select()
                    ->from($oData->info('name'), array('FRO_QuestionID', 'FRO_Type'))
                    ->where("{$this->_dataId} = ?", $id);

            $optData    = $this->_db->fetchRow($select);
            $questionID = $optData["FRO_QuestionID"];
        }

        if (($data["FRO_Default"] == 1 && $optData['FRO_Type'] != 'multi')||
                ($keyExists && isset($optData['FRO_Type'])))
        {
            $this->_db->update(
                $oData->info('name'),
                array("FRO_Default" => 0),
                $this->_db->quoteInto(
                        "FRO_QuestionID = ?",
                        $questionID));
        }
    }
}