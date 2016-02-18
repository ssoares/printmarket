<?php
class FormTextData extends Zend_Db_Table
{
    protected $_name = 'Form_Text';

    /**
     * Returns the table name for the text element
     * @return String
     */
    public function getName()
    {
        return $this->_name;
    }
}