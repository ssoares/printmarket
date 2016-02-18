<?php
class FormElementType extends Zend_Db_Table
{
    protected $_name = 'Form_ElementType';

    /**
     * Get the name of the table
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}