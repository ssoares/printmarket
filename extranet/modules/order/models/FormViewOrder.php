<?php
    
class FormViewOrder extends Cible_Form
{
    public function __construct($options = null)
    {
        $data = $options['data'];
        unset($options['data']);
        parent::__construct($options);

        
        foreach ($data as $name => $value)
        {
            $tmp = explode('_', $name);
            $text = array_pop($tmp) . ' : ' . $value;
            $elem = new Cible_Form_Element_Html(
                    $name,
                    array('value' => $text )
                );
            $elem->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'form_title_inline left')
                    ),
                )
        );
            $this->addElement($elem);
        }
    }
}