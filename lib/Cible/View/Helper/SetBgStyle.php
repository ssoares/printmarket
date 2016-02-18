<?php

/**
 * Cible
 *
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id: SetBgStyle.php 762 2012-01-12 19:21:56Z ssoares $
 */

/**
 * Set the backcground css style tag
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_setBgStyle extends Zend_View_Helper_Abstract
{
    const RANDOM    = 'rand';
    const RANDSTART = 'randStart';
    const INCREMENT = 'increment';

    protected $_folderPath = "/themes/default/images/backgrounds/";

    /**
     * Set into the registry the html tag style for the background.
     * Fetch all the image files in the folder and according to the file name
     * build an array with the file path and the color for the background.
     * <br>
     * The file name must contains the hexdecimal color value at the end and
     * with an underscore before it.
     * <br>
     * Select an image randomly and set the string in the registry as bgStyle.
     *
     * @param string $type       Defines the type to select the image and the style.
     * @param string $folderPath The relative path <strong>on the server</strong>
     *                           of the folder containing the images.
     * @return string bgStyle The parameter in the registry. It can be retrieved
     *                        with Zend_Registry::get('bgStyle')
     */
    public function setBgStyle($type = 'rand', $folderPath = '')
    {
        $images = array();

        if (!empty($folderPath))
            $this->_folderPath = $folderPath;

        $part = $this->view->BaseUrl() . $this->_folderPath;
        $dir  = $_SERVER['DOCUMENT_ROOT'] . $part;

        $dirHandler = opendir($dir);

        $i = 1;

        while (($file = readdir($dirHandler)) !== false)
        {
            $color = '';
            $realPath = realpath($dir . $file);
            $infos    = pathinfo($dir . $file);
            if (filetype($realPath) == 'file')
            {
                $tmpData = explode('_', $infos['filename']);
                if (count($tmpData) > 1)
                    $color = end($tmpData);

                if (!empty($color) && $color[0] != '#')
                    $color = '#' . $color;

                $images[$i] = array(
                    'img' => $part . $infos['basename'],
                    'color' => $color
                    );
                $i++;
            }
        }

        closedir($dirHandler);

        switch ($type)
        {
            case self::RANDOM:
                $this->_setRandom($images);
                break;
            case self::RANDSTART:
                $this->_setRandStart($images);
                break;
            case self::INCREMENT:
                $this->_setIncrement($images);
                break;

            default:
                break;
        }

    }

    private function _setRandom(array $images)
    {
        $style = '';
        if (count($images) > 0)
        {
            $randBgPosition = rand(1, count($images));

            $image  = $images[$randBgPosition];
            $style  = 'style="background-image:url(\''. $image['img'] .'\');';

            if (!empty($image['color']))
                $style .= ' background-color:' . $image['color'] . ';';

            $style .= '"';

            Zend_Registry::set('bgStyle', $style);
        }
    }

    private function _setRandStart(array $images)
    {
        $style = '';
        if (count($images) > 0)
        {
            Zend_Registry::set('bgStyle', $style);
        }
    }

    private function _setIncrement(array $images)
    {
        $style = '';

        if (count($images) > 0)
        {
            Zend_Registry::set('bgStyle', $style);
        }
    }
}