<?php
    class FormBlockGallery extends Cible_Form_Block   
    {
        protected $_moduleName = 'gallery';
        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];
            
            parent::__construct($options);
            
            /****************************************/
            // PARAMETERS
            /****************************************/
            // select box category (Parameter #1)
            $blockCategory = new Zend_Form_Element_Select('Param1');
            $blockCategory->setLabel($this->getView()->getCibleText('form_gallery_blockCategory_label'))
            ->setAttrib('class','largeSelect')
            ->setOrder(11);
            
            $categories = new Categories();
            $select = $categories->select()->setIntegrityCheck(false)
                                 ->from('Categories')
                                 ->join('CategoriesIndex', 'C_ID = CI_CategoryID')
                                 ->where('C_ModuleID = ?', 9)
                                 ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
                                 ->order('CI_Title');
            
            $categoriesArray = $categories->fetchAll($select);
            
            foreach ($categoriesArray as $category){
                $blockCategory->addMultiOption($category['C_ID'],$category['CI_Title']); 
            }

            $this->addElement($blockCategory);
            
            
            $blockGallery = new Zend_Form_Element_Select('Param2');
            $blockGallery->setLabel($this->getView()->getCibleText('form_gallery_blockGallerey_label'))
            ->setAttrib('class','largeSelect')
            ->setOrder(12);
            $galleries = new Galleries();
            $selectG = $galleries->select()->setIntegrityCheck(false)
                                 ->from('Galleries')
                                 ->join('GalleriesIndex', 'G_ID = GI_GalleryID')
                                 ->where('G_Online = 1')
                                 ->where('GI_LanguageID = ?', Zend_Registry::get("languageID"))
                                 ->order('GI_Title');
            
            $galleriesArray = $galleries->fetchAll($selectG);
            //echo $selectG;
            $blockGallery->addMultiOption('0',$this->getView()->getCibleText('form_gallery_blockGallerey_None'));
            foreach ($galleriesArray as $gallery){
                $blockGallery->addMultiOption($gallery['GI_GalleryID'],$gallery['GI_Title']); 
            }

            $this->addElement($blockGallery);
            
            $this->removeDisplayGroup('parameters');
            $this->addDisplayGroup(array('Param999', 'Param1','Param2'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');
        }        
    }
?>


