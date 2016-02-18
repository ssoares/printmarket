<?php

class Cible_View_Helper_StyledButton extends Zend_View_Helper_Abstract
{
    const CONTAINER = '<div class="buttonStyleBorderLeft">
                        <div class="buttonStyleBorderRight">
                        ##SPECIAL## ##CONTENT##
                        </div>
                    </div>';

    protected $_label = 1;
    protected $_href;
    protected $_special = '<span class="##SPECIALCLASS##">&gt;&gt;&gt;</span>';
    protected $_specialClass = 'fifthColor';
    protected $_class;

    public function styledButton($options = array())
    {
        $emptyPlaceholders = array();
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }
        $special = '';
        if (!empty($this->_special))
            $special = $this->_special;
        if (!empty($this->_specialClass))
            $special = str_replace ('##SPECIALCLASS##', $this->_specialClass, $this->_special);

        $content = '';
        if (!empty($this->_label))
            $label = $this->_label;
        if (!empty($this->_href))
            $content = $this->view->link($this->_href, $this->_label, array('title' => $this->_label, 'class' => $this->_class));
        else
            $content = $label;

        $styledBtn = str_replace(array('##SPECIAL##', '##CONTENT##'), array($special, $content), self::CONTAINER);

        return $styledBtn;
    }
}