<?php
    
    class Cart_IndexController extends Cible_Controller_Block_Abstract
    {
        protected $_moduleID      = 15;
        protected $_defaultAction = 'list';
        protected $_moduleTitle   = 'cart';


        public function addAction(){
            throw new Exception('Not implemented');
        }
        
        public function editAction(){
            throw new Exception('Not implemented');
        }
        
        public function deleteAction(){
            throw new Exception('Not implemented');
        }
    }
?>