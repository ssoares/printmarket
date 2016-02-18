<?php
class NewsletterArticles extends Zend_Db_Table
{
    protected $_name = 'Newsletter_Articles';    
    
    public function getArticleIdByName($string,$date){
        $select = $this->_db->select();            
        $select->from('Newsletter_Articles','NA_ID') 
                ->join('Newsletter_Releases','NR_ID=NA_ReleaseID')
                ->where("NA_ValUrl = ?", $string)
                ->where("NR_Date = ?", $date);
       
        $id = $this->_db->fetchRow($select); 
        return $id['NA_ID'];
    }
    
    public function getNewsletterIdByName($string,$date){
        $select = $this->_db->select();            
        $select->from('Newsletter_Releases','NR_ID')  
                ->join('Newsletter_Articles','NR_ID=NA_ReleaseID')
                ->where("NA_ValUrl = ?", $string)
                ->where("NR_Date = ?", $date);       
        $id = $this->_db->fetchRow($select);      
        return $id['NR_ID'];
    }
}