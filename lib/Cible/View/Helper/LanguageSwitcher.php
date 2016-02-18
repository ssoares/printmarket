<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2013 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of LanguageSwitcher
 *
 * @category
 * @package
 * @copyright Copyright (c)2013 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_View_Helper_LanguageSwitcher extends Zend_View_Helper_Abstract
{
    /**
     * List all languages and set the current one as selected and unselectable.
     */
    const ALL = 'all';
    /**
     * Exclude current languagea and list others.
     */
    const EXCLUDE = 'exclude';
    /**
     * Get the current page only.
     */
    const PAGE = 'page';

    const switcherTemplate = '<ul class="##CLASSMENU##">##CONTENT##</ul>';
    const separTemplate = '<li class="verticalSeparator"> ##SEPARATOR## </li>';
    const separKey = '##SEPARATOR##';
    const classKey = '##CLASSMENU##';
    const contentKey = '##CONTENT##';
    const hrefKey = '##HREF##';

    protected $_separator = '|';
    protected $_addFirstSeparator = false;
    protected $_class = 'languageTop nav navbar-nav';
    protected $_fontClass = '';
    protected $_type = self::EXCLUDE;
    protected $_message = '';
    protected $_domain = '';
    protected $_config = '';
    protected $_use_suffix = false;

    public function setProperties(array $options)
    {
        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }
    }

    public function languageSwitcher(array $options = array())
    {
        $this->setProperties($options);
        $this->_config = Zend_Registry::get('config');
        $content = '';
        $first = true;
        $numberOfLanguageNow = 0;

        $currentLang = Zend_Registry::get('languageID');
        $languages = Cible_FunctionsGeneral::getAllLanguage();
        $currentPageId = Zend_Registry::get('pageID');
        $numberOfLanguage = sizeof($languages);

        foreach ($languages as $lang)
        {
            $class = 'class="';
            $numberOfLanguageNow++;
            if ($first)
            {
                $first = false;
                $class .= 'first';
            }
            else
                $class .= '';

            if ($numberOfLanguageNow == count($languages))
                $class .= 'last';

            $contentTmp = "";
            $classTmp = "";
            if($this->_use_suffix)
                $textTruncate = $lang['L_Suffix'];
            else
                $textTruncate = $lang['L_Title'];
            if ($lang['L_ID'] != $currentLang)
            {
                if (count($this->_config->site->domainsName) > 1){
                    $protocol = Zend_Registry::get('protocol');
                    $this->_domain = $protocol . $this->_config->site->domainsName->$lang['L_ID'];
                }
                $localizedPage = Cible_FunctionsPages::getPageNameByID($currentPageId, $lang['L_ID']);
//                $localizedAction = Cible_FunctionsPages::getActionNameByLang(Zend_Registry::get('currentUrlAction'), $lang['L_ID']);
                $localizedAction = '';
                $url = empty($localizedAction) ? $this->view->url(array('controller' => $localizedPage)) : $this->view->url(array('controller' => $localizedPage, 'action' => $localizedAction));

                if ($localizedPage)
                {
                    $module = '';
                    if (Zend_Registry::isRegistered('module') && !is_null(Zend_Registry::get('module')))
                    {
                        $module = Zend_Registry::get('module');
                        $url = $this->view->action('langswitch','index', $module, array('lang' => $lang['L_ID'], 'url' => $url));
                    }
                    // Code to optimize, works only for one language in the loop
                    if ($this->_type === self::PAGE)
                        return $this->_setUrlPage($url);

                    $url = $this->_domain . $url;
                    $contentTmp .= $this->view->link($url, $textTruncate, array('class' => $this->_fontClass));
                }
                elseif ($this->_type === self::ALL)
                {
                    $contentTmp .= "<span class=''> $textTruncate</span>";
                    $classTmp .= " language-unavailable";
                }
            }
            elseif ($this->_type === self::ALL)
            {
                $contentTmp .= "<span class=''>$textTruncate</span>";
                $classTmp .= " language-unavailable";
            }
            $class .= $classTmp . '"';
            if (!empty($contentTmp))
            {
                $content .= "<li $class>";
                $content .= $contentTmp;
                $content .= '</li>';
            }
            if ($numberOfLanguageNow < $numberOfLanguage && $this->_type === self::ALL)
                $content .= str_replace(self::separKey, $this->_separator, self::separTemplate);
        }
        if ($this->_addFirstSeparator && !empty($content))
            $content = str_replace(self::separKey, $this->_separator, self::separTemplate) . $content;

        return str_replace(array(self::classKey, self::contentKey), array($this->_class, $content), self::switcherTemplate);
    }

    private function _setUrlPage($url)
    {
        if (!empty($this->_message))
            $msg = str_replace(self::hrefKey, $url, $this->_message);
        else
            $msg = $url;

        return $msg;
    }

}
