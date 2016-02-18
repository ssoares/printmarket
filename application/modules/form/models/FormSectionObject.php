<?php
/**
 * Class FromSectionObject - Manage sections data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormSectionObject - Manage sections data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormSectionObject extends DataObject
{
    protected $_dataClass   = 'FormSection';
    protected $_dataId      = 'FS_ID';
    protected $_dataColumns = array(
            'FS_FormID'    => 'FS_FormID',
            'FS_Seq'       => 'FS_Seq',
            'FS_Repeat'    => 'FS_Repeat',
            'FS_RepeatMin' => 'FS_RepeatMin',
            'FS_RepeatMax' => 'FS_RepeatMax',
            'FS_ShowTitle' => 'FS_ShowTitle',
            'FS_PageBreak' => 'FS_PageBreak'
        );

    protected $_indexClass      = 'FormSectionIndex';
    protected $_indexId         = 'FSI_SectionID';
    protected $_indexLanguageId = 'FSI_LanguageID';
    protected $_indexColumns    = array(
            'FSI_Title' => 'FSI_Title'
    );

    public function  __construct()
    {
        parent::__construct();
        $query = $this->_db->select()
                        ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
                        ->where('TABLE_NAME = ?', 'Form_Section')
                        ->where('TABLE_SCHEMA = ?', $this->_schema);

        $increment        = $this->_db->fetchAll($query);
        $this->_currentId = $increment['0']['AUTO_INCREMENT'];
    }

    /**
     * Build the section zone for displaying.
     * Add any element found in database (text, questions or page break).
     *
     * @access private
     * @param  int     $formId The current form to be managed.
     * @param  int     $langId The language id.
     *
     * @return string $html The html code.
     */
    public function show ($formId = '', $langId = null)
    {
        if(!empty($formId))
        {
            $sections  = $this->_selectSections($formId, $langId);
            $oElements = new FormElementObject();

            foreach($sections as $index => $section)
            {
                //Find elements related to this section
                $elements =  $oElements->getElements($section['FS_ID'], $langId, true);

                //push it into the the section array
                $sections[$index]['elements']= $elements;
            }
        }
        
        //return the data
        return $sections;
    }
    
    /**
     * Get data of related sections
     *
     * @access protected
     * @param  int $formId Id of the form which this section relates.
     * @param  int $langId
     *
     * @return array
     */
    protected function _selectSections($formId, $langId = null)
    {
        $oSection      = new FormSection();
        $oSectionIndex = new FormSectionIndex();

        $select = $oSection->select()
                ->setIntegrityCheck(false)
                ->from(array('FS' => $oSection->info('name')))
                ->join(array('FSI' => $oSectionIndex->info('name')),
                        'FS.FS_ID = FSI.FSI_SectionID',
                        'FSI_Title')
                ->where('FS.FS_FormID = ?', $formId);

        if($langId != null)
        {
            $select->where('FSI.' . $this->_indexLanguageId . ' = ?', $langId);
        }

        $select->order('FS.FS_Seq ASC');

        $sections = $oSection->fetchAll($select)->toArray();

        return $sections;
    }
}