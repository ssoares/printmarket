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
 * @version    $Id:
 */

/**
 * Allow to retrieve the path for files such as *.css, *.js
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_LocateFile extends Zend_View_Helper_Abstract {

    protected $_theme = 'default';

    /**
     * According parameters, create the path of the files location.
     *
     * @param array  $file  The name of files with extension.
     * @param string $path  <Optional> The path to speficy the location.
     *                      For specific files not in common folders
     *
     * @return string $filePath The path where the file is stored
     */
    public function locateFile($file, $path = null, $force = '', $theme = null, $noBaseUrl = false) {
        if (Zend_Registry::isRegistered('currentTheme')){
            $this->_theme = Zend_Registry::get('currentTheme');
        }
        if (!is_null($theme)){
            $this->_theme = $theme;
        }

        $themePath = $this->view->setThemePath($this->_theme);

        $filePath = $noBaseUrl ? '' : $this->view->BaseUrl();
        $isBackOffice = (preg_match("/extranet/", $filePath));
        switch ($force) {
            case 'front':
                if ($isBackOffice) {
                    $filePath = preg_replace('/\/extranet/', '', $filePath);
                    $isBackOffice = false;
                }
                break;
            case 'back':
                if (!$isBackOffice) {
                    $filePath .= '/extranet';
                    $isBackOffice = true;
                }
                break;

            default:
                break;
        }

        $imgType = array('jpg', 'gif', 'png', 'svg', 'ico');

        if ($file != null) {
            $type = substr($file, strrpos($file, '.') + 1);
            // If the type exists, it's an image file
            if (in_array($type, $imgType)) {
                $type = "img";
                $themePath .= "images";
            }

            // Select the path according type
            switch ($type) {
                case 'img':
                    if (!$isBackOffice) {
                        $imgPath = $themePath . "/common/" ;
                        if (!empty($path)){
                            $imgPath = $themePath . $path . '/';
                            $tmp = Zend_Registry::get('fullDocumentRoot')
                            . $imgPath . $file;
                            if (!is_file($tmp)){
                                $imgPath = $themePath . "/common/" ;
                            }
                        }
                        $filePath .= $imgPath . $file;
                    } else {
                        $imgPath = $themePath . $path . '/';
                        if (!empty($path)){
                            $tmp = Zend_Registry::get('fullDocumentRoot')
                            . $imgPath . $file;
                            if (!is_file($tmp)){
                                $imgPath = $themePath . "/" ;
                            }
                        }
                        $filePath .= $imgPath . $file;
                    }

                    break;
                case 'less':
                case 'css':
                    $isMobile = false;//Zend_Registry::get('isMobile');
                    if(Zend_Registry::isRegistered('isMobile'))
                        $isMobile = Zend_Registry::get('isMobile');
                    $themePath .= $type . '/';
                    $cssPath = (empty($path)) ? $themePath :
                            $themePath . $path . '/';

                    $filePathTmp = $filePath;
                    $filePath .= $cssPath . $file;

                    if ($isMobile) {
                        $filePathTmp .= $cssPath . 'mobile-' . $file;
                        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $filePathTmp)) {
                            $filePath = $filePathTmp;
                        }
                    }

                    break;
                case 'js':
                    $jsRoot = ($path) ? '/' . $type . '/' . $path . '/' :
                            '/' . $type . '/';
                    $filePath .= $jsRoot . $file;
                    break;
                default:
                    $filePath .= '/' . $type . '/' . $file;
                    break;
            }
        }
        $docRoot = rtrim(Zend_Registry::get('fullDocumentRoot'),'/');
        if (preg_match('#' . $this->view->baseUrl() . '#', $filePath)){
            $docRoot = rtrim(Zend_Registry::get('serverDocumentRoot'),'/');
        }
        if (!file_exists($docRoot . $filePath) && $this->_theme !== 'default'){
            $filePath = $this->locateFile($file, $path, $force, 'default', $noBaseUrl);
        }

        return $filePath;
    }

}
