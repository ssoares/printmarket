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
class Cible_View_Helper_SetThemePath extends Zend_View_Helper_Abstract
{
    protected  $_theme = 'default';

    public function setThemePath($theme = '')
    {
        if (!empty($theme)){
            $this->_theme = $theme;
        }
        $themePath    = "/themes/{$this->_theme}/";
        $tmpName = "themes";
        $tmpTheme = explode('/', $this->_theme);
        if (count($tmpTheme) > 1)
        {
            $tmpName = '/' . $tmpTheme[0] . '/' . $tmpName . '/';
            unset($tmpTheme[0]);
            $themePath = $tmpName . implode('/', $tmpTheme) . '/';
        }
        elseif ($this->_theme !== 'default'){
            $themePath = "/{$this->_theme}/{$tmpName}/";
        }
        
        return $themePath;
    }
}