<?php
class Cible_FunctionsCrop
{
    protected $_options = array();
    protected $_fileSource = "";
    protected $_fileDestination = "";
    protected $_sizeXWanted = 0;
    protected $_sizeYWanted = 0;
    protected $_ratioX = 1;
    protected $_ratioY = 1;
    protected $_returnPage = "";
    protected $_cancelPage = "";
    protected $_view;
    protected $_formData;
    protected $_maxWShow;
    protected $_maxHShow;
    protected $_maxWPreShow;
    protected $_maxHPreShow;
    protected $_realWShow;
    protected $_realHShow;
    protected $_submitPage;
    protected $_showActionButton;

    public function __construct($options = null, $formData = null)
    {
        if (null === $this->_view){
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->_view = $viewRenderer->view;
        }
        if (!empty($options))
        {
            $this->_fileSource .= (isset($options['fileSource'])) ? $options['fileSource'] : "";
            $this->_fileDestination .= (isset($options['fileDestination'])) ? $options['fileDestination'] : "";
            $this->_sizeXWanted = (isset($options['sizeXWanted'])) ? $options['sizeXWanted'] : 100;
            $this->_sizeYWanted = (isset($options['sizeYWanted'])) ? $options['sizeYWanted'] : 100;
            $this->_ratioX = (isset($options['ratioX'])) ? $options['ratioX'] : $this->_sizeXWanted;
            $this->_ratioY = (isset($options['ratioY'])) ? $options['ratioY'] : $this->_sizeYWanted;
            $this->_maxWPreShow = (isset($options['maxWPreShow'])) ? $options['maxWPreShow'] : 960;
            $this->_maxHPreShow = (isset($options['maxHPreShow'])) ? $options['maxHPreShow'] : 600;

            $this->_returnPage = (isset($options['returnPage'])) ? $options['returnPage'] : "";
            $this->_cancelPage = (isset($options['cancelPage'])) ? $options['cancelPage'] : "";
            $this->_submitPage = (isset($options['submitPage'])) ? $options['submitPage'] : "";

            $this->_showActionButton = (isset($options['showActionButton'])) ? $options['showActionButton'] : false;

//            $image = "../" . stristr($this->_fileSource, "data");
            $image = $_SERVER['DOCUMENT_ROOT'] . $this->_fileSource;
            list($imagewidth, $imageheight, $imageType) = getimagesize($image);

            $this->_realWShow = 1;
            $this->_realHShow = 1;
            $this->_maxWShow = $imagewidth;
            $this->_maxHShow = $imageheight;

            if ($imagewidth >= $this->_maxWPreShow){
                   $this->_maxWShow = $this->_maxWPreShow;
                   $this->_realHShow = $this->_realWShow = $imagewidth / $this->_maxWShow;
                   $this->_maxHShow = $imageheight / $this->_realHShow;
               }elseif ($imageheight >= $this->_maxHPreShow){
                   $this->_maxHShow = $this->_maxHPreShow;
                   $this->_realHShow = $this->_realWShow = $imageheight / $this->_maxHShow;
                   $this->_ratio = $this->_maxHShow / $imageheight;
                   $this->_maxWShow = $imagewidth / $this->_realHShow;
               }
        }

        if (!empty($formData))
            $this->_formData = $formData;
    }

    public function cropRenderImage()
    {
        echo $this->_view->partial('partials/ImageCrop.phtml', array(
            'fileSource' => $this->_fileSource,
            'fileDestination' => $this->_fileDestination,
            'ratioX' => $this->_ratioX,
            'ratioY' => $this->_ratioY,
            'maxWShow' => (int)$this->_maxWShow,
            'maxHShow' => (int)$this->_maxHShow,
            'sizeXWanted' => $this->_sizeXWanted,
            'sizeYWanted' => $this->_sizeYWanted,
            'realWShow' => $this->_realWShow,
            'realHShow' => $this->_realHShow,
            'maxWPreShow' => $this->_maxWPreShow,
            'maxHPreShow' => $this->_maxHPreShow,
            'returnPage' => $this->_returnPage,
            'cancelPage' => $this->_cancelPage,
            'submitPage' => $this->_submitPage,
            'showActionButton' => $this->_showActionButton
        ));
    }

    public function cropImage()
    {
        if ($this->_formData['ImageSrc'] <> "")
        {
            //Get the new coordinates to crop the image.
            $realHShow = $this->_formData["realHShow"];
            $realWShow = $this->_formData["realWShow"];
            $x1 = $this->_formData["x1"] * $realWShow;
            $y1 = $this->_formData["y1"] * $realHShow;
            $x2 = $this->_formData["x2"] * $realWShow;
            $y2 = $this->_formData["y2"] * $realHShow;
            $w = $this->_formData["w"] * $realWShow;
            $h = $this->_formData["h"] * $realHShow;
            if (($w > 0) && ($h > 0))
            {
                $sizeYWanted = $this->_formData["sizeYWanted"];
                $sizeXWanted = $this->_formData["sizeXWanted"];
                $scale = 1;
                $scaleX = $sizeXWanted / $w;
                $scaleY = $sizeYWanted / $h;
                $cropped = $this->resizeThumbnailImage($this->_formData['ImageSrc'], $this->_formData['ImageDestination'], $w, $h, $x1, $y1, $scaleX, $scaleY);
            }
        }
    }

    function resizeThumbnailImage($image, $thumb_image_name, $width, $height, $start_width, $start_height, $scaleX, $scaleY)
    {
        $image = $_SERVER['DOCUMENT_ROOT'] . $image;
        $thumb_image_name = $_SERVER['DOCUMENT_ROOT'] . $thumb_image_name;

        list($imagewidth, $imageheight, $imageType) = getimagesize($image);
        $imageType = image_type_to_mime_type($imageType);

        $newImageWidth = ceil($width * $scaleX);
        $newImageHeight = ceil($height * $scaleY);
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        switch ($imageType)
        {
            case "image/gif":
                $source = imagecreatefromgif($image);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                $source = imagecreatefromjpeg($image);
                break;
            case "image/png":
            case "image/x-png":
                $source = imagecreatefrompng($image);
                break;
        }
        imagecopyresampled($newImage, $source, 0, 0, $start_width, $start_height, $newImageWidth, $newImageHeight, $width, $height);
        switch ($imageType)
        {
            case "image/gif":
                imagegif($newImage, $thumb_image_name);
                break;
            case "image/pjpeg":
            case "image/jpeg":
            case "image/jpg":
                imagejpeg($newImage, $thumb_image_name, 100);
                break;
            case "image/png":
            case "image/x-png":
                imagepng($newImage, $thumb_image_name);
                break;
        }
        chmod($thumb_image_name, 0777);

        //echo $imagewidth . " " . $imageheight;
        //exit;
        return $thumb_image_name;
    }

}
