<?php
/**
 * Description of CssSpritesheet
 *
 * @author draluc
 */
abstract class Cible_CssSpritesheet
{
    protected static $_folder = 'myfolder';
    protected static $_outputCSS;
    protected static $_outputImage;

    public function getFolder()
    {
        return $this->_folder;
    }

    public function getOutputCSS()
    {
        return $this->_outputCSS;
    }

    public function getOutputImage()
    {
        return $this->_outputImage;
    }

    public function setFolder($folder)
    {
        $this->_folder = $folder;
        return $this;
    }

    public function setOutputCSS($outputCSS)
    {
        $this->_outputCSS = $outputCSS;
        return $this;
    }

    public function setOutputImage($outputImage)
    {
        $this->_outputImage = $outputImage;
        return $this;
    }

    /** 
     * 
     */
    public static function factory(array $config = array())
    {
        self::setProperties($config);
        new Cible_CssSpritesheet_Generator();

    }

    protected function __construct(array $config = array()) {}

    /**
     * Set global configuration options
     *
     * @param array $config
     */
    public static function setProperties(array $config)
    {
        foreach ($config as $k => $v) {
            $property = '_' . $k;
            self::$$property = $v;
        }
    }

}