<?php

class Cible_View_Helper_ZonesTemplate extends Zend_View_Helper_Abstract
{

    const CONTAINER = '<div class="zone_panel zone_X_panel">
                <p class="zoneLabel ##HIDECLASS##">Zone ##ZONEID##</p>
                <ul id="zone_##ZONEID##" class="zone ##CONNECTEDZONE##">
                ##ROWS##
                </ul>
            </div>';

    const ROW = '<li id="module-##MODULE##" class="" module="##MODULE##" pageid="##PAGEID##" blockid="##BLOCKID##" zoneid="##ZONEID##">##ACTION##</li>';
    protected $_zoneId = 1;
    protected $_rows;
    protected $_controller;
    protected $_isActive = true;

    public function zonesTemplate($options = array())
    {
        $rowsHtml = '';
        $this->_isActive = true;
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }
        $activeClass = 'connectedSortable';
        $hideClass = '';
        if (!$this->_isActive)
        {
            $activeClass = '';
            $hideClass = 'hidden';
        }

        $totalRow = count($this->_rows);
        $search = array('##MODULE##','##PAGEID##', '##BLOCKID##', '##ZONEID##', '##ACTION##');
        foreach ($this->_rows as $key => $block)
        {
            $module = $block['M_MVCModuleTitle'];
            $param = array(
                'ID'         => $block['B_ID'],
                'pageID'     => $block['B_PageID'],
                'position'   => $key + 1,
                'totalBlock' => $totalRow,
                'blockTitle' => $block['BI_BlockTitle'],
                'status'     => $block['B_Online'],
                'secured'    => $block['B_Secured'],
                'isActive'   => $this->_isActive
                );
            $action = $this->view->action($this->_controller . '-block','index',$module, $param);
            $replace = array($module, $param['pageID'], $param['ID'], $this->_zoneId, $action);
            $rowsHtml .= str_replace($search, $replace, self::ROW);
        }
        $arraySrc = array('##ZONEID##', '##ROWS##', '##CONNECTEDZONE##', '##HIDECLASS##');
        $arrayReplace = array($this->_zoneId, $rowsHtml, $activeClass, $hideClass);

        return str_replace($arraySrc, $arrayReplace,  self::CONTAINER);
    }
}