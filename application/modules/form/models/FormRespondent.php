<?php
class FormRespondent extends Zend_Db_Table
{
    protected $_name = 'Form_Respondent';

    /**
     * Delete the form and all th related data
     *
     * @param int $id Id of the form to delete
     *
     * @return void
     */
    public function deleteAll($id, $all = false)
    {
        throw new Exception('Not implemented yet');
    }

    /**
     * Load data to display in the form.
     *
     * @param int $id Id of the form to populate
     *
     * @return array
     */
    public function loadAll($id)
    {
        $respondents = array();
        $oResponse   = new FormRespondentResponse();

        $select = $this->_db->select()
                ->from($this->_name, '')
                ->where('FR_FormID = ?', $id);

        $respondents = $oResponse->fetchAll($select);

        foreach ($respondents as $key => $respondent)
        {
            $responses = $oResponse->loadAll($respondent['FR_ID']);
            $respondents[$key]['responses'] = $responses;
        }

        return $respondents;
    }
}