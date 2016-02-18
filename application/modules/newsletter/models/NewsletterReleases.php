<?php
class NewsletterReleases extends Zend_Db_Table
{
    protected $_name = 'Newsletter_Releases';    
    
    public function getNewsletterIdByName($string,$date){
        $select = $this->_db->select();
        $select->from('Newsletter_Releases','NR_ID')
                ->where("NR_ValUrl = ?", $string)
                ->where("NR_Date = ?", $date);

        $id = $this->_db->fetchRow($select);
        
        return $id['NR_ID'];
    }
    
    public function getFilterArchive()
    {
        $select = $this->select()
                    ->distinct()
                    ->from('Newsletter_Releases', array('Annee' => 'Year(NR_Date)'))
                    ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                    ->where('NR_Online = 1')
                    ->order('NR_Date desc');
        
        $arraySelect = $this->fetchAll($select);
        
        return $arraySelect;
}
}