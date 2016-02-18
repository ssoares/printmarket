<?php
/**
 * Class FromTextObject - Manage texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormTextObject - Manage Texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormTextObject extends DataObject
{
    protected $_dataClass   = 'FormTextData';
    protected $_dataId      = 'FT_ID';
    protected $_dataColumns = array(
            'FT_ElementID' => 'FT_ElementID'
        );

    protected $_indexClass      = 'FormTextIndex';
    protected $_indexId         = 'FTI_FormTextID';
    protected $_indexLanguageId = 'FTI_LanguageID';
    protected $_indexColumns    = array(
            'FTI_Text' => 'FTI_Text'
    );

    /**
     * Returns all the texts for the current element
     *
     * @param int $elemId Id of the current element
     * @param int $langId Id of the language
     *
     * @return array $txtList Array with the texts data
     */
    public function getTexts($elemId, $langId)
    {
        $txtList = array();
        $txtList = $this->_selectTexts($elemId, $langId);
        if (empty ($txtList))
        {
            $langId = Cible_Controller_Action::getDefaultEditLanguage();
            $this->getTexts($elemId, $langId);
        }

        return $txtList;
    }

    /**
     * Delete a text zone
     *
     * @param int $id The element id containing the text or the id of the text
     *                itself.
     * @param boolean $all Set if all the linked data will be deleted
     *
     * @return bool $deleted If the element is deleted return true
     */
    public function deleteAll($id = null, $all = false)
    {
        $deleted = true;
       // If the method is called with wrong parameters
        if ((!$all && $id == null))
        {
            return $deleted;
        // If deletion is called by element deletion
        }
        elseif ($all)
        {
           // Select all the texts
            $texts = $this->_selectTexts($id);

            if (count($texts) > 0)
            {
                // and delete them
                foreach ($texts as $text)
                {
                    $this->delete($text['FT_ID']);
                }
            }
        }
        else
        {
            // Delete the element
            $this->delete($id);
        }

        return $deleted;
    }

    /**
     * Find data from the text tables.
     *
     * @param int $elemId The text id to find and get its data
     * @param int $langId The language id
     *
     * @return array $textList
     */
    protected function _selectTexts($elemId, $langId = null)
    {
        $oText    = new FormTextData();
        $oTxtType = new FormTextIndex();

        $select = $oText->select()
                ->setIntegrityCheck(false)
                ->from(array('FT' => $oText->getName()))
                ->joinInner(
                        array('FTI' => $oTxtType->info('name')),
                        'FTI_FormTextId = FT_ID',
                        'FTI_Text')
                ->where('FT_ElementID = ?', $elemId);

        if($langId != null)
        {
            $select->where($this->_indexLanguageId . ' = ?', $langId);
        }

        $txtList = $oText->fetchAll($select)->toArray();

        return $txtList;
    }

    /**
     * Build the text zone for displaying.
     *
     * @access public
     * @param  int $elemId The element id containing the text zone.
     * @param  int $langId
     *
     * @return string $html The html code of the text zone.
     */
    public function show($elemId = '', $langId = 1, $elemSeq = null)
    {

        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $icon    = $baseUrl . "/text/index/get-icon/format/48x48";
        $html    = '';
        $text    = Cible_Translation::getCibleText('form_default_text_value');

        if (!empty($elemId))
        {
            $txtData = $this->_selectTexts($elemId, $langId);
            $text    = $txtData[0]['FTI_Text'];
            $href    = $baseUrl . "/form/text/edit/element/" . $elemId
                        . "/textID/" . $txtData[0]['FT_ID'];

            $formElemId  = 'element_' . $elemId;
            $textZoneId  = 'textzone_' . $txtData[0]['FT_ID'];

            $html .= "<li elementtype=\"textzone\"
                class=\"element element_textzone section_element_textzone_li\"
                style=\"display: list-item;\"
                id=\"". $formElemId ."\">";
            $html .= $this->_render($icon, $text, $textZoneId, $formElemId, $elemSeq, $href);
            $html .= "</li>";
        }
        else
        {
            $db         = Zend_Registry::get('db');
            $tableElem  = new FormElement();
            $tableText  = new FormTextData();
            $formElemId = 'element_1';
            $textZoneId = 'textzone_1';

            $lastElem = $tableElem->fetchRow(null, 'FE_ID DESC');

            if ($lastElem)
               $formElemId = "element_" . ($lastElem->FE_ID + 1);

            $lastText = $tableText->fetchRow(null, 'FT_ID DESC');

            if ($lastText)
               $textZoneId = "textzone_" . ($lastText->FT_ID + 1);

            $html = $this->_render($icon, $text, $textZoneId, $formElemId);
        }


        return $html;
    }

    /**
     * Build the html code.
     *
     * @access private
     * @param  string $icon       Path to the icon image.
     * @param  string $text       Content of the text zone.
     * @param  string $textZoneId Content for the id tag of the text zone.
     * @param  string $formElemId Content for the id tag of the element.
     *
     * @return string $html Html code for the zone text.
     */
    private function _render(
        $icon,
        $text,
        $textZoneId = 1,
        $formElemId = '',
        $elemSeq    = 1,
        $href       = '')
    {
        $html  = chr(13);
        $html .= "  <div id=\"". $textZoneId ."\" class='textzone'>" . chr(13);
        $html .= "      <div class='header'>" . chr(13);
        $html .= "          <img class='sortableElement' alt='' src=\"". $icon ."\" />" . chr(13);
        $html .= "          <form id='". $formElemId ."' action='' class='hidden'>" . chr(13);
        $html .= "              <input id='FE_Seq' class='hidden' value='". $elemSeq ."'/>" . chr(13);
        $html .= "          </form>" . chr(13);
        $html .= "          <div class='floatRight'>" . chr(13);
        $html .= "              <p class='textzone'>Zone de texte (ceci n'est pas une question)</p>" . chr(13);
        $html .= "              <p class='links'>" . chr(13);
        $html .= "                  <a href=\""
                                        . $href . "\" class='textzone_edit_link'
                                            elementType='textzone'>"
                                        . Cible_Translation::getCibleText('form_element_edit_link')
                                   . "</a>" . chr(13);
        $html .= "                  &nbsp;|&nbsp;" . chr(13);
        $html .= "                  <a class='textzone_delete_link' elementType='textzone'>"
                                        . Cible_Translation::getCibleText('form_element_delete_link')
                                   . "</a>" . chr(13);
        $html .= "              </p>" . chr(13);
        $html .= "          </div>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "      <div class='center' style='clear:both;'>" . chr(13);
        $html .= "          <div class='previewText'>" . chr(13);
        $html .=                $text . chr(13);
        $html .= "          </div>" . chr(13);
        $html .= "      </div>" . chr(13);
        $html .= "  </div>" . chr(13);

        return $html;
    }
}