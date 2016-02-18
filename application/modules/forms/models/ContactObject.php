<?php
/**
 * Manage data from contact table.
 * @category  Application_Module
 * @package   Application_Module_Forms
 */
class ContactObject extends DataObject
{
    protected $_dataClass = 'ContactData';
    protected $_searchColumns = array(
        'data' => array(),
        'index' => array()
    );
    protected $_id;

    /**
     * Setter for id value
     *
     * @param int $value
     */
    public function setId($value)
    {
        $this->_id = $value;
    }
}