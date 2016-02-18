
<?php

class FormBlockNews extends Cible_Form_Block
{

    protected $_moduleName = 'news';

    public function __construct($options = null)
    {
        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        parent::__construct($options);

        /*         * ************************************* */
        // PARAMETERS
        /*         * ************************************* */

        // select box category (Parameter #1)
        $blockCategory = new Zend_Form_Element_Select('Param1');
        $blockCategory->setLabel($this->getView()->getCibleText('label_category_news_bloc'))
            ->setAttrib('class', 'largeSelect')
            ->setOrder(3);

        $categories = new Categories();
        $select = $categories->select()->setIntegrityCheck(false)
            ->from('Categories')
            ->join('CategoriesIndex', 'C_ID = CI_CategoryID')
            ->where('C_ModuleID = ?', 2)
            ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
            ->order('CI_Title');

        $categoriesArray = $categories->fetchAll($select);

        foreach ($categoriesArray as $category)
        {
            $blockCategory->addMultiOption($category['C_ID'], $category['CI_Title']);
        }

        $this->addElement($blockCategory);

        // number of news to show in front-end (Parameter #2)
        $blockNewsMax = new Zend_Form_Element_Text('Param2');
        $blockNewsMax->setLabel($this->getView()->getCibleText('label_number_news_show'))
            ->setAttrib('class', 'smallTextInput')
            ->setOrder(4);

        $this->addElement($blockNewsMax);

        // show the breif text in front-end (Parameter #3)
        $blockShowBrief = new Zend_Form_Element_Checkbox('Param3');
        $blockShowBrief->setLabel($this->getView()->getCibleText('label_show_brief_text'))
            ->setOrder(5);
        $blockShowBrief->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($blockShowBrief);

        // display order (Parameter #4)
        $blockOrder = new Zend_Form_Element_Select('Param4');
        $blockOrder->setLabel($this->getView()->getCibleText('label_order_display'))
            ->setAttrib('class', 'largeSelect')
            ->setOrder(6);

        $blockOrder->addMultiOption('ND_Date DESC', $this->getView()->getCibleText('label_date_desc'));
        $blockOrder->addMultiOption('ND_Date ASC', $this->getView()->getCibleText('label_date_asc'));
        $blockOrder->addMultiOption('NI_Title ASC', $this->getView()->getCibleText('label_alpha_asc'));

        $this->addElement($blockOrder);

        // display news date (Parameter #5)
        $blockDate = new Zend_Form_Element_Checkbox('Param5');
        $blockDate->setLabel($this->getView()->getCibleText('label_date_news'))
            ->setOrder(7);
        $blockDate->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($blockDate);


        // display news pics (Parameter #6)
        $blockPics = new Zend_Form_Element_Checkbox('Param6');
        $blockPics->setLabel($this->getView()->getCibleText('label_images_show_news'))
            ->setOrder(8);
        $blockPics->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        $this->addElement($blockPics);
        // display news pics (Parameter #7)
        $showFilter = new Zend_Form_Element_Checkbox('Param7');
        $showFilter->setLabel($this->getView()->getCibleText('form_label_showFilter'))
            ->setOrder(9);
        $showFilter->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $this->addElement($showFilter);

        $this->removeDisplayGroup('parameters');

        $this->addDisplayGroup(array('Param999', 'Param1', 'Param2', 'Param3', 'Param4', 'Param5', 'Param6', 'Param7'), 'parameters');
        $parameters = $this->getDisplayGroup('parameters');

        $script =<<< EOS
            $('#Param999').change(function(){
//                $('#Param1').show();
//                $('label[for=Param1]').show();
                $('#Param2').show();
                $('label[for=Param2]').show();
                $('#Param3').show();
                $('label[for=Param3]').show();
                $('#Param4').show();
                $('label[for=Param4]').show();
                $('#Param5').show();
                $('label[for=Param5]').show();
                $('#Param6').show();
                $('label[for=Param6]').show();
                $('#Param7').show();
                $('label[for=Param7]').show();

            if (($(this).val() == 'details')||($(this).val() == 'gestion'))
            {
//                $('#Param1').hide();
//                $('label[for=Param1]').hide();
                $('label[for=Param2]').hide();
                $('#Param2').hide();
                $('#Param3').hide();
                $('label[for=Param3]').hide();
                $('#Param4').hide();
                $('label[for=Param4]').hide();
                $('#Param6').hide();
                $('label[for=Param6]').hide();
                $('#Param7').hide();
                $('label[for=Param7]').hide();

                if($(this).val() == 'gestion'){
                    $('#Param5').hide();
                    $('label[for=Param5]').hide();
                }
            }
        }).change();
EOS;
        $this->getView()->inlineScript()->appendScript($script);
    }

}
