<?php

class Cible_View_Helper_GetValuesFromId extends Zend_View_Helper_Abstract
{

    public function getValuesFromId($id, $field = '')
    {

        switch ($field)
        {
            case '':

                break;
            default:
                $tmp =array();
                $oObj = new ReferencesObject();
                if (is_array($id) && !empty($id)){
                    foreach ($id as $value){
                        if (!empty($value))
                        {
                            $data = $oObj->populate($value, Zend_Registry::get('languageID'));
                            array_push($tmp, $data['RI_Value']);
                        }
                    }
                }
                elseif(!empty($id))
                {
                    $data = $oObj->populate($id, Zend_Registry::get('languageID'));
                    array_push($tmp, $data['RI_Value']);
                }
                $value = implode('<br />', $tmp);
                break;
        }

        if (!isset($value))
            $value = $id;

        return $value;
    }

}