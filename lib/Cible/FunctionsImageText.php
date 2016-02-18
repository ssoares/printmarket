<?php
    class Cible_FunctionsImageText
    {

        protected $_options = array();

        protected $_colorR = 0;
        protected $_colorG = 0;
        protected $_colorB = 0;
        protected $_positionX = 0;
        protected $_positionY = 13;  // because it is the bottom of the text that it is start to draw 0 = outside the top of the image
        protected $_text = "";
        protected $_fontfile = "../../www/themes/default/fonts/arial.ttf";
        protected $_size = 13;

        protected $_alignH = "";
        protected $_alignV = "";
        protected $_zoneWidth = 1;
        protected $_zoneHeight = 1;
        protected $_angle = 0;


        /**
        * Getter for the text.
        *
        * @return string
        */
        public function getText()
        {
            return $this->_text;
        }

        /**
        * Setter for the text
        */
        public function setText($value)
        {
            $this->_text = $value;
        }

        /**
        * Getter for the color red.
        *
        * @return int
        */
        public function getColorR()
        {
            return $this->_colorR;
        }

        /**
        * Setter for the color red.
        */
        public function setColorR($value)
        {
            $this->_colorR = $value;
        }

        /**
        * Getter for the color green.
        *
        * @return int
        */
        public function getColorG()
        {
            return $this->_colorG;
        }

        /**
        * Setter for the color green.
        */
        public function setColorG($value)
        {
            $this->_colorG = $value;
        }

        /**
        * Getter for the color blue.
        *
        * @return int
        */
        public function getColorB()
        {
            return $this->_colorB;
        }

        /**
        * Setter for the color blue.
        */
        public function setColorB($value)
        {
            $this->_colorB = $value;
        }

        /**
        * Getter for the PositionX.
        *
        * @return int
        */
        public function getPositionX()
        {
            return $this->_positionX;
        }

        /**
        * Setter for the PositionX.
        */
        public function setPositionX($value)
        {
            $this->_positionX = $value;
        }

        /**
        * Getter for the PositionY.
        *
        * @return int
        */
        public function getPositionY()
        {
            return $this->_positionY;
        }

        /**
        * Setter for the PositionY.
        */
        public function setPositionY($value)
        {
            $this->_positionY = $value;
        }

        /**
        * Getter for the fontfile.
        *
        * @return string
        */
        public function getFontfile()
        {
            return $this->_fontfile;
        }

        /**
        * Setter for the fontfile.
        */
        public function setFontfile($value)
        {
            $this->_fontfile = $value;
        }

        /**
        * Getter for the size.
        *
        * @return int
        */
        public function getSize()
        {
            return $this->_size;
        }

        /**
        * Setter for the size.
        */
        public function setSize($value)
        {
            $this->_size = $value;
        }

        /**
        * Getter for the zoneWidth.
        *
        * @return int
        */
        public function getZoneWidth()
        {
            return $this->_zoneWidth;
        }

        /**
        * Setter for the zoneWidth.
        */
        public function setZoneWidth($value)
        {
            $this->_zoneWidth = $value;
        }

        /**
        * Getter for the zoneHeight.
        *
        * @return int
        */
        public function getZoneHeight()
        {
            return $this->_zoneHeight;
        }

        /**
        * Setter for the zoneHeight.
        */
        public function setZoneHeight($value)
        {
            $this->_zoneHeight = $value;
        }

        /**
        * Getter for the angle.
        *
        * @return int
        */
        public function getAngle()
        {
            return $this->_zoneHeight;
        }

        /**
        * Setter for the angle.
        */
        public function setAngle($value)
        {
            $this->_angle = $value;
        }

        /**
        * Getter for the alignH.
        *
        * @return string
        */
        public function getAlignH()
        {
            return $this->_alignH;
        }

        /**
        * Setter for the alignH.
        */
        public function setAlignH($value)
        {
            $this->_alignH = $value;
        }

         /**
        * Getter for the alignV.
        *
        * @return string
        */
        public function getAlignV()
        {
            return $this->_alignV;
        }

        /**
        * Setter for the alignV.
        */
        public function setAlignV($value)
        {
            $this->_alignV = $value;
        }

        public function setParameters($params = array())
        {
            foreach (get_class_vars(get_class($this)) as $name=>$default){

                if($name!='_options'){
                    $this->$name = $default;
                }
            }

            foreach ($params as $property => $value)
            {
                $methodName = 'set' . ucfirst($property);

                if (property_exists($this, '_' . $property)
                    && method_exists($this, $methodName))
                {
                    $this->$methodName($value);
                }
            }

        }

        /**
        * @param array $params
        *      $option can be the following :
        *          - string text       the text to add to the image
        *          - int positionX     the position of the left text in X axe OR if alignH is set it is used has the starting or the ending position to align
        *          - int positionY     the position of the bottom text in Y axe
        *          - string fontfile   the font file containing .ttf
        *          - int size          the font size
        *          - int colorR        the font color red
        *          - int colorG        the font color green
        *          - int colorB        the font color blue
        *          - string alignH     left, center or right from the positionX value (if this is set, zoneWidth will be used to determine the correct positionX)
        *          - int zoneWidth     it is only used if the alignH option is set
        *          - string alignV     top, middle or bottom from the positionY (if this is set, zoneHeight will be used to determine the correct positionY)
        *          - int zoneHeight    it is only used if the alignV option is set
        *          - int angle         the angle of the text from 0 to 360
        *
        *
        */
        public function addOptions($params = array()){
            $this->setParameters($params);
            $array = array();
            foreach(get_class_vars('Cible_FunctionsImageText') as $key => $value){
                if($key!='_options')
                    $array[$key] = $this->$key;
            }
            array_push($this->_options,$array);

        }

         /**
         * Add a text to an image and create that image in the same directory
         *
         * @param string $path of image
         * @param string $newNamePath of the created image
         *
         * @return string  new image path
         */
        public function addTextToImage($path, $newNamePath)
        {
            $first = true;
            $destroyImage = false;
            foreach($this->_options as $option){

                $positionX = $option["_positionX"];
                $positionY = $option["_positionY"];

                if(!empty($option["_alignH"])){
                    if($option["_alignH"]=='right'){
                        list($left,, $right) = imageftbbox($option["_size"], 0, $option["_fontfile"], $option["_text"]);
                        $length = $right-$left;
                        $positionX = $positionX - $length;
                    }
                    else if($option["_alignH"]=='center'){
                        list($left,, $right) = imageftbbox($option["_size"], 0, $option["_fontfile"], $option["_text"]);
                        $center = ceil($option["_zoneWidth"] / 2);
                        $positionX = $positionX + $center - (ceil(($right-$left)/2));
                    }
                }
                if(!empty($option["_alignV"])){
                    if($option["_alignV"]=='top'){
                        list(,$bottom,,,,$top) = imageftbbox($option["_size"], 0, $option["_fontfile"], $option["_text"]);
                        $height = $bottom-$top;
                        $positionY = $positionY - $option['_zoneHeight'] + $height;
                    }
                    else if($option["_alignV"]=='middle'){
                        list(,$bottom,,,,$top) = imageftbbox($option["_size"], 0, $option["_fontfile"], $option["_text"]);
                        $height = $bottom-$top;
                        $center = ceil($option['_zoneHeight'] / 2);
                        $positionY = $positionY - $center + (ceil(($height)/2));
                    }
                }

                $image['ext'] = strtolower(substr($path, strrpos($path, '.') + 1));
                list($image['width'], $image['height'], $image['type'], $image['attr']) = getimagesize($path);
                if($image['ext'] == 'jpeg' || $image['ext'] == 'jpg'){

                    if($first==false){
                        $pathtmp = $newNamePath;
                    }
                    else{
                        $pathtmp = $path;
                    }
                    $newImage = imagecreatefromjpeg($pathtmp);
                    $thumb = imagecreatetruecolor($image['width'],$image['height']);
                    $text_color = imagecolorallocate($newImage, $option["_colorR"], $option["_colorG"], $option["_colorB"]);
                    imagettftext($newImage, $option["_size"] , $option["_angle"] , $positionX, $positionY , $text_color , $option["_fontfile"] , $option["_text"] );
                    imagecopyresampled($thumb,$newImage,0,0,0,0,$image['width'],$image['height'],$image['width'],$image['height']);
                    imagejpeg($thumb, $newNamePath, 100);
                    $destroyImage = true;
                }

                elseif($image['ext'] == 'gif'){
                    if($first==false){
                        $path = $newNamePath;
                    }
                    $newImage = imagecreatefromgif($path);
                    $thumb = imagecreate($image['width'],$image['height']);
                    $text_color = imagecolorallocate($newImage, $option["_colorR"], $option["_colorG"], $option["_colorB"]);
                    imagettftext($newImage, $option["_size"] , $option["_angle"] , $positionX, $positionY , $text_color , $option["_fontfile"] , $option["_text"] );
                    $trans_color = imagecolorallocate($thumb, 255, 0, 0);
                    imagecolortransparent($thumb, $trans_color);
                    imagecopyresampled($thumb,$newImage,0,0,0,0,$image['width'],$image['height'],$image['width'],$image['height']);
                    imagegif($thumb, $newNamePath);

                    $destroyImage = true;

                }
                elseif($image['ext'] == 'png'){
                    if($first==false){
                        $path = $newNamePath;
                    }
                    $image_source = imagecreatefrompng($path);
                    $newImage = imagecreatetruecolor($image['width'],$image['height']);
                    $text_color = imagecolorallocate($newImage, $option["_colorR"], $option["_colorG"], $option["_colorB"]);
                    imagettftext($image_source, $size , $option["_angle"] , $positionX, $positionY , $text_color , $option["_fontfile"] , $option["_text"] );
                    if (function_exists('imagecolorallocatealpha')){
                        imagealphablending($newImage, false);
                        imagesavealpha($newImage, true);
                        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                        imagefilledrectangle($newImage, 0, 0, $image['width'], $image['height'], $transparent);
                        imagecolortransparent($newImage, $transparent);
                    }
                    imagecopyresampled($newImage, $image_source, 0, 0, 0, 0, $image['width'],$image['height'],$image['width'],$image['height']);
                    imagepng($newImage, $newNamePath);

                    $destroyImage = true;

                }
                $first = false;
            }
            if($destroyImage == true){
                imagedestroy($newImage);
            }
       }


        public static function fullUpperInFrench($string){
            return strtr(strtoupper($string), array(
              "à" => "À",
              "è" => "È",
              "ì" => "Ì",
              "ò" => "Ò",
              "ù" => "Ù",
              "á" => "Á",
              "é" => "É",
              "í" => "Í",
              "ó" => "Ó",
              "ú" => "Ú",
              "â" => "Â",
              "ê" => "Ê",
              "î" => "Î",
              "ô" => "Ô",
              "û" => "Û",
              "ç" => "Ç",
            ));
        }
  }

?>