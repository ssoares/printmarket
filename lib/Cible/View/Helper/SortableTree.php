<?php
    class Cible_View_Helper_SortableTree extends Cible_View_Helper_Tree
    {
        protected $_acceptClass;

        public function sortableTree($tree, $options = array()){

            $this->_li_template = "<li id='%OBJECT_ID%' class='%LI_CLASS%'><img class='handle' src='" . $this->view->baseUrl() . "/icons/icon-drag-22x22.png' />";

            $menu = parent::tree($tree, $options);

            if( empty( $options['acceptClass'] ) )
                Throw new Exception('SortableTree must define the acceptClass options to work');

            $attr['acceptClass'] = $options['acceptClass'];
            $attr['onChange'] =  !empty( $options['onChange'] ) ? $options['onChange'].'(serialized)' : 'alert(\'Implementing the onchange method would let you save the state.\');' ;
            $attr['receive'] = !empty( $options['receive'] ) ? $options['receive'].'(event, ui)' : '' ;
            $attr['stop'] = !empty( $options['stop'] ) ? $options['stop'].'(event, ui)' : '' ;

            // Removed from var $script
            /**/

            $script = <<< EOS

            function format{$this->_id}SortableTree(){

                $('ul#ul_{$this->_id}').find('ul').each(function(){
                    if( $(this).children('li').size() == 0 ){
                        $(this).remove();
                    }
                });

                $('ul#ul_{$this->_id}').find('li').each(function(){

                    var child_list = $(this).find('ul:first');

                    if( child_list.size() > 0){
                        $(this).addClass('hasChildren');
                    } else {
                        $(this).removeClass('hasChildren');
                    }
                });
            }

            function build{$this->_id}Sortable(){

                $('ul#ul_{$this->_id}').SortableDestroy();
                $('ul#ul_{$this->_id}').NestedSortable({
                    accept: '{$attr['acceptClass']}',
                    helperclass: 'helper',
                    handle: '.handle',
                    noNestingClass: 'no-nesting',
                    onChange: function(serialized){
                        {$attr['onChange']}
                        build{$this->_id}Sortable();
                        format{$this->_id}SortableTree();

                    },
                    receive: function(event, ui){
                        {$attr['receive']}
                    },
                    stop: function(event, ui){
                        {$attr['stop']}
                    }
                });
            }

            $(function(){

                $('ul#ul_{$this->_id}').NestedSortable({
                    accept: '{$attr['acceptClass']}',
                    helperclass: 'helper',
                    handle: '.handle',
                    noNestingClass: 'no-nesting',
                    nestingPxSpace: '10',
                    onChange: function(serialized){
                        {$attr['onChange']}
                        build{$this->_id}Sortable();
                        format{$this->_id}SortableTree();

                    },
                    receive: function(event, ui){
                        {$attr['receive']}
                    },
                    stop: function(event, ui){
                        {$attr['stop']}
                    }
                });
                format{$this->_id}SortableTree();
            });

EOS;

            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/jquery/jquery.livequery.js");
            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/jquery/jquery.rightClick.js");
            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/interface.js");
            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/inestedsortable.js");
            $this->view->headScript()->appendFile("{$this->view->baseUrl()}/js/csa/overlay.js");
            $this->view->headScript()->appendScript($script);
            //$this->view->headScript()->offsetSetScript(100,$script);

            return $menu;
        }
    }