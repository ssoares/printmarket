<?php
    class Cible_View_Helper_Tree extends Zend_View_Helper_Abstract
    {
        protected $_id;
        protected $_ul_id;
        protected $_class;
        protected $_customLiClass;
        protected $_attribs;
		protected $_showActions;
		protected $_actions;
        protected $_li_template = "<li id='%OBJECT_ID%' class='%LI_CLASS%'>";
        protected $_stripHtml = false;
        protected $_tplImgClassChanged = false;

        public function tree($tree, $options = array()){

            if(!empty( $options['id'] ) )
                $this->_id = $options['id'];

             if(!empty( $options['stripHtml'] ) )
                $this->_stripHtml = $options['stripHtml'];

            $this->_ul_id = !empty( $options['id'] ) ? "ul_{$options['id']}" : '';

            if(!empty( $options['class'] ) )
                $this->_class = $options['class'];

            if(!empty( $options['customLiClass'] ) )
                $this->_customLiClass = $options['customLiClass'];

            $this->_showActions = false;
            $this->_actions = array();

            $this->_attribs = '';
            if(!empty( $options['attribs'] ) && is_array( $options['attribs'] ) ){
                foreach( $options['attribs'] as $key => $val )
                    $this->_attribs .= " {$key}=\"{$val}\"";
            }

            $this->_setActions($options);

             return "<ul id='{$this->_ul_id}' class='{$this->_class}' $this->_attribs>".$this->generateList( $tree )."</ul>";
        }

        protected function generateList($tree, $level = 1){

            $content = '';

            foreach($tree as $object){
                $link = !empty($object['Link']) ? $object['Link'] : 'javascript:void(0);';
                $tmp = '';
                $title = $this->_stripHtml ? Cible_FunctionsGeneral::html2text($object['Title']) : $object['Title'];
                if(isset($object['MID_Show']) && $object['MID_Show']==1){
                    $tmp .= "<a id='menuTitle_{$object['ID']}' href='{$link}'>{$title}</a>";
                }
                else{
                    $tmp .= "<a class='italic-offline' id='menuTitle_{$object['ID']}' href='{$link}'>{$title}</a>";
                }

                if( !empty( $object['child'] ) ){
                    $tmp .= "<ul class='{$this->_class}'>";
                    $tmp .= $this->generateList( $object['child'], $level + 1 );
                    $tmp .= '</ul>';
                }

                $attr['liClass'] = !empty( $this->_customLiClass ) ? $this->_customLiClass : '';
                if ($object['Placeholder'] == 2 && $level > 1){
                    $this->_showActions = false;
                    if (isset($object['noHandle']) && $object['noHandle']){
                        $this->_li_template = str_replace("<img class='handle'", "<img class='drag-offline'", $this->_li_template);
                        $this->_tplImgClassChanged = true;
                    }
                }else{
                    $this->_showActions = true;
                }
                $content .= str_replace(array('%OBJECT_ID%','%LI_CLASS%'),array($object['ID'],$attr['liClass']), $this->_li_template).
					$tmp.
					$this->showActions($object['ID']).
					"</li>";
            }
            if ($this->_tplImgClassChanged){
                $this->_li_template = str_replace("<img class='drag-offline'", "<img class='handle'", $this->_li_template);
            }
            return $content;
        }

		protected function showActions($id){

            if( !$this->_showActions)
				return '';

			$action_links = '';

			foreach($this->_actions as $key => $val){
				$action_links .= "<a href='javascript:void(0);' class='{$key}'>{$val}</a>";
			}

			if( !empty( $action_links) )
				return "<div id='actions-dialog-{$id}' class='actions-dialog'><div class='hd'><div class='c'></div></div><div class='bd'><div class='c'><div class='s'><div id='actions_{$id}' class='actions'>{$action_links}</div></div></div></div><div class='ft'><div class='c'></div></div></div>";
		}

        private function _setActions($options = array())
        {
            if( !empty($options['actions']) && is_array($options['actions']) && count($options['actions']) > 0){
				$this->_showActions = true;
				$this->_actions = $options['actions'];
            }
        }
    }