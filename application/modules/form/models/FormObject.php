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

    protected $_formdata = array();
    protected $_id = 0;
    protected $_langId = 0;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (isset($options['id']) && isset($options['langId']))
        {
            $this->_id     = $options['id'];
            $this->_langId = $options['langId'];
        }

        $this->loadAll($this->_id, $this->_langId);

    }

    public function loadAll($id, $langId)
    {
        $formData  = $this->populate($this->_id, $this->_langId);

        $oRespondent  = new FormRespondent();
        $oSections    = new FormSectionObject();
        $sectionsData = $oSections->show($this->_id, $this->_langId);

//        $respondentData = $oRespondent->loadAll($this->_id);

        $this->_formdata['form']     = $formData;
        $this->_formdata['sections'] = $sectionsData;
//        $this->_formdata['respondent'] = $respondentData;

    }

    public function getFormData()
    {
        return $this->_formdata;
    }
}