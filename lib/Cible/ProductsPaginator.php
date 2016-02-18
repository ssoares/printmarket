<?php
    class Cible_ProductsPaginator
    {
        protected $_db;
        protected $_view;
        protected $_request;
        
        /**
         * Constructor
         *
         * Registers form view helper as decorator
         * 
         * @param mixed $options 
         * @return void
         */
        public function __construct($select, $numberOfColumns, $itemViewscript = null, $options = null){
            $this->_db = Zend_registry::get('db');
            $_frontController = Zend_Controller_Front::getInstance();
            $this->_request = $_frontController->getRequest();
            
            if (null === $this->_view) {
                require_once 'Zend/Controller/Action/HelperBroker.php';
                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
                $this->_view = $viewRenderer->view;
            }
            
            if( !empty($options['paginationViewScript']) )
                $this->_view->assign('paginationViewScript', $options['paginationViewScript']);
            else
                $this->_view->assign('paginationViewScript', null);
            
            $this->_view->assign('numberOfColumns', $numberOfColumns);
            $this->_view->assign('itemViewScript', $itemViewscript);
                
            $adapter = new Zend_Paginator_Adapter_DbSelect(
                $select
            );

            $paginator = new Zend_Paginator($adapter);
            $_config = Zend_Registry::get('config');
            
            $itemPerPage = 12;
            if( !empty($_config->products->itemPerPage) ){
                $itemPerPage = $_config->products->itemPerPage;
            }
            
            if( !empty($options['list_options']['perPage']) )
                $itemPerPage = $options['list_options']['perPage'];
            
            if( $this->_request->getParam('perPage') ){
                $itemPerPage = $this->_request->getParam('perPage') == 'all' ? $paginator->getTotalItemCount() : $this->_request->getParam('perPage');
            }
                
            $pageRange = 5;
            if( !empty($_config->products->pageRange) ){
                $pageRange = $_config->products->pageRange;
            }
            
            $paginator->setItemCountPerPage($itemPerPage);
            $paginator->setCurrentPageNumber($this->_request->getParam('page'));
            $paginator->setPageRange($pageRange);
            
            $this->_view->assign('paginator', $paginator);
        }
        
        public function render(){

            echo $this->_view->partial('partials/product.list.phtml', array(
                'paginator' => $this->_view->paginator,
                'numberOfColumns' => $this->_view->numberOfColumns,
                'itemViewScript' => $this->_view->itemViewScript,
                'paginationViewScript' => $this->_view->paginationViewScript
            ));
            
        }
    }
