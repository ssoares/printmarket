<?php
    /** Zend_Form_Element_Xhtml */
    require_once 'Zend/Form/Element/Hidden.php';

    class Cible_Form_Element_ImageGalleryPicker extends Zend_Form_Element_Hidden
    {
        public function __construct($spec, $options = null)
        {
            parent::__construct($spec, $options);

            $this->associatedElement = $options['associatedElement'];
            $this->galleryID    = $options['galleryID'];
        }
        /**
         * Render form element
         *
         * @param  Zend_View_Interface $view
         * @return string
         */
        public function render(Zend_View_Interface $view = null)
        {

            //$_id = $this->getId();
            $_lang = Zend_Registry::get('languageID') == 1 ? 'fr' : 'en';

            $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
            $defaultImage = $_baseUrl . "/icons/image_non_ disponible.jpg";

            $this->_view->headScript()->appendFile($this->getView()->baseUrl().'/js/tiny_mce/plugins/imagemanager/js/mcimagemanager.js');
            if (null !== $view) {
                $this->setView($view);
            }

            $content = '';

            $content .= '
                <script>
                    function separateImage($associatedElement, $defaultImage, $imagePicker){
                        document.getElementById($associatedElement).src = $defaultImage;
                        document.getElementById($imagePicker).value = "";
                    }

                    function dump(arr,level) {
                        var dumped_text = "";
                        if(!level) level = 0;

                        //The padding given at the beginning of the line.
                        var level_padding = "";
                        for(var j=0;j<level+1;j++) level_padding += "    ";

                        if(typeof(arr) == "object") { //Array/Hashes/Objects
                         for(var item in arr) {
                          var value = arr[item];

                          if(typeof(value) == "object") { //If it is an array,
                           dumped_text += level_padding + "\'" + item + "\' ...\n";
                           dumped_text += dump(value,level+1);
                          } else {
                           dumped_text += level_padding + "\'" + item + "\' => \"" + value + "\"\n";
                          }
                         }
                        } else { //Stings/Chars/Numbers etc.
                         dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
                        }
                        return dumped_text;
                    }

                </script>';

            //$content .= "<a href=\"javascript:mcImageManager.upload({fields : '', path : '../../../../../data/images/gallery/".$this->galleryID."'});\">[Parcourir]</a>";
            //$content .= "<img id='".$this->getId()."_preview' src='' border=0 />";
            //$content .= "<input id='temporaire' type='hidden' value='' />";

            if($this->galleryID <> "")
                $pathTmp = "../../../../../data/images/gallery/".$this->galleryID."/tmp";
            else
                $pathTmp = "../../../../../data/images/gallery/tmp";
                $content .= "<a href=\"javascript:mcImageManager.upload({fields : '".$this->getId()."',
                                                                        path : '".$pathTmp."',
                                                                        insert_filter : function (data){
                                                                        },
                                                                        onupload :  function(info) {
                                                                                        /*alert(dump(info)); */
                                                                                        document.getElementById('".$this->getId()."_tmp').value = info.files[0].custom.thumbnail_url;
                                                                                        document.getElementById('".$this->getId()."_original').value = info.files[0].url;
                                                                                        document.getElementById('".$this->getId()."_preview').src = info.files[0].custom.thumbnail_url;
                                                                                        document.getElementById('".$this->getId()."').value = info.files[0].name;
                                                                                    }});\">[Parcourir]</a>";
            //$content .=  "&nbsp;&nbsp;<img class='action_icon' alt='Supprimer' src='".$_baseUrl."/icons/del_icon_16x16.png' onclick='separateImage(\"".$this->associatedElement."\",\"".$defaultImage."\",\"".$this->getId()."\")' />";

           // alert(dump(data));
            //document.getElementById('".$this->getId()."_preview').src = imageSrc;

            foreach ($this->getDecorators() as $decorator) {
                $decorator->setElement($this);
                $content = $decorator->render($content);
            }
            return $content;
        }
    }