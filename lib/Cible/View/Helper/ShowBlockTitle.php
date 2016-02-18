<?php

class Cible_View_Helper_ShowBlockTitle extends Zend_View_Helper_Abstract
{

    const STR = '##STRING##';
    protected $_class = 'blockTitle';

    public function setClass($value)
    {
        if (is_array($value))
            $value = explode (' ', $value);
        if (!empty($value))
            $this->_class .= ' ' . $value;
        else
            $this->_class = '';
    }
    /**
     * Creates the html code to insert the block title into the view
     *
     * @param string $tag
     * @return type string
     */
    public function showBlockTitle($tag = 'span', array $options = array())
    {
        foreach ($options as $key => $value)
        {
//            $property = '_' . $key;
//            $this->$property = $value;
            $setAttrib = 'set' . ucfirst($key);
            $this->$setAttrib($value);
        }
        if (!$this->view->showTitle) $tag = 'h1';
        $title = "";
        $html = '<' . $tag . ' class="' . $this->_class . '">' . self::STR . '</' . $tag . '>';
        if ($this->view->showBlockTitle)
        {
            $id = $this->view->blockId;
            $block = Cible_FunctionsBlocks::getBlockDetails($id);
            $title = str_replace(self::STR, $block->BI_BlockTitle, $html);
        }

        return $title;
    }

}
