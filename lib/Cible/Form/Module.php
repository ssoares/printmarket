<?php
    class Cible_Form_Module extends Cible_Form_Multilingual
    {
        protected $tableName = '';
        protected $tableFieldPrefix = '';

        public function __construct($options = null)
        {
            parent::__construct($options);

            if( empty( $this->tableName) )
                throw new Exception('You need to set the $tableName protected variable in your Form instance');

            $baseDir = $options['baseDir'];
            Zend_Registry::set('baseUrl',$baseDir);
            $cancel_url = $options['cancelUrl'];

            // Title
            $title = new Zend_Form_Element_Text($this->tableFieldPrefix . 'Title');
            $title->setLabel(Cible_Translation::getCibleText('form_label_title'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => Cible_Translation::getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');

            $label = $title->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($title);

            // Status
            $status = new Zend_Form_Element_Select($this->tableFieldPrefix . 'Status');
            $status->setLabel(Cible_Translation::getCibleText('form_label_status'))
            ->setAttrib('class','stdSelect');

            $db = $this->_db;
            $sql = 'SELECT * FROM Status';
            $status_options = $db->fetchAll($sql);

            foreach ($status_options as $_option){
                $status->addMultiOption($_option['S_ID'], Cible_Translation::getCibleText("status_{$_option['S_Code']}") );
            }

            $this->addElement($status);
        }
    }
?>
