<?php

class Zend_View_Helper_Backgroundpages
{
    public function Backgroundpages($imageFile)
    {       
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select('PI_PageTitle')
                ->from('PagesIndex')                        
                ->where('PI_ImageBackground = ?', $imageFile);
        $Rows = $Pages->fetchAll($Select);       
        
        
        return $Rows;
    }    
   
}
