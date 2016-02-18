<?php

class Cible_Paginator extends Zend_Paginator
{

    protected $_db;
    protected $_view;
    protected $_request;
    protected $_renderPartial = 'partials/generic.list.phtml';
    protected $_adapter;

    public function setRenderPartialcript($renderPartial)
    {
        $this->_renderPartial = $renderPartial;
    }

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($select, $tables, $field_list, $options = null)
    {
        $this->_db = Zend_registry::get('db');
        $_frontController = Zend_Controller_Front::getInstance();
        $this->_request = $_frontController->getRequest();

        if (null === $this->_view)
        {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->_view = $viewRenderer->view;
        }

        if (!empty($options['actionKey']))
            $this->_view->actionKey = $options['actionKey'];

        if (!empty($options['renderPartial']))
            $this->_renderPartial = $options['renderPartial'];
        if (!empty($options['commands']))
            $this->_view->assign('commands', $options['commands']);

        if (!empty($options['to-excel-action']))
            $this->_view->assign('to_excel_action', $options['to-excel-action']);
        else
            $this->_view->assign('to_excel_action', 'to-excel');

        if (!empty($options['disable-export-to-excel']))
            $this->_view->assign('disable_export_to_excel', $options['disable-export-to-excel']);
        else
            $this->_view->assign('disable_export_to_excel', 'false');

        if (!empty($options['filters']))
        {
            $this->_view->assign('filters', $options['filters']);

            foreach ($options['filters'] as $key => $filter)
            {
                $filter_val = $this->_request->getParam($key);
                if (!empty($filter_val) || (!is_null($filter_val) && (int)$filter_val === 0))
                    if ($filter['associatedTo'] <> '')
                    {
                        if (!empty($filter['kindOfFilter']) && $filter['kindOfFilter'] == 'list')
                        {
                            $select->where("{$filter['associatedTo']} = '$filter_val'
                                                OR {$filter['associatedTo']} like '%,$filter_val'
                                                OR {$filter['associatedTo']} like '$filter_val,%'
                                                OR {$filter['associatedTo']} like '%,$filter_val,%'
                                 ");
                        }else{
                            $select->where("{$filter['associatedTo']} = ?", $filter_val);
                        }
                        if (!empty($filter['constraint']) && is_array($filter['constraint'])){
                            foreach ($filter['constraint'] as $key => $test){
                                $select->where($key . $test);
                            }
                        }
                    }
            }
        } else
        {
            $this->_view->assign('filters', array());
        }


        if (!empty($options['action_panel']))
        {
            if (!empty($options['action_panel']))
                $field_list['action_panel'] = $options['action_panel'];

            if (!empty($options['action_panel']['actions']))
                $this->_view->assign('action_links', $options['action_panel']['actions']);
        }

        $this->_view->assign('field_list', $field_list);

        if ($this->_request->getParam('order'))
        {
            $orderField = $this->_request->getParam('order');

            if (in_array($orderField, array_keys($field_list)))
            {

                $part = $select->getPart('order');
                $select->reset(Zend_Db_Select::ORDER);

                $direction = $this->_request->getParam('order-direction') ? $this->_request->getParam('order-direction') : 'ASC';
                $select->order($orderField . ' ' .$direction);

                $this->_view->assign('order', $this->_request->getParam('order'));
                $this->_view->assign('order_direction', $this->_request->getParam('order-direction'));
            }
        }

        $searchfor = $this->_request->getParam('searchfor');
        if ($searchfor)
        {
            $searching_on = array();
            $search_keywords = explode(' ', $searchfor);
            foreach ($tables as $table => $columns)
            {
                foreach ($columns as $column)
                {
                    $doSearch = true;
                    if (isset($options['onlyColumns'])
                        && !in_array($column, $options['onlyColumns'])){
                        $doSearch = false;
                    }
                    elseif (isset($options['excludedColums'])
                        && in_array($column, $options['excludedColums'])){
                        $doSearch = false;
                    }
                    if ($doSearch == true){
                    foreach ($search_keywords as $keyword)
                        array_push($searching_on, $this->_db->quoteInto("`{$table}`.{$column} LIKE ?", "%{$keyword}%"));
                    }
            }
            }

            if (!empty($searching_on))
                $select->where(implode(' OR ', $searching_on));
        }

        $this->_view->assign('searchfor', $searchfor);
        $adap = isset($options['adapter']) ? $options['adapter'] : '';
        switch ($adap)
        {
            case 'select':
                $this->_adapter = new Zend_Paginator_Adapter_DbSelect($select);
                break;
            case 'array':
                $data = array();
                $tmpData = $this->_db->fetchAll($select);
                foreach ($tmpData as $key => $tmp)
                {
                    $index = key($tmp);
                    if ($this->_view->isInArrays($tmp[$index], $options['adapterData'])){
                        $data[$key] = $options['adapterData'][$key];
                    }
                }
                $this->_adapter = new Zend_Paginator_Adapter_Array($data);
                break;
            case 'arrayAdmin':
                $tmpData = $this->_db->fetchAll($select);
                $data = array();
                if (!$searchfor){
                    $data = array_merge($tmpData, $options['adapterData']);
                }else{
                    $data = $tmpData;
                }
                $this->_adapter = new Zend_Paginator_Adapter_Array($data);
                break;

            default:
            $this->_adapter = new Zend_Paginator_Adapter_DbSelect($select);
                break;
        }

        $paginator = new Zend_Paginator($this->_adapter);
        $_config = Zend_Registry::get('config');

        $itemPerPage = 10;
        if (!empty($_config->lists->itemPerPage))
        {
            $itemPerPage = $_config->lists->itemPerPage;
        }

        if (!empty($options['list_options']['perPage']))
            $itemPerPage = $options['list_options']['perPage'];

        if ($this->_request->getParam('perPage'))
        {
            $itemPerPage = $this->_request->getParam('perPage') == 'all' ? $paginator->getTotalItemCount() : $this->_request->getParam('perPage');
        }

        $pageRange = 5;
        if (!empty($_config->lists->pageRange))
        {
            $pageRange = $_config->lists->pageRange;
        }

        $paginator->setItemCountPerPage($itemPerPage);
        $paginator->setCurrentPageNumber($this->_request->getParam('page'));
        $paginator->setPageRange($pageRange);

        $this->_view->assign('paginator', $paginator);
    }

    public function render(Zend_View_Interface $view = null)
    {
        echo $this->_view->partial($this->_renderPartial, array(
            'paginator' => $this->_view->paginator,
            'searchfor' => $this->_view->searchfor,
            'order' => $this->_view->order,
            'order_direction' => $this->_view->order_direction,
            'field_list' => $this->_view->field_list,
            'action_links' => $this->_view->action_links,
            'actionKey' => $this->_view->actionKey,
            'filters' => $this->_view->filters,
            'disable_export_to_excel' => $this->_view->disable_export_to_excel,
            'to_excel_action' => $this->_view->to_excel_action,
            'commands' => $this->_view->commands,
            'moduleName' => $this->_view->moduleName,
            'urlDetails' => $this->_view->urlDetails,
            'view' => $this->_view
        ));
    }

}
