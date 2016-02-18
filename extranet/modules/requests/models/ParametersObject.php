<?php
/**
 * Module Requests
 * Management of the RequestOptions.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RequestOptions.php 1303 2013-10-25 20:37:48Z ssoares $id
 */
class RequestOptionsObject
{
    public static function fieldTypes($type = 0)
    {
        $fieldTypes = array(
            1 => 'varchar',
            2 => 'int',
            3 => 'bool',
            4 => 'date',
            5 => 'list',

        );
        return $type > 0 ? $fieldTypes[$type] : $fieldTypes;
    }
    public static function textCompare($index = 0)
    {
        $operators = array(
            '1' => array('operator' => 'like "%##VALUES##%"', 'label' => 'op_contain_label'),
            '2' => array('operator' => 'not like "%##VALUES##%"', 'label' => 'op_not_contain_label'),
            '3' => array('operator' => '=', 'label' => 'op_equal_label'),
            '4' => array('operator' => '!=', 'label' => 'op_equal_label'),

        );
        return $index > 0 ? $operators[$index] : $index;
    }
    public static function intDateCompare($index = 0)
    {
        $operators = array(
            '1' => array('operator' => '=', 'label' => 'op_equal_label'),
            '2' => array('operator' => '!=', 'label' => 'op_equal_label'),
            '3' => array('operator' => '>', 'label' => 'op_greater_label'),
            '4' => array('operator' => '>=', 'label' => 'op_greater_equal_label'),
            '5' => array('operator' => '<', 'label' => 'op_lesser_label'),
            '6' => array('operator' => '<=', 'label' => 'op_lesser_equal_label'),

        );
        return $index > 0 ? $operators[$index] : $index;
    }

    public static function boolListCompare($index = 0)
    {
        $operators = array(
            '1' => array('operator' => '=', 'label' => 'op_equal_label'),
        );
        return $index > 0 ? $operators[$index] : $index;
    }

}