<?php
class FormRespondentResponse extends Zend_Db_Table
{
    protected $_name = 'Form_RespondentResponse';

    /**
     * Get response related to the id respondent.
     *
     * @param int $respondentId
     * @return array $responseList
     */
    public function loadAll($respondentId)
    {
        $responseList = array();

        $select  = $this->_db->select()
                ->from($this->_name)
                ->where('FRR_RespondentID = ?', $respondentId);

        $responseList = $this->fetchAll($select);

        return $responseList;
    }
}