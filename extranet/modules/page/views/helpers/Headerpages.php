<?php

class Zend_View_Helper_Headerpages
{
    public function Headerpages($imageFile)
    {       
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select('PI_PageTitle')
                ->from('PagesIndex')                        
                ->where('PI_TitleImageSrc = ?', $imageFile);
        $Rows = $Pages->fetchAll($Select);       
        
        
        return $Rows;
    }    
   
}
