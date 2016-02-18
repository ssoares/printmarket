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
        //Get data associated to the element according to the type
        //if Question
        
        foreach ($elements as $element)
        {
            if($element['FE_TypeID'] == '2')
                $html .= $oQuestion->show(
                    $element[$this->_dataId],
                    null,
                    $langId,
                    $element['FE_Seq']
                    );
            // or if text zone
            elseif($element['FE_TypeID'] == '1')
                $html .= $oText->show(
                    $element['FE_ID'],
                    $langId,
                    $element['FE_Seq']
                    );
            // Include data into the element
        }

        return $html;
    }

    /**
     * Delete elements and linked data.
     *      If the process is called from the section each element will
     *      be processed and linked data will be deleted too.
     *
     * @param int     $id  Id of the section or the element itself.
     * @param boolean $all Set the process to use.
     *
     * @return boolean
     */
    public function deleteAll($id, $all = false)
    {
        $deleted   = true;
        $oQuestion = new FormQuestionObject();
        $oText     = new FormTextObject();
        // If the method is called with wrong parameters
        // If the method is called with wrong parameters
        if ((!$all && empty($id)))
            throw new Exception('Erreur de paramètres');
        // If deletion is called by section deletion

        if ($all)
        {
            //Select all the elements of the section
            $elements = $this->_selectElements($id, $all);

            if (count($elements) > 0)
            {
                //Call elements in section to delete them too
                foreach ($elements as $element)
                {
                    if($element['FE_TypeID'] == '2')
                        $delElem = $oQuestion->deleteAll(
                            $element[$this->_dataId],
                            $all
                        );
                    // or if text zone
                    elseif($element['FE_TypeID'] == '1')
                        $delElem  = $oText->deleteAll (
                            $element[$this->_dataId],
                            $all
                        );

                    if ($delElem)
                    {
                        $this->delete($element[$this->_dataId]);
                    }else{
                        $deleted = false;
                        break;
                    }
                }
            }
        // If deletion is done for only a defined section
        }
        else
        {

            // call the elements to be deleted
            $element = $this->_selectElements($id);

            if(count($element) && $element[0]['FE_TypeID'] == '2')
            {
                $deleted = $oQuestion->deleteAll(
                    $id,
                    true
                  );
            }
            // or if text zone
            elseif(count($element) && $element[0]['FE_TypeID'] == '1')
                $deleted = $oText->deleteAll($element[0][$this->_dataId], true);

            if ($deleted)
                $this->delete($id);
        }

        return $deleted;
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

    /**
     * Insert into database data of the new element
     *
     * @param array $data Data of the new element
     * @param int   $lang Langauge id
     *
     * @return integer
     */
    public function insert($data, $lang)
    {
        if (empty($data))
            Throw new Exception('Parameter data is empty.');

        // Creates the data entry in the ObjectData Table
        $data_object = new $this->_dataClass();

        $_row = $data_object->createRow();

        foreach ($this->_dataColumns as $_key => $_val)
        {
            if(!empty($data[$_key]))
                $_row->$_val = $data[$_key];
        }

        $_row->save();

        $_dataId = $this->_dataId;
        $_insertedId = $_row->$_dataId;

        return $_insertedId;
    }

    /**
     * Update element object.
     *
     * @param int   $id   Id of the current element.
     * @param array $data Array with data for update.
     *
     * @return boolean
     */
    public function update($id, $data)
    {
        if (empty($data))
            Throw new Exception('Parameter data is empty.');

        $db          = $this->_db;
        $saved       = false;
        $data_object = array();

        foreach ($this->_dataColumns as $_key => $_val)
        {
            if (isset($data[$_key]))
                $data_object[$_val] = $data[$_key];
        }

            if(!empty($data_object)){
                $_objectData = new FormElement();
                $_objectDataTableName = $_objectData->info('name');

                $_objectData->update(
                    $data_object,
                    $this->_dataId . ' = "' . $id . '"'
                );
                $saved = true;

        }

        return $saved;
    }

    /**
     * Delete the element.
     * Override the parent method because there's no index table.
     *
     * @param int $id Id of the elementt to delete.
     *
     * @return void
     */
    public function delete($id)
    {
        $db        = Zend_Registry::get('db');
        $oClass    = new $this->_dataClass();
        $tableName = $oClass->info('name');

        $test =$db->delete(
                $tableName,
                "{$this->_dataId} = " . $id
            );
    }
}