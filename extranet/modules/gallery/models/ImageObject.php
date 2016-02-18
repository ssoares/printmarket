<?php

    class ImageObject extends DataObject
    {
        protected $_dataClass = 'Images';
        protected $_dataId = 'I_ID';
        protected $_dataColumns = array(
            'ImageSrc' => 'I_OriginalLink'
        );
        
        protected $_indexClass = 'ImagesIndex';
        protected $_indexId = 'II_ImageID';
        protected $_indexLanguageId = 'II_LanguageID';
        protected $_indexColumns = array(
            'II_Title' => 'II_Title',
            'II_Description' => 'II_Description'
        );
    }
    
?>
