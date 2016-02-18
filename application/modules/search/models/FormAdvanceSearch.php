<?php


class FormAdvanceSearch extends Cible_Form
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;

        parent::__construct($options);

        $this->setAttrib("id", "advancedSearch");

        $words = new Zend_Form_Element_Text('words');
        $words->setLabel($this->getView()->getClientText('form_label_advance_words'));
        $this->addElement($words);
        $searchOptions = new Zend_Form_Element_Radio('searchOption');
        $searchOptions->setLabel($this->getView()->getClientText('form_label_advance_options'))
            ->setDecorators(array(
                'ViewHelper',
                'Label',
                array('label', array('tag' => 'dt', 'placement' => 'prepend')),
//                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'radio')),
            ))
        ;
        $arrayOpt = array(
            1 => $this->getView()->getClientText('form_label_advance_option_one'),
            2 => $this->getView()->getClientText('form_label_advance_option_all'),
            3 => $this->getView()->getClientText('form_label_advance_option_exact')
            );
        $searchOptions->addMultiOptions($arrayOpt);
        $searchOptions->setValue(1);
        $this->addElement($searchOptions);

        $filterList = new Zend_Form_Element_Select('sites');
        $filterList->setLabel($this->getView()->getClientText('form_label_advance_sites'));
        $config = Zend_Registry::get('config');
        foreach ($config->multisite as $data)
        {
            if ((bool) $data->active)
            $sites[$data->name] = $this->getView()->getClientText('site_label_' . $data->name);
        }
        $sites['all'] = $this->getView()->getClientText('form_label_advance_allsites');

        $filterList->addMultiOptions($sites)
            ->setDecorators(array(
                'ViewHelper',
                'Label',
                array('label', array('placement' => 'prepend')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'sites')),
            ));
        $this->addElement($filterList);

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->getView()->getClientText('Trouver'))
            ->removeDecorator('DtDdWrapper');
        $submit->setDecorators(array(
            'ViewHelper',
            array('Description'),
            array('HtmlTag', array('tag' => 'dd', 'class'=>'formFindButton')),
        ));
        $this->addElement($submit);

    }

}
