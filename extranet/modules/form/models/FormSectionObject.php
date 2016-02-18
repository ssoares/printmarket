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
    protected $_foreignKey      = 'FSI_SectionID';
    protected $_indexLanguageId = 'FSI_LanguageID';
    protected $_indexColumns    = array(
            'FSI_Title' => 'FSI_Title'
    );

    public function  __construct()
    {
        parent::__construct();
        $query = $this->_db->select()
                        ->from('INFORMATION_SCHEMA.TABLES', 'AUTO_INCREMENT')
                        ->where('TABLE_NAME = ?', $this->_oDataTableName)
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
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $icone = $baseUrl . '/form/index/get-icon/format/48x48/prefix/icon-section/ext/png';
        $html = '';

        if(!empty($formId))
        {
            $sections  = $this->_selectData($formId, $langId);
            $defaultLangId = Cible_Controller_Action::getDefaultEditLanguage();
            if (empty ($sections) && $langId != $defaultLangId)
                $this->show($formId, $defaultLangId);

            $oElements = new FormElementObject();

            foreach($sections as $section)
            {
                $sectionTitle  = $section['FSI_Title'];
                $formSectionID = "section_". $section['FS_ID'];
                $sectionSeq    = $section['FS_Seq'];
                $chkTitle      = ($section['FS_ShowTitle'] == 1) ? "checked=\"checked\"": "";
                $chkRepeat     = ($section['FS_Repeat']) ? "checked=\"checked\"": "";
                $RepeatMin     = $section['FS_RepeatMin'];
                $RepeatMax     = $section['FS_RepeatMax'];
                $pageBreakVal  = $section['FS_PageBreak'];
                $pageBreak     = '&nbsp;';
                if ($pageBreakVal)
                    $pageBreak = $this->showBreakPage();

                $elements =  $oElements->getElements($section['FS_ID'], $langId, true);
                if (empty($elements))
                    $elements = "&nbsp;";
                $titleStyle = "style=\"display: none;\"";

                $html .= "<li elementtype=\"section\" class=\"section_li\"
                    style=\"display: list-item;\"
                    id=\"". $formSectionID ."\">";
                $html .= $this->_render(
                    $icone,
                    $sectionTitle,
                    $formSectionID,
                    $sectionSeq,
                    $chkTitle,
                    $chkRepeat,
                    $RepeatMin,
                    $RepeatMax,
                    $pageBreakVal,
                    $elements,
                    $pageBreak,
                    $titleStyle);
                $html .= "</li>";

            }
        }
        else
        {
            $db            = Zend_Registry::get('db');
            $table         = new FormSection();
            $formSectionID = 'section_' . $this->_currentId;

            $lastSection = $table->fetchRow(null, 'FS_ID DESC');

            if ($lastSection)
               $formSectionID = "section_" . ($lastSection->FS_ID + 1);

            $html = $this->_render($icone, null, $formSectionID);
        }

        return $html;
    }

    /**
     * Delete sections and related data.
     *      If the process is called from the form each section will
     *      be processed and linked data will be deleted too.
     *
     * @access private
     * @param  int     $id  Id of the form or of the section itself.
     * @param  boolean $all Set the process to use.
     *
     * @return boolean
     */
    public function deleteAll ($id, $all = false)
    {
        $deleted = true;
        $oElem   = new FormElementObject();

        // If the method is called with wrong parameters
        if ((!$all && empty($id)))
            throw new Exception('Erreur de paramètres');

        // If deletion is called by form deletion
        if ($all)
        {
            //Select all the section
            $sections = $this->_selectData($id);

            if (count($sections) > 0 )
            {
                //Call elements in section to delete them too
                foreach ($sections as $section)
                {
                    $delElem = $oElem->deleteAll($section['FS_ID'], $all);

                    if ($delElem)
                    {
                        $deleted = true;
                        $this->delete($section['FS_ID']);
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
            $deleted = $oElem->deleteAll($id, true);
            if ($deleted)
                $this->delete ($id);
        }

        return $deleted;
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
    protected function _selectData($formId, $langId = null)
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

    /**
     * Build the html code for the page break zone.
     * The returned html code is added to the section zone.
     *
     * @access public
     * @see    show()
     *
     * @return string $html
     */
    public function showBreakPage()
    {
        $html = chr(13);

        $html .= "<div class='breakpage' elementtype='pagebreak'>" . chr(13);
        $html .= "  <div class='floatRight'>" . chr(13);
        $html .= "      <a class='breakpage_delete_link' elementType='breakpage'>Supprimer</a>" . chr(13);
        $html .= "  </div>" . chr(13);
        $html .= "</div>" . chr(13);

        return $html;
    }

    /**
     * Build the html code for section rendering
     *
     * @access private
     *
     * @param string $icone         Path to the section icon (gif).
     * @param string $sectionTitle  Title of the section.
     * @param string $formSectionID String for the id tad of the section.
     * @param int    $sectionSeq    Position of the section in the form.
     * @param string $chkTitle      Title checkbox is checked or not.
     * @param string $chkRepeat     Repeat checkbox is checked or not.
     * @param int    $RepeatMin     Initiate the minimum of repetition.
     * @param int    $RepeatMax     Initiate the maximum of repetition.
     * @param int    $pageBreakVal  Initiate the page break render (default = 0)
     * @param string $elements      Html code for the elements (texzone or question)
     * @param string $pageBreak     Page break html code for rendering.
     * @param string $titleStyle    html style tag to hide or display the title.
     *
     * @return string $html The html code
     */
    private function _render(
        $icone,
        $sectionTitle  = 'new section',
        $formSectionID = '',
        $sectionSeq    = '',
        $chkTitle      = 'checked="checked"',
        $chkRepeat     = '',
        $RepeatMin     = '1',
        $RepeatMax     = '5',
        $pageBreakVal  = '0',
        $elements      = '&nbsp;',
        $pageBreak     = '&nbsp;',
        $titleStyle    = ''
        )
    {
        $titleLbl     = Cible_Translation::getCibleText('form_section_title_label');
        $showTitleLbl = Cible_Translation::getCibleText('form_section_showtitle_label');
        $repeatLbl    = Cible_Translation::getCibleText('form_section_repeat_label');
        $repeatMinLbl = Cible_Translation::getCibleText('form_section_repeatMin_label');
        $repeatMaxLbl = Cible_Translation::getCibleText('form_section_repeatMax_label');

        $html  = chr(13);
        $html .= "  <div class='section'>" . chr(13);
        $html .= "      <div>" . chr(13);
        $html .= "          <img class='sortableSection' alt='' src='$icone' />" . chr(13);
        $html .= "          <div class='floatRight'>" . chr(13);
        $html .= "              <a class='section_options_link' elementType='section'>Options</a>";
        $html .= "              &nbsp;|&nbsp;" . chr(13);
        $html .= "              <a class='section_delete_link' elementType='section'>Supprimer</a>" . chr(13);
        $html .= "          </div>" . chr(13);
        $html .= "          <p class=\"section_title\">" . $sectionTitle . "</p>" . chr(13);
        $html .= "          <form id=\"" . $formSectionID . "\" action=\"\" class=\"params\"" . $titleStyle . " >" . chr(13);
        $html .= "              <input type=\"hidden\" class=\"\" id=\"FS_Seq\" value=\"". $sectionSeq ."\" />" . chr(13);
        $html .= "              <label for=\"FSI_Title\">"  . $titleLbl;
        $html .= "              </label>" . chr(13);
        $html .= "              <input type=\"text\" class=\"section_title_edit\" id=\"FSI_Title\" value=\"". $sectionTitle ."\"/>" . chr(13);
        $html .= "              <br><input type=\"checkbox\" class=\"\" id=\"FS_ShowTitle\" ". $chkTitle ."/>" . chr(13);
        $html .= "              <label for=\"FS_ShowTitle\" class='after_checkbox'>" . $showTitleLbl ;
        $html .= "              </label>" . chr(13);
        $html .= "              <input type=\"hidden\" class=\"\" id=\"FS_Repeat\" ". $chkRepeat ."/>" . chr(13);
//      $html .= "              <label for=\"FS_Repeat\">" . $repeatLbl;
//      $html .= "              </label>" . chr(13);
        $html .= "              <input type=\"hidden\" class=\"section_RepeatMin\" id=\"FS_RepeatMin\" value=\"". $RepeatMin ."\"/>" . chr(13);
//      $html .= "              <label for=\"FS_RepeatMin\">" . $repeatMinLbl;
//      $html .= "              </label>" . chr(13);
        $html .= "              <input type=\"hidden\" class=\"section_RepeatMax\" id=\"FS_RepeatMax\" value=\"". $RepeatMax ."\"/>" . chr(13);
//      $html .= "              <label for=\"FS_RepeatMax\">" . $repeatMaxLbl;
//      $html .= "              </label>" . chr(13);
        $html .= "              <input type=\"hidden\" class=\"\" id=\"FS_PageBreak\" value=\"". $pageBreakVal ."\" />" . chr(13);
        $html .= "          </form>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "      <div class='drop_zone'>" . chr(13);
        $html .= "          <ul class='section_drop_zone connectedSortable_section ui-sortable'>" . $elements . "</ul>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "      <div class='drop_zone'>" . chr(13);
        $html .= "          <ul class='breakpage_drop_zone connectedSortable_section ui-sortable'>" . $pageBreak . "</ul>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "  </div>" . chr(13);


        return $html;
    }
}