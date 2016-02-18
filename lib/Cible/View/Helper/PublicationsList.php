<?php

class Cible_View_Helper_PublicationsList extends Zend_View_Helper_Abstract
{
    const IndexP = 'releases';
    const IndexR = 'researchers';
    protected $_viewScript = 'index/publications.phtml';
    protected $_addCheckbox = false;
    protected $_showCoworkers = true;
    protected $_addPaginator = false;

    public function publicationsList(array $data, $options = array())
    {
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }
        $this->view->addCheckbox = $this->_addCheckbox;
        if ($this->_showCoworkers)
        {
            //$this->view->researchers = $data[self::IndexP][self::IndexR];
            $this->view->showCoworkers = $this->_showCoworkers;
        }

        if($this->_addPaginator)
            $this->view->publications = $data;
        else
            $this->view->publications = $data[self::IndexP];

        $html = $this->view->render($this->_viewScript);

        return $html;
    }

}