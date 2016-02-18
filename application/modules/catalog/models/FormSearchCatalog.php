<?php
class FormSearchCatalog extends Cible_Form
{
    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;
        parent::__construct($options);
        $categoryId = (isset($options['categorieId']) ? $options['categorieId'] : 0);
        $kw = (!empty($options['keywords'])) ? $options['keywords'] : '';
        $orderByOptions = array(
            '' => $this->getView()->getCibleText('label_order_display')
            , 'P_Seq Desc' => 'Test order by'
            );
        $orderBy = new Zend_Form_Element_Select('orderBy', array(
//            'placeholder' => $this->getView()->getCibleText('label_order_display'),
            'decorators' => array('ViewHelper'),
            'multiOptions' => $orderByOptions
        ));
        $orderBy->addFilters(array('StringTrim', 'StripTags'));
        $perPageOptions = array(
            '' => $this->getView()->getCibleText('form_list_items_per_page_end')
            , 6 => 6
            , 15 => 15
            , 75 => 75
            );
        $nbPerPage = new Zend_Form_Element_Select('limit', array(
//            'placeholder' => $this->getView()->getCibleText('form_list_items_per_page_end'),
            'decorators' => array('ViewHelper'),
            'multiOptions' => $perPageOptions
        ));
        $nbPerPage->addFilters(array('StringTrim', 'StripTags'));
        $oC= new CatalogCategoriesObject();
        $categories = $oC->getList(false, null, false);
        array_shift($categories);
        array_unshift($categories, $this->getView()->getCibleText('form_label_P_CategoryID'));
        $category = new Zend_Form_Element_Select('catId', array(
            'class' => '',
            'decorators' => array('ViewHelper', 'Errors'),
            'multiOptions' => $categories
            ));
        $category->addFilters(array('StringTrim', 'StripTags'));
        // Keywords
        $url = $this->getView()->baseUrl("/catalog/index/ajax/actionAjax/kwlist");
        $keywords = new Zend_Form_Element_Text('keywords', array(
            'placeholder' => $this->getView()->getCibleText('form_search_product_label'),
            'value' => $kw,
            'attribs' => array('class' => 'form-control tokenfield-typeahead',
                'style' => 'display:inline-block;',
                'data-autocomplete' => true,
                'data-url' => $url),
            'Decorators' => array('ViewHelper', 'label')
            ));
        $keywords->addFilters(array('StringTrim', 'StripTags'));
        $keywordsId = new Zend_Form_Element_Hidden('keywordsId', array(
            'attribs' => array('data-parent' => 'keywords'),
            'Decorators' => array('ViewHelper')
            ));
        $keywordsId->addFilters(array('StringTrim', 'StripTags'));

        // search button
        $searchButton = new Zend_Form_Element_Submit('searchCatalog', array(
            'attribs' => array('class' => 'btn btn-primary btn-lg'),
            'label' => $this->getView()->getCibleText('button_search_label'),
            'decorators' => array('ViewHelper')
        ));

        $this->addElement($category);
        $this->addElement($orderBy);
        $this->addElement($nbPerPage);
        $this->addElement($keywords);
        $this->addElement($keywordsId);
        $this->addElement($searchButton);

        $this->setDecorators(array('FormElements', 'Form'));

        $this->setAction('');
        $this->clearAttribs();
    }

    /**
     * Retrieve fields name into an array to exclude these fields later.
     * @param string $name
     * @return array
     */
    public function getElementsId($name = '')
    {
        $ids = array();
        if (!empty($name) && array_key_exists($name, $this->getElements())){
            $ids[] = $this->getElement($name)->getName();
        }else{
            $data = $this->getElements();
            foreach($data as $key => $element){
                $ids[] = $element->getName();
            }
        }

        return $ids;
    }
}