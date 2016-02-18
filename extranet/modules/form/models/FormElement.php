<?php
class FormElement extends Zend_Db_Table
{
    protected $_name = 'Form_Element';

    /**
     * Get the name of the table.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}