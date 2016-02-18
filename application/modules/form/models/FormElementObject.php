<?php
/**
 * Class FromElementObject - Manage elements data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormElementObject - Manage elements data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormElementObject extends DataObject
{
    protected $_dataClass   = 'FormElement';
    protected $_dataId      = 'FE_ID';
    protected $_dataColumns = array(
            'FE_SectionID' => 'FE_SectionID',
            'FE_Seq'       => 'FE_Seq',
            'FE_TypeID'    => 'FE_TypeID'
        );

    protected $_typeClass      = 'FormElementType';
    protected $_typeId         = 'FET_ID';
    protected $_typeLanguageId = '';
    protected $_typeColumns    = array(
        'FET_Title' => 'FET_Title'
    );

    /**
     * Class constructor
     *
     * @access public
     *
     * @return void
     */
    public function  __construct()
    {

    }

    /**
     * Get all elements linked to a specific section
     *
     * @param int  $sectionId Id of the queried section.
     * @param int  $langId    Id of the current language.
     * @param bool $all       If true then loading elements not creating a new one
     *
     * @return string $html  The html format to display current element in section
     */
    public function getElements($sectionId, $langId, $all = false)
    {
        $html     = '';
        $elements = $this->_selectelements($sectionId, true);

        $oQuestion = new FormQuestionObject();
        $oText     = new FormTextObject();
        //Get associated data according to the type
        
        foreach ($elements as $index => $element)
        {
            //if Question
            if($element['FE_TypeID'] == '2')
            {
                $question = $oQuestion->show(
                    $element[$this->_dataId],
                    null,
                    $langId,
                    $element['FE_Seq']
                );
                // Include data into the element
                $elements[$index]['questions'] = $question;
            // or if text zone
            }
            elseif($element['FE_TypeID'] == '1')
            {
                $textZone = $oText->getTexts (
                    $element['FE_ID'],
                    $langId
                    );
                // Include data into the element
                $elements[$index]['textzone']  = $textZone;
            }
        }

        return $elements;
    }

    /**
     * Return an array with the elements according to the parameters
     *
     * @param int $id
     * @param int $langId
     * @param string $type
     *
     * @return array
     */
    protected function _selectElements($id, $all = false)
    {
        $oElem     = new FormElement();
        $oElemType = new FormElementType();

        $select = $oElem->select()
                ->setIntegrityCheck(false)
                ->from($oElem->getName())
                ->joinInner(
                        $oElemType->getName(),
                        'FET_ID = FE_TypeID', 'FET_Title'
                        );
        if (!$all)
        {
            $select->where('FE_ID = ?', $id);
        }else{
            $select->where('FE_SectionID = ?', $id);
        }

        $select->order('FE_Seq ASC');

        $elements = $oElem->fetchAll($select)->toArray();

        return $elements;
    }

}