<?php
    class Cible_View_Helper_CollapsibleTree extends Cible_View_Helper_Tree
    {

        protected $_collapse_images = array('open'=>'','close'=>'');

        public function collapsibleTree($tree, $options = array()){

            if(!empty( $options['images'] ) ){
                $this->_collapse_images['open'] = $options['images']['open'];
                $this->_collapse_images['close'] = $options['images']['close'];
            }

            if(!empty( $options['li_template'] ) ){
                $this->_li_template = $options['li_template'];
            }

            parent::tree($tree, $options);

             return "<ul id='{$this->_ul_id}' class='{$this->_class}'>".$this->generateList( $tree )."</ul>";
        }

        protected function generateList($tree, $level = 1){

            $content = '';

            foreach($tree as $object){
                $tmp = '';
                $tmp .= "<a href='{$object['onClick']}'>{$object['Title']}</a>";

                if( !empty( $object['child'] ) ){
                    $tmp = '<img class="handle" src='.$this->_collapse_images['close'].' align="absMiddle" style="cursor: pointer" onclick="var li = $(this).parents(\'li:first\');li.toggleClass(\'closed\'); if(li.hasClass(\'closed\')){$(this).attr(\'src\',\''.$this->_collapse_images['close'].'\')} else {$(this).attr(\'src\',\''.$this->_collapse_images['open'].'\')}" />' . $tmp;
                    $tmp .= '<ul>';
                    $tmp .= $this->generateList( $object['child'] );
                    $tmp .= '</ul>';
                }

                $attr['liClass'] = !empty( $this->_customLiClass ) ? "class='{$this->_customLiClass}'" : '';

                $content .= "<li id='{$object['ID']}' {$attr['liClass']} class='closed'>".
					$tmp.
					$this->showActions($object['ID']).
					"</li>";
            }

            return $content;
        }
    }