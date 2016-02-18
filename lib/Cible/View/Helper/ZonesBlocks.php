<?php

class Cible_View_Helper_ZonesBlocks extends Zend_View_Helper_Abstract
{

    protected $_zoneId = 1;
    protected $_rows;
    protected $_controller;

    public function zonesBlocks($options = array())
    {
        $emptyPlaceholders = array();
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }

        for ($this->_zoneId = 1; $this->_zoneId <= $this->_nbZones; $this->_zoneId++)
        {
            $blocks = array();
            if (isset($this->view->blocks[$this->_zoneId]))
                $blocks = $this->view->blocks[$this->_zoneId];

            $zoneName = 'zone' . $this->_zoneId;
            $this->view->placeholder($zoneName)->captureStart();
            foreach ($blocks as $block){
                $module = $block['module'];
                switch ($block['params']['secured']){
                    case 1:
                        $user = Zend_Registry::get('user');
                        if(count($user))
                            echo $this->view->action($block['action'],'index',$module, $block['params']);
                        break;
                    case 2:
                        $user = Zend_Registry::get('user');
                        if(!count($user))
                            echo $this->view->action($block['action'],'index',$module, $block['params']);
                        break;
                    default:
                        echo $this->view->action($block['action'],'index',$module, $block['params']);
                        break;
                }
            }
            $this->view->placeholder($zoneName)->captureEnd();
            $value = $this->view->placeholder($zoneName)->getValue();
            if (empty($value))
                $emptyPlaceholders[$this->_zoneId] = true;
        }
        return $emptyPlaceholders;
    }
}