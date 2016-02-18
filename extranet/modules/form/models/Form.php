<?php
/**
 * Class Form - Manage actions for the module administration.
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class Form - Manage db access for the table Form
 *
 * @package    Form
 * @copyright  (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class Form extends Zend_Db_Table
{
    protected $_name = 'Form';

    /**
     * Select data from database.
     *
     * @param int $id   The form id if defined.
     * @param int $lang The language id if defined.
     *
     * @return object $select List of froms from DB.
     */
    public function getFormList($id = null, $lang = null)
    {
        $db     = new Form();
        $select = $db->select()
                    ->from($this->_name)
                    ->setIntegrityCheck(false)
                    ->join('FormIndex', 'Form.F_ID = FormIndex.FI_FormID');
                    
        if ($id)
            $select->where('Form.F_ID = ?', $id);
        if ($lang)
            $select->where('FormIndex.FI_LanguageID = ?', $lang);
        else
        {
            $select->where(
                    'FormIndex.FI_LanguageID = ?',
                    Cible_Controller_Action::getDefaultEditLanguage());
        }

        return $select;
    }

    /**
     * Build an array with all the data needed to display the form foe edition
     *
     * @param int $id   Id of the form to display
     * @param int $lang Id of the current language displayed
     * 
     * @return array $data An array with all the data retrieved from linked tables
     */
    public function getAllData($id, $lang)
    {
        $data = array();
        if (empty($id))
            Throw new Exception('Parameter id is empty.');

        if (empty($langId))
            Throw new Exception('Parameter langId is empty.');

        return $data;
    }
}