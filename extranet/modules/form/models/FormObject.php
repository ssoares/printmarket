<?php
/**
 * Class FormObject -
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormObject -
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormObject extends DataObject
{
    protected $_dataClass   = 'Form';
    protected $_dataId      = 'F_ID';
    protected $_dataColumns = array(
            'F_Notification' => 'F_Notification',
            'F_Profil'       => 'F_Profil',
            'F_Captcha'      => 'F_Captcha'
        );

    protected $_indexClass      = 'FormIndex';
    protected $_indexId         = 'FI_FormID';
    protected $_indexLanguageId = 'FI_LanguageID';
    protected $_indexColumns    = array(
            'FI_Title' => 'FI_Title'
    );

    /**
     * Delete the form and all th related data
     *
     * @param int $id Id of the form to delete
     *
     * @return void
     */
    public function deleteAll($id)
    {
        $oSection = new FormSectionObject();
        $deleted  = $oSection->deleteAll($id, true);
        //TODO: Delete data from table notification / Respondent

        if ($deleted)
            $this->delete($id);
    }

    public function loadAll($id, $langId)
    {
        $formData  = $this->populate($id, $langId);

        $oRespondent  = new FormRespondent();
        $oSections    = new FormSectionObject();
        $sectionsData = $oSections->getSections($id, $langId);
        /*
        echo "<pre>";
        print_r($sectionsData);
        echo "</pre>";
        exit;
        */
        $respondentData = $oRespondent->loadAll($id);

        $formData['sections']   = $sectionsData;
        $formData['respondent'] = $respondentData;

        return $formData;
    }

    public function show($id, $langId)
    {
        $sectionSelect = new FormSection();
        $select = $sectionSelect->select()
                                ->where('FS_FormID = ?', $id)
                                ->order('FS_Seq');


        $sections = $sectionSelect->fetchAll($select);
        $cptSection = count($sections);




        return($cptSection);

    }

    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
}