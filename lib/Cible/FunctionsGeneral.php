<?php
/**
 * Cible
 *
 * @category   Cible
 * @package    Cible
 * @subpackage Cible
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id: FunctionsGeneral.php 1842 2016-01-14 15:21:52Z ssoares $
 */

/**
 * Offers various tools
 *
 * @category   Cible
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
abstract class Cible_FunctionsGeneral
{
    /**
     * Date written in its entire string form. <br />
     * ex: Le 31 décembre 2010 (FR) or Monday, December 30th, 2013 (EN)
     */
    const DATE_FULL = 'DF';
    /**
     * Date written as a short string <br/>
     * ex : 31 décembre 2010
     */
    const DATE_LONG = 'DL';
    /**
     * The same as DATE_FULL but month is shortened. <br/>
     * ex: 10 déc. 2013
     */
    const DATE_SHORT = 'DS';
    /**
     * Date format like : décembre 2010
     */
    const DATE_LONG_NO_DAY = 'DLND';
    /**
     * Date format like : 20 juin
     */
    const DATE_DAY_MONTH = 'DDM';
    /**
     * Date format like : 20 sept.
     */
    const DATE_DAY_MONTH_SHORT = 'DDMS';
    /**
     * Date format : day/month/year (Separator can be changed)
     */
    const DATE_NUM = 'DN';
    /**
     * Date format : year-month-day (separator is still the same -)
     */
    const DATE_SQL = 'DSQL';
    /**
     * Date in US format : month/day/year (Separator can be changed)
     */
    const DATE_NUM_USA = 'DNUSA';
    /**
     *
     * Date format with two digits for the year. <br/>
     * ex: 31/12/13 (Separator can be changed)
     */
    const DATE_NUM_SHORT_YEAR = 'DNSY';
    /**
     * The same as DATE_LONG_NO_DAY but with the first letter in uppercase.
     * ex : December 2013
     */
    const DATE_MONTH_YEAR = 'DMY';
    /**
     * The same as DATE_LONG_NO_DAY but with the first letter in uppercase.
     * ex : Dec. 2013
     */
    const DATE_SHORT_MONTH_YEAR = 'SDMY';
    /**
     * Day name and day number
     * ex : Mardi 21
     */
    const DATE_DAY_DAY = 'DD';

    public static function log($message, $trace = array(), $priority = Zend_Log::DEBUG)
    {
        if (Zend_Registry::isRegistered('LOG')){
            $oLog = Zend_Registry::get('LOG');
            $cfg = Zend_Registry::get('config');
            $traceLength = isset($cfg->log->traceLength) ? $cfg->log->traceLength : 0;
            if (!empty($trace))
            {
                for($i = 0; $i < $traceLength; $i++)
                {
                        $row = $trace[$i];
                        if (!empty($row)){
                            if ($i == 0){
                            $message .= ' : log from ';
                            $message .= !empty($row['function'])? $row['function'] : '';
                            }
                        $message .= PHP_EOL;
                        $message .= "              ";
                        $message .= !empty($row['line'])?'Called on line ' . $row['line'] : '' ;
                        $message .= !empty($trace[$i+1]['function'])?' in ' . $trace[$i+1]['function'] . ' ' : '';
                        if (!empty($row['file'])){
                            $message .= 'from ' . str_replace(Zend_Registry::get('serverDocumentRoot'), '', $row['file']);
                        }
                    }
                }
            }
            $oLog->log($message, $priority);
        }
    }

        public static function getAllLanguage($onlyActive = true)
    {
        $Languages = Zend_Registry::get("db");
        $Select = $Languages->select()
            ->from('Languages')
            ->order('L_Seq');

        if ($onlyActive)
            $Select->where('L_Active = ?', 1);

        return $Languages->fetchAll($Select);
    }

    public static function getFilterLanguages()
    {
        $languages = self::getAllLanguage();
        $choices = array('' => Cible_Translation::getCibleText('filter_empty_language'));

        foreach ($languages as $language)
        {
            if (!isset($choices[$language['L_ID']]))
            {
                $choices[$language['L_ID']] = $language['L_Title'];
            }
        }

        return $choices;
    }

    public static function extranetLanguageIsAvailable($langID)
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchOne('SELECT L_ID FROM Languages WHERE L_ID = ? AND L_ExtranetUI = \'1\'', $langID);
    }

    public static function languageIsAvailable($langID)
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchOne('SELECT L_ID FROM Languages WHERE L_ID = ? AND L_Active = \'1\'', $langID);
    }

    public static function removeAccents($string)
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        $string = str_replace($a, $b, $string);

        return $string;
    }

    public static function getExtranetLanguage()
    {
        $Languages = Zend_Registry::get("db");
        $Select = $Languages->select()
                        ->from('Languages')
                        ->where('L_ExtranetUI = ?', 1);

        return $Languages->fetchAll($Select);
    }

    public static function getLanguageID($suffix)
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchOne('SELECT L_ID FROM Languages WHERE L_Suffix = ?', $suffix);
    }

    public static function getLanguageSuffix($id)
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchOne('SELECT L_Suffix FROM Languages WHERE L_ID = ?', $id);
    }

    public static function getLanguageTitle($id)
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchOne('SELECT L_Title FROM Languages WHERE L_ID = ?', $id);
    }

    public static function getStatus()
    {
        $_db = Zend_Registry::get("db");

        return $_db->fetchAll('SELECT * FROM Status');
    }

    public static function getStatusCode($statusId, $status = null)
    {
        if ($status == null)
            $status = getStatus();

        foreach ($status as $_s)
        {
            if ($_s['S_ID'] == $statusId)
                return $_s['S_Code'];
        }

        throw new Exception('Status not found Exception');
    }

    public static function generateLanguageSwitcher($view)
    {
        $_availableLanguages = Cible_FunctionsGeneral::getAllLanguage();

        $baseUrl = $view->baseUrl();
        $params = $view->params;

        $_module = '';
        $_controller = '';
        $_action = '';
        $_params = '';

        foreach ($params as $_key => $_val)
        {
            switch ($_key)
            {
                case 'module':
                    $_module = $_val;
                    break;
                case 'controller':
                    $_controller = $_val;
                    break;
                case 'action':
                    $_action = $_val;
                    break;
                default:
                    if (strtolower($_key) != 'lang' && !isset($_POST[$_key]))
                        $_params .= "/$_key/$_val";
            }
        }

        $_requestURI = "$baseUrl/$_module/$_controller/$_action$_params";

        $content = '';

        foreach ($_availableLanguages as $_lang)
        {
            $_selected = false;

            if ($_lang['L_ID'] == Zend_Registry::get('currentEditLanguage'))
                $_selected = true;

            $content .= '<li>';
            if ($_selected)
            {
                $content .= $view->link("$_requestURI/lang/{$_lang['L_Suffix']}", $_lang['L_Title'], array('class' => 'selected'));
            }
            else
            {
                $content .= $view->link("$_requestURI/lang/{$_lang['L_Suffix']}", $_lang['L_Title']);
            }
            $content .= '</li>';
        }

        if (!empty($content))
        {
            $content = "<ul id='language-switcher'>$content</ul>";
        }

        return $content;
    }

    public static function generateHtmlTableV2($searchArray = "", $listArray = "", $navigationArray = "")
    {
        // build the search
        $searchTable = "";
        if ($searchArray <> "")
        {
            $searchTable = Cible_FunctionsGeneral::generateHtmlTableSearch($searchArray);
        }

        // build the list
        $listTable = "";
        if ($listArray <> "")
        {
            $listTable = Cible_FunctionsGeneral::generateHTMLTableList($listArray);
        }

        // build the navigation
        $navigationTable = "";
        if ($navigationArray <> "")
        {
            $navigationTable = Cible_FunctionsGeneral::generateHtmlTableNavigation($navigationArray);
        }

        return ($searchTable . "\n" . $listTable . "\n" . $navigationTable);
    }

    public static function generateHTMLTableList($listArray)
    {
        $listTable = "";

        // table list start
        $listTable .= " <table class='default_html_table'>";

        // caption
        if (isset($listArray['caption']))
            $listTable .= " <caption>" . $listArray['caption'] . "</caption>";

        // head
        if (isset($listArray['thArray']))
        {
            $listTable .= "     <thead>";
            $listTable .= "         <tr>";
            foreach ($listArray['thArray'] as $TH)
            {
                $listTable .= "         <th>";
                if (array_key_exists("OrderField", $TH) && array_key_exists("Order", $TH))
                {
                    $listTable .= '<div style="float:left;">' . $TH["Title"] . '</div>';
                    $listTable .= '<div class="listTableOrder"><a href="' . $TH["OrderLink"] . '"><img class="action_icon" src="' . Zend_Controller_Front::getInstance()->getBaseUrl() . '/icons/order_' . $TH["Order"] . '_icon.gif" /></a></div>';
                }
                else
                {
                    $listTable .= $TH['Title'];
                }
                $listTable .= "         </th>";
            }
            $listTable .= "         </tr>";
            $listTable .= "     </thead>";
        }

        // rows
        $listTable .= "     <tbody>";
        $rowColor = "rowPair";
        foreach ($listArray['rowsArray'] as $rows)
        {
            if ($rowColor == "rowPair")
                $rowColor = "rowOdd";
            else
                $rowColor = "rowPair";

            $listTable .= '<tr class="' . $rowColor . '">';
            foreach ($rows as $details)
            {
                $listTable .= '<td>' . $details . "</td>";
            }
            $listTable .= "</tr>";
        }


        // table list end
        $listTable .= " </table>";

        return $listTable;
    }

    public static function generateHtmlTable($tableTitle, $tableTH, $tableRows, $tableNavigation = "", $tableSearch = "")
    {

        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $table = "";
        $table .= $tableSearch;
        $table .= "<table class='default_html_table'>";
        $table .= "     <caption>" . $tableTitle . "</caption>";
        // TABLE HEADER
        $table .= "     <thead>";
        $table .= "         <tr>";
        foreach ($tableTH as $TH)
        {
            $table .= "<th>";
            if (array_key_exists("OrderField", $TH) && array_key_exists("Order", $TH))
            {
                $table .= '<div style="float:left;">' . $TH["Title"] . '</div>';
                $table .= '<div class="listTableOrder"><a href="' . $TH["OrderLink"] . '"><img class="action_icon" src="' . $_baseUrl . '/icons/order_' . $TH["Order"] . '_icon.gif" /></a></div>';
            }
            else
            {
                $table .= $TH["Title"];
            }
            $table .= "</th>";
        }
        $table .= "         </tr>";
        $table .= "     </thead>";

        // TABLE ROWS
        $table .= "     <tbody>";
        $rowColor = "rowPair";
        foreach ($tableRows as $rows)
        {
            if ($rowColor == "rowPair")
                $rowColor = "rowOdd";
            else
                $rowColor = "rowPair";

            $table .= '<tr class="' . $rowColor . '">';
            foreach ($rows as $details)
            {
                $table .= '<td>' . $details . "</td>";
            }
            $table .= "</tr>";
        }
        if ($tableNavigation <> "")
        {
            $table .= "<tr>";
            $table .= "<td colspan=" . count($tableTH) . ">";
            $table .= $tableNavigation;
            $table .= "</td>";
            $table .= "</tr>";
        }
        $table .= "     </tbody>";

        $table .= " </table>";

        return $table;
    }

    public static function generateHtmlTableSearch($searchArray)
    {
        $tableSearch = "<table class=\"tableSearch\">";
        $tableSearch .= "     <tr valign=\"top\">";
        $tableSearch .= "         <td>";
        $tableSearch .= "             <b>Mots-clés</b><br/>";
        $tableSearch .= "             <input id=\"searchText\" name=\"searchText\" type='text' class='stdTextInput'/><br/>";
        $tableSearch .= "             <input type=\"button\" onclick=\"location.href = '" . $searchArray['searchLink'] . "/search/'+escape(document.getElementById('searchText').value)\" value=\"Rechercher\"/>";
        $tableSearch .= "             <input type=\"button\" onclick=\"location.href = '" . $searchArray['searchLink'] . "'\" value=\"Liste complète\"/>";
        $tableSearch .= "         </td>";
        $tableSearch .= "         <td>";
        if ($searchArray["searchText"] <> "")
        {
            $tableSearch .= "         <b>Résultats de recherche</b><br/><br/>";
            $tableSearch .= "         Mots-clés : <b>" . $searchArray['searchText'] . "</b><br/>";
            $tableSearch .= "         Nombre trouvé : <b>" . $searchArray['searchCount'] . "</b>";
        }
        else
        {
            $tableSearch .= "         <b>Liste complète</b><br/><br/>";
            $tableSearch .= "         Nombre trouvé : <b>" . $searchArray['searchCount'] . "</b>";
        }
        $tableSearch .= "         </td>";
        $tableSearch .= "     </tr>";
        $tableSearch .= "</table>";

        return $tableSearch;
    }

    public static function generateHtmlTableNavigation($navigationArray)
    {
        $tablePage = $navigationArray["tablePage"];
        $navigationLink = $navigationArray["navigationLink"];
        $nbTablePage = $navigationArray["nbTablePage"];

        $tableNavigation = "";

        if ($tablePage > 1)
            $tableNavigation .= "<a href='" . $navigationLink . "/tablePage/" . ($tablePage - 1) . "'>Précédent</a>&nbsp;&nbsp;";

        for ($i = 1; $i <= $nbTablePage; $i++)
        {
            if ($i == $tablePage)
            {
                $tableNavigation .= $i . "&nbsp;&nbsp;";
            }
            else
            {
                $tableNavigation .= "<a href='" . $navigationLink . "/tablePage/" . $i . "'>" . $i . "</a>&nbsp;&nbsp;";
            }
        }

        if ($tablePage <> $nbTablePage)
            $tableNavigation .= "<a href='" . $navigationLink . "/tablePage/" . ($tablePage + 1) . "'>Suivant</a>";

        return $tableNavigation;
    }

    public static function getAllChildCategory($moduleID, $categoryParentID, $languageID)
    {
        $categories = new Categories();
        $select = $categories->select()->setIntegrityCheck(false)
                        ->form('Categories')
                        ->join('CategoriesIndex', 'C_ID = CI_CategoryID')
                        ->where('C_ModuleID = ?', $moduleID)
                        ->where('C_ParentID = ?', $categoryParentID)
                        ->where('CI_LanguageID = ?', $languageID)
                        ->order('CI_Title');

        return $categories->fetchAll($select);
    }

    public static function getCategoryDetails($categoryID)
    {
        $category = new Categories();
        $select = $category->select()->setIntegrityCheck(false)
                        ->from('Categories')
                        ->join('CategoriesIndex', 'CI_CategoryID = C_ID')
                        ->where('C_ID = ?', $categoryID)
                        ->where('CI_LanguageID = ?', Zend_Registry::get('languageID'));

        return $category->fetchRow($select);
    }

    public static function getCustomerDetails($customerID)
    {
        $dbGestionCible = Zend_Registry::get('dbGestionCible');
        $select = $dbGestionCible->select()
                        ->from('Cible_Registre')
                        ->where('R_ID = ?', $customerID);
        return $dbGestionCible->fetchRow($select);
    }

    public static function getFirstCategoryParent($categoryID)
    {
        $category = new Categories();
        $select = $category->select()
                        ->where('C_ID = ?', $categoryID);

        $categoryData = $category->fetchRow($select);

        if ($categoryData['C_ParentID'] == 0)
        {
            return $categoryData->toArray();
        }
        else
        {
            return Cible_FunctionsGeneral::getFirstCategoryParent($categoryData['C_ParentID']);
        }
    }

    public static function getCategoryParent($categoryID)
    {
        $category = new Categories();
        $select = $category->select()
                        ->where('C_ID = ?', $categoryID);

        $categoryData = $category->fetchRow($select);

        return $categoryData->toArray();
    }

    public static function fillStatusSelectBox($selectBox, $table, $field)
    {
        $db = Zend_Registry::get("db");

        $sql = "SHOW COLUMNS FROM $table LIKE '$field'";
        $result = $db->fetchAll($sql);

        // fill the status select box
        $StatusArray = "";
        if (count($result) > 0)
        {
            $StatusArray = explode("','", preg_replace("/(enum|set)\('(.+?)'\)/", "\\2", $result[0]["Type"]));
        }
        $NbStatus = count($StatusArray);

        // Sort le Array pour mettre le texte "Actif" avant "Inactif"
        sort($StatusArray);

        foreach ($StatusArray as $Status)
        {
            $selectBox->addMultiOption($Status, Cible_Translation::getCibleText("form_extranet_group_status_$Status"));
        }

        return $selectBox;
    }

    public static function html2text($text)
    {
        $search = array(
            "/\r/", // Non-legal carriage return
            "/[\n\t]+/", // Newlines and tabs
            '/[ ]{2,}/', // Runs of spaces, pre-handling
            '/<script[^>]*>.*?<\/script>/i', // <script>s -- which strip_tags supposedly has problems with
            '/<style[^>]*>.*?<\/style>/i', // <style>s -- which strip_tags supposedly has problems with
            //'/<!-- .* -->/',                         // Comments -- which strip_tags might have problem a with
            '/<h[123][^>]*>(.*?)<\/h[123]>/ie', // H1 - H3
            '/<h[456][^>]*>(.*?)<\/h[456]>/ie', // H4 - H6
            '/<p[^>]*>/i', // <P>
            '/<br[^>]*>/i', // <br>
            '/<b[^>]*>(.*?)<\/b>/ie', // <b>
            '/<strong[^>]*>(.*?)<\/strong>/ie', // <strong>
            '/<i[^>]*>(.*?)<\/i>/i', // <i>
            '/<em[^>]*>(.*?)<\/em>/i', // <em>
            '/(<ul[^>]*>|<\/ul>)/i', // <ul> and </ul>
            '/(<ol[^>]*>|<\/ol>)/i', // <ol> and </ol>
            '/<li[^>]*>(.*?)<\/li>/i', // <li> and </li>
            '/<li[^>]*>/i', // <li>
            '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/ie',
            // <a href="">
            '/<hr[^>]*>/i', // <hr>
            '/(<table[^>]*>|<\/table>)/i', // <table> and </table>
            '/(<tr[^>]*>|<\/tr>)/i', // <tr> and </tr>
            '/<td[^>]*>(.*?)<\/td>/i', // <td> and </td>
            '/<th[^>]*>(.*?)<\/th>/ie', // <th> and </th>
            '/&(nbsp|#160);/i', // Non-breaking space
            '/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i',
            // Double quotes
            '/&(apos|rsquo|lsquo|#8216|#8217|#146|#39);/i', // Single quotes
            '/&gt;/i', // Greater-than
            '/&lt;/i', // Less-than
            '/&(amp|#38);/i', // Ampersand
            '/&(copy|#169);/i', // Copyright
            '/&(trade|#8482|#153);/i', // Trademark
            '/&(reg|#174);/i', // Registered
            '/&(mdash|#151|#8212);/i', // mdash
            '/&(ndash|minus|#8211|#8722);/i', // ndash
            '/&(bull|#149|#8226);/i', // Bullet
            '/&(pound|#163);/i', // Pound sign
            '/&(euro|#8364);/i', // Euro sign
            '/(«|&laquo;)/i', // « sign
            '/(»|&raquo;)/i', // » sign
            //'/&[^&;]+;/i',                         // Unknown/unhandled entities
            '/[ ]{2,}/', // Runs of spaces, post-handling
        );

        $replace = array(
            '', // Non-legal carriage return
            ' ', // Newlines and tabs
            ' ', // Runs of spaces, pre-handling
            '', // <script>s -- which strip_tags supposedly has problems with
            '', // <style>s -- which strip_tags supposedly has problems with
            //'',                                     // Comments -- which strip_tags might have problem a with
            "strtoupper(\"\n\n\\1\n\n\")", // H1 - H3
            "ucwords(\"\n\n\\1\n\n\")", // H4 - H6
            "\n\n\t", // <P>
            "\n", // <br>
            'strtoupper("\\1")', // <b>
            'strtoupper("\\1")', // <strong>
            '_\\1_', // <i>
            '_\\1_', // <em>
            "\n\n", // <ul> and </ul>
            "\n\n", // <ol> and </ol>
            "\t* \\1\n", // <li> and </li>
            "\n\t* ", // <li>
            '"\\2"', // <a href="">
            "\n-------------------------\n", // <hr>
            "\n\n", // <table> and </table>
            "\n", // <tr> and </tr>
            "\t\t\\1\n", // <td> and </td>
            "strtoupper(\"\t\t\\1\n\")", // <th> and </th>
            ' ', // Non-breaking space
            '"', // Double quotes
            "'", // Single quotes
            '>',
            '<',
            '&',
            '(c)',
            '(tm)',
            '(R)',
            '--',
            '-',
            '*',
            '£',
            'EUR', // Euro sign.  ?
            '"', // « sign.
            '"', // » sign.
            //'',                                     // Unknown/unhandled entities
            ' ', // Runs of spaces, post-handling
        );

        $text = trim(stripslashes($text));
        $text = strip_tags($text);
        $text = preg_replace($search, $replace, $text);
        //$text = htmlentities($text);
        return($text);
    }

    public static function stripTextWords($text, $wordCount = 25)
    {
        $text = Cible_FunctionsGeneral::html2text($text);
        $totalWordCount = str_word_count($text);

        if ($wordCount > $totalWordCount)
            $wordCount = $totalWordCount;

        // Get the X first word
        $text_tmp = explode(' ', $text);
        $text_tmp = implode(' ', array_slice($text_tmp, 0, $wordCount));

        if ($wordCount < $totalWordCount && substr($text_tmp, strlen($text_tmp), 1) <> '.')
        {
            $text = substr($text, strlen($text_tmp), strlen($text));
            if (strpos($text, '.'))
                $text = substr($text, 0, strpos($text, '.') + 1);
            $text = $text_tmp . $text;
        }
        else
        {
            $text = $text_tmp;
        }

        return $text;
    }

    /**
     * Truncate a string to the max number of characters given and allows to
     * associate a class for the three final dots.
     *
     * @param string $string The string to truncate
     * @param int    $max    <OPTIONAL> Default = 150. Limit to cut the string.
     * @param array  $option <OPTIONAL> Array for parameters (ie class attrib).
     *
     * @return string $string The formatted string
     */
    public static function truncateString($string, $max = 150, $option = array())
    {
        $string = Cible_FunctionsGeneral::html2text($string);

        if (strlen($string) > $max)
        {
            $string = substr($string, 0, $max);
            $i      = strrpos($string, " ");
            $string = substr($string, 0, $i);
            $dot    = "...";

            if (!empty($option["dotStyle"]))
            {
                $dot = "<span class='" . $option["dotStyle"] . "'>...</span>";
            }
            $string = $string . $dot;
        }

        return $string;
    }

    public static function delFolder($dir)
    {
        if( substr( $dir, -1 ) != '/' )
            $dir .= '/';
        $files = glob($dir . '*', GLOB_MARK);

        foreach ($files as $file)
        {
            if (substr($file, -1) == '/')
                Cible_FunctionsGeneral::delFolder($file);
            else
            {
                if (file_exists($file))
                    var_dump ($file);
                    unlink($file);
            }
        }
        if(file_exists($dir)){
            rmdir($dir);
        }
    }

    public static function getApprobationRequest($moduleName)
    {
        $_db = Zend_Registry::get("db");

        switch ($moduleName)
        {
            case 'text':
                $query = $_db->quoteInto('SELECT COUNT(*) FROM TextData WHERE TD_ToApprove = ?', 1);
                break;

            default:;
        }
        $count = $_db->fetchOne($query);

        if ($count > 0)
            $boldAttribute = ' font-weight:bold';
        else
            $boldAttribute = '';

        return " <span style='font-family:Arial; font-size:12px; $boldAttribute'>($count)</span>";
    }

    public static function generatePassword()
    {

        $password = '';
        $pw_length = 8;
        // set ASCII range for random character generation
        $lower_ascii_bound = 50;          // "2"
        $upper_ascii_bound = 122;       // "z"
        // Exclude special characters and some confusing alphanumerics
        // o,O,0,I,1,l etc
        $notuse = array(58, 59, 60, 61, 62, 63, 64, 73, 79, 91, 92, 93, 94, 95, 96, 108, 111);
        $i = 0;
        while ($i < $pw_length)
        {
            mt_srand((double) microtime() * 1000000);
            // random limits within ASCII table
            $randnum = mt_rand($lower_ascii_bound, $upper_ascii_bound);
            if (!in_array($randnum, $notuse))
            {
                $password = $password . chr($randnum);
                $i++;
            }
        }

        return $password;
    }

    public static function getRssCategories($lang = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        //$select = $db->select();

        $category = new Categories();
        $select = $category->select()->setIntegrityCheck(false)
                        ->from('Categories')
                        ->join('CategoriesIndex', 'CI_CategoryID = C_ID')
                        ->where('C_ShowInRss = 1')
                        ->where('CI_LanguageID = ?', $lang);

        $categoryData = $category->fetchAll($select);

        return $categoryData->toArray();
    }

    /**
     * Get data if an user is already authenticated
     *
     * @return string $authentication user's data from cookie
     */
    public static function getAuthentication()
    {
        $authentication = null;

        if (isset($_COOKIE['authentication']))
        {
            $authentication = json_decode($_COOKIE['authentication'], true);
            $path = Zend_Registry::get('web_root');

            $profile = new GenericProfilesObject();
            $foundUser = $profile->findData(array(
                        'GP_Email' => $authentication['email'],
                        'GP_Hash' => $authentication['hash'],
                        'GP_Status' => $authentication['status'],
                        'GP_Deleted' => 0
                    ));

            if (empty($foundUser)){
                $authentication = null;
                setcookie('authentication', '', -1, $path);
            }
        }

        return $authentication;
    }

    /**
     * Get users data if he has got n account and if his password is ok.
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public static function authenticate($email, $password)
    {
        $pwd = md5($password);
        $profile = new GenericProfilesObject();
        $filters = array('GP_Email' => $email, 'GP_Password' => $pwd,
            'GP_Deleted' => 0);
        $tmp = $profile->findData($filters);
        $foundUser = isset($tmp[0]) ? $tmp[0] : array();
        if (isset($foundUser['GP_MemberID'])){
            return array(
                'success' => 'true',
                'member_id' => $foundUser['GP_MemberID'],
                'email' => $foundUser['GP_Email'],
                'lastName' => $foundUser['GP_LastName'],
                'firstName' => $foundUser['GP_FirstName'],
                'status' => $foundUser['GP_Status']

            );
        }else{
            return array('success' => 'false');
        }
    }

    /**
     * Search for a user account and return his data.
     *
     * @param int    $memberId    The member's id
     * @param string $email       The user email
     * @param int    $accountType The account type
     *
     * @return array $foundUser
     */
    public static function isAuthenticated($memberId, $email, $accountType)
    {
        $profile = new GenericProfilesObject();
        $foundUser = $profile->findData(array('GP_MemberID' => $memberId,
            'GP_Email' => $email));

        return $foundUser[0];
    }

    /**
     * Get a list of all the countries
     *
     * @param int $lang  The language id
     * @param int $ctyId The country id
     *
     * @return array
     */
    public static function getCountries($lang = null, $ctyId = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('Countries', array('ID' => 'C_ID', 'value' => 'C_Identifier'))
                ->joinLeft('CountriesIndex', 'Countries.C_ID = CountriesIndex.CI_CountryID', array('name' => 'CountriesIndex.CI_Name'))
                ->where('CountriesIndex.CI_LanguageID = ?', $lang);

        if ($ctyId)
        {
            if (is_numeric($ctyId))
                $select->where('C_ID = ?', $ctyId);
            else
                $select->where('C_Identifier = ?', $ctyId);
        }

        $countries = $db->fetchAll($select);
        $result = array();

        foreach ($countries as $country)
        {
            if ($ctyId)
            {
                $result = array(
                    'ID' => $country['ID'],
                    'value' => $country['value'],
                    'name' => $country['name']);
            }
            else
            {
            array_push($result, array(
                'ID' => $country['ID'],
                'value' => $country['value'],
                'name' => $country['name']
            ));
        }
        }

        return $result;
    }

    public static function getCountryByStateID($stateID)
    {
        $db = Zend_Registry::get("db");
        $lang = Zend_Registry::get('languageID');

        $select = $db->select();
        $select->from('States', array("ID" => "S_CountryID"))
                ->where('S_ID = ?', $stateID);

        return $db->fetchRow($select);
    }

    public static function getCountryByCode($code = null, $lang = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('Countries', array())
                ->joinLeft('CountriesIndex', 'Countries.C_ID = CountriesIndex.CI_CountryID', array('name' => 'CI_Name'))
                ->where('CountriesIndex.PI_LanguageID = ?', $lang)
                ->where('Countries.P_Identifier = ?', $code);

        return $db->fetchOne($select);
    }

    /**
     * Fetch salutation identifier and retrieve text fron static texts table.
     *
     * @param int $id   <OPTIONAL> If null then all the salutations will be returned.
     * @param int $lang <OPTIONAL> If null then the language id will be setted.
     *
     * @return array
     */
    public static function getSalutations($id = null, $lang = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('Salutations', array('ID' => 'S_ID', 'value' => 'S_StaticTitle'));

        if ($id)
            $select->where('Salutations.S_ID = ?', $id);

        $salutations = $db->fetchAll($select);

        $result = array();

        foreach ($salutations as $salutation)
        {
            $result[$salutation['ID']] = Cible_Translation::getCibleText($salutation['value'], $lang);
        }

        return $result;
    }

    /**
     * Fetch states data according to the country code or the state code.
     *
     * @param int|string $countryCode Country id (int) or country identifier
     *                                (2 caracters eq CA for Canada)
     * @param int|string $stateCode   State Id (int) or state identifier (2 car.
     *                                eq QC for Quebec)
     * @param int        $lang        Id of the current user language. If no
     *                                language given, set the default language.
     * @return array
     */
    public static function getStateByCode($countryCode = null, $stateCode = null, $lang = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('States', array('id' => 'S_ID'))
                ->joinLeft(
                    'StatesIndex',
                    'States.S_ID = StatesIndex.SI_StateID',
                    array('name' => 'SI_Name'))
                ->joinLeft('Countries', 'States.S_CountryID = Countries.C_ID', array())
                ->where('StatesIndex.SI_LanguageID = ?', $lang);

        if (is_numeric($countryCode) && !is_null($countryCode))
            $select->where('States.S_CountryID = ?', $countryCode);
        elseif (!is_null($countryCode))
            $select->where('Countries.C_Identifier = ?', $countryCode);

        if (is_numeric($stateCode) && !is_null($stateCode)){
            $select->where('States.S_ID = ?', $stateCode);
            return $db->fetchOne($select);
        }elseif (!is_null($stateCode)){
            $select->where('States.S_Identifier = ?', $stateCode);
            return $db->fetchOne($select);
        }

        return $db->fetchAll($select);

    }

    public static function getStates($lang = null, $stateId = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('States', array('ID' => 'States.S_ID','value' => 'S_Identifier', 'ctyId' => 'States.S_CountryID'))
                ->joinLeft('StatesIndex', 'States.S_ID = StatesIndex.SI_StateID', array('name' => 'SI_Name'))
                ->joinLeft('Countries', 'States.S_CountryID = Countries.C_ID', array('code' => 'C_Identifier'))
                ->where('StatesIndex.SI_LanguageID = ?', $lang)
                ->order('SI_Name');

        if ($stateId)
        {
            if (is_numeric($stateId))
                $select->where('States.S_ID = ?', $stateId);
            else
                $select->where('States.S_Identifier = ?', $stateId);
        }
        $states = $db->fetchAll($select);

        $result = array();

        foreach ($states as $state)
        {
            if (!isset($result[$state['ctyId']]))
                $result[$state['ctyId']] = array();

            if ($stateId)
            {
                $result = array(
                'value' => $state['value'],
                'name' => $state['name']
                );
            }
            else
            {

                array_push(
                        $result[$state['ctyId']],
                        array(
                            'id' => $state['ID'],
                            'value' => $state['value'],
                            'name' => $state['name']
            ));
        }
        }

        return $result;
    }

    public static function getStatesByCountry($countryID)
    {
        $db = Zend_Registry::get("db");
        $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from('States', array('ID' => 'S_ID'))
                ->joinLeft('StatesIndex', 'States.S_ID = StatesIndex.SI_StateID', array('Name' => 'SI_Name'))
                ->where('StatesIndex.SI_LanguageID = ?', $lang)
                ->where('S_CountryID = ?', $countryID)
                ->order('SI_Name');

        return $db->fetchAll($select);
    }

    /**
     * Fetches cities data.
     *
     * @param int $lang    Id of the current language to display.
     * @param int $cityId  Id to fecth data only for this city.
     * @param int $stateId Id to fecth data only for this state.
     *
     * @return array
     */
    public static function getCities($lang = null, $cityId = null, $stateId = null)
    {
        $db = Zend_Registry::get("db");

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $select = $db->select();

        $select->from(
            'Cities',
            array(
                'id'    => 'C_ID',
                'value' => 'c_Name',
                'name'  => 'C_Name',
                'code'  => 'C_Name')
            )
            ->order('C_Name');

        if ($cityId)
            $select->where('C_ID = ?', $cityId);

        if ($stateId)
            $select->where('C_StateID = ?', $stateId);

        $states = $db->fetchAll($select);
        $result = array();
        if ($stateId)
            $result = $states;
        else
        {
        foreach ($states as $state)
        {
            if (!isset($result[$state['code']]))
                $result[$state['code']] = array();
                if ($cityId)
                {
                    $result = array(
                        'value' => $state['value'],
                        'name' => $state['name']);
                }
                else
                {
            array_push($result[$state['code']], array(
                'value' => $state['value'],
                'name' => $state['name']
            ));
        }
            }
        }
        return $result;
    }

    /**
     * Retrieve the registred users with managers privileges.
     *
     * @return array
     */
    public static function getClientWithManagerPrivileges()
    {
        $db = Zend_Registry::get("db");

        $profile = new MemberProfile();

        $select = $profile->getSelectStatement();

//        $select->where('MP_IsDetaillant = ?', 1);
        $select->order('company');
        $select->order('lastName');
        $select->order('firstName');

        return $db->fetchAll($select);
    }

    public static function getClientStaticText($identifier, $lang = null)
    {

        if (is_null($lang))
            $lang = Zend_Registry::get('languageID');

        $staticText = new StaticTexts();
        $select = $staticText->select()
                        ->where("ST_Identifier = ?", $identifier)
                        ->where("ST_LangID = ?", $lang);
        //die($select);
        return $staticText->fetchRow($select);
    }

    /**
     * Set the css class name to chamge color when user switch language.
     *
     * @param array $options
     *
     * @return string
     */
    public static function getLanguageLabelColor($options = array())
    {
        $lableClass = 'formLabelLanguageCssColor_';
        if (isset($options['addAction']))
        {
            $config = Zend_Registry::get("config");
            $lableClass .= $config->defaultEditLanguage;
        }
        else
        {
            $lableClass .= Zend_Registry::get("currentEditLanguage");
        }
        return $lableClass;
    }

    /**
     * Return the local for a language
     *
     * @param int $langID
     *
     * @return string with the local for the language
     */
    public static function getLocalForLanguage($langID){
        $Languages = Zend_Registry::get("db");
        $Select = $Languages->select()
            ->from('Languages', array('L_Local'))
            ->where('L_ID =?',$langID);

        $local = $Languages->fetchOne($Select);

        return $local;
    }

    /**
     * Format and return the date
     *
     * @param Zend_Date $date
     * @param const     $format     The format of the string
     * @param string    $separator  That will be apply between the elements of the date (default is '/')
     * @param int       $lang       The id os the language if needed
     * @param bool      $weekdays   NOT APPLY FOR NOW
     * @param bool      $cap        NOT APPLY NOW
     *
     * @return string with the string formatted
     */
    public static function dateToString($date, $format = self::DATE_LONG, $separator = '/', $lang=0,$weekdays = false, $cap = false)
    {
        $strDate    = $date;
        $formatByLang = array(
            self::DATE_FULL => array(
                'fr' => array(
                    'format' => "Le %s %s %s %s",
                    'values'=> array('day','dayDate', 'monthName', 'year')),
                'en' => array('format' => "%s %s %s",
                    'values'=> array('monthName', 'dayDate', 'daySuffix', 'year')),
                'es' => array('format' => "%s de %s de %s",
                    'values'=> array('day','dayDate', 'monthName', 'year'))
            ),
            self::DATE_LONG => array(
                'fr' => array(
                    'format' => "%s %s %s",
                    'values'=> array('dayDate', 'monthName', 'year')),
                'en' => array('format' => "%s, %s %s%s, %s",
                    'values'=> array('day', 'monthName', 'dayDate', 'daySuffix', 'year')),
                'es' => array('format' => "%s, el %s de %s de %s",
                    'values'=> array('day','dayDate', 'monthName', 'year'))
                ),
            self::DATE_SHORT => array(
                'fr' => array(
                    'format' => "%s %s %s",
                    'values'=> array('dayDate', 'monthName', 'year')),
                'en' => array('format' => "%s %s %s",
                    'values'=> array('dayDate', 'monthName', 'year')),
                'es' => array('format' => "%s, el %s de %s de %s",
                    'values'=> array('day','dayDate', 'monthName', 'year'))
                ),
            self::DATE_LONG_NO_DAY => array(
                'format' => "%s %s",
                'values' => array('monthName', 'year')
            ),
            self::DATE_DAY_MONTH => array(
                'format' => "%s %s",
                'values' => array('dayDate', 'monthName')
            ),
            self::DATE_DAY_MONTH_SHORT => array(
                'format' => "%s %s",
                'values' => array('dayDate', 'monthName')
            ),
            self::DATE_NUM => array(
                'fr' => array(
                    'format' => "%02d{$separator}%02d{$separator}%d",
                    'values' => array('dayDate', 'month', 'year')),
                'en' => array(
                    'format' => "%02d{$separator}%02d{$separator}%d",
                    'values'=> array('month', 'dayDate', 'year')),
            ),
            self::DATE_SQL => array(
                'format' => "%d-%02d-%02d",
                'values' => array('year', 'month', 'dayDate')
            ),
            self::DATE_NUM_USA => array(
                'format' => "%d{$separator}%d{$separator}%d",
                'values' => array('month', 'dayDate', 'year')
            ),
            self::DATE_NUM_SHORT_YEAR => array(
                'format' => "%02d{$separator}%02d{$separator}%d",
                'values' => array('dayDate','month', 'yearShort')
            ),
            self::DATE_MONTH_YEAR => array(
                'format' => "%s %d",
                'values' => array('monthName', 'year')
            ),
            self::DATE_SHORT_MONTH_YEAR => array(
                'format' => "%s %s",
                'values' => array('monthName', 'year')
            ),
            self::DATE_DAY_DAY => array(
                'format' => "%s %d",
                'values' => array('day', 'dayDate')
            ),
        );
        if (!empty($date))
        {
            if (!Zend_Registry::isRegistered('languageSuffix')){
                $suffixLang = self::getLanguageSuffix(Zend_Registry::get('languageID'));
            }elseif($lang==0){
                $suffixLang = Zend_Registry::get('languageSuffix');
                $locale = self::getLocalForLanguage(Zend_Registry::get("languageID"));
            }else{
                $suffixLang = Cible_FunctionsGeneral::getLanguageSuffix($lang);
                $locale = self::getLocalForLanguage($lang);
            }
            if(is_string($date))
                $date = new Zend_Date($date, null, $locale);

            $day     = $date->get(Zend_Date::WEEKDAY);
            $daySuffix = $date->get(Zend_Date::DAY_SUFFIX);
            $dayDate = $date->get(Zend_Date::DAY);
            $month   = $date->get(Zend_Date::MONTH);
            if ($format == self::DATE_DAY_MONTH_SHORT
                || $format == self::DATE_SHORT
                || $format == self::DATE_SHORT_MONTH_YEAR){
                $monthName = $date->get(Zend_Date::MONTH_NAME_SHORT);
            }else{
            $monthName = $date->get(Zend_Date::MONTH_NAME);
            }
            $year    = $date->get(Zend_Date::YEAR);
            $yearShort = $date->get(Zend_Date::YEAR);
            switch($format)
            {
                case self::DATE_FULL :
                case self::DATE_SHORT :
                case self::DATE_LONG :
                case self::DATE_NUM :
                    if(strpos($dayDate,'0')==0)
                        $dayDate = str_replace("0", "", $dayDate);

                    $formatStr = $formatByLang[$format][$suffixLang]['format'];
                    foreach ($formatByLang[$format][$suffixLang]['values'] as $value)
                        $arguments[] = $$value;
                    $strDate = vsprintf($formatStr, $arguments);
                    if($suffixLang == 'fr')
                    {
                        if($dayDate=='1')
                            $strDate = str_replace("1 ", "1<sup>er</sup> ", $strDate);
                    }

                    break;
                case self::DATE_MONTH_YEAR : // ex: Juin 2011
                    $formatStr = $formatByLang[$format]['format'];
                    foreach ($formatByLang[$format]['values'] as $value)
                        $arguments[] = $$value;
                    $strDate = ucfirst(vsprintf($formatStr, $arguments));
                    break;
                default :
                    $formatStr = $formatByLang[$format]['format'];
                    foreach ($formatByLang[$format]['values'] as $value)
                        $arguments[] = $$value;
                    $strDate = vsprintf($formatStr, $arguments);
                    break;

            }

            if($cap==true)
                $strDate = strtoupper($strDate);
        }

        return $strDate;
    }

    /**
     * Replace accent and some characters to create a name to add to the url.
     *
     * @param string $string The string to format.
     *
     * @return string
     */
    public static function formatValueForUrl($string)
    {
        (string) $format = strtolower($string);
        $format = strip_tags($format);
        $format = html_entity_decode($format, null, 'UTF-8');
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', 'Ώ', 'ώ', 'Ί', 'ί', 'ϊ', 'ΐ', 'Ύ', 'ύ', 'ϋ', 'ΰ', 'Ή', 'ή');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');
        $format = str_replace($a, $b, $format);
        $format =  preg_replace('/[&]/', "et", $format);
        $format =  preg_replace('/[\']/', "-", $format);
        $format =  preg_replace('/[\/]/', "-", $format);
        $format =  preg_replace('/[,]/', "-", $format);
        $format =  preg_replace('/["]/', "", $format);
        $format =  preg_replace('/[%]/', "", $format);
        $format =  preg_replace('/ /', "-", $format);
        $format =  preg_replace('/[^A-Za-z0-9_-]/', "", $format);
        $format =  preg_replace('/[-]{2,50}/', "-", $format);
        $format =  preg_replace('/[-]$/', "", $format);
        $format =  preg_replace('/^[-]/', "", $format);


        return $format;
    }

    public static function getParameters($param = '')
    {
        $oParameters = new ParametersObject();
        $parameters  = $oParameters->getAll();

        if(!empty($param))
            $data = $parameters[0][$param];
        else
            $data = $parameters[0];

        return $data;
    }

    /**
     * Compares two floats.<br />
     * Converts the floats into integer and compares the two values.<br />
     * Returns the result of the comparison as boolean.
     *
     * The comparison string is like :<br />
     * - ">"<br />
     * - ">="<br />
     * - "=="<br />
     * - "<="<br />
     * - "<"<br />
     *
     * @param float  $first      The first float to compare, it's the left part.
     * @param string $comparison The kind of comparison to process.
     * @param float  $second     The second float to compare, it's the right part.
     * @param int    $precision  The number of decimal of the floats to convert.
     *
     * @return bool
     */
    public static function compareFloats($first, $comparison, $second, $precision = 5)
    {
        switch ($comparison)
        {
            case ">":
                $exp    = pow(10, $precision);
                $first  = intval($first * $exp);
                $second = intval($second * $exp);
                return ($first > $second);

            case ">=":
                $exp    = pow(10, $precision);
                $first  = intval($first * $exp);
                $second = intval($second * $exp);
                return ($first >= $second);

            case "<":
                $exp    = pow(10, $precision);
                $first  = intval($first * $exp);
                $second = intval($second * $exp);

                return ($first < $second);

            case "<=":
                $exp    = pow(10, $precision);
                $first  = intval($first * $exp);
                $second = intval($second * $exp);

                return ($first <= $second);

            default:
            case "==":
                $exp    = pow(10, $precision);
                $first  = intval($first * $exp);
                $second = intval($second * $exp);
                return ($first == $second);
        }
    }

    public static function provinceTax($amount = 0)
    {
        if(Zend_Session::namespaceIsset('order'))
            $session = Zend_Session::namespaceGet('order');
        else
            throw new Exception('Session namespace "order" is undefined.
                Thus state id is not set. It is not possible to get TVQ rate.
                Modify code to set state if or create the session namespace with
                stateId parameter.');

            $oTaxe = new TaxesObject();
            $taxes = $oTaxe->getTaxData($session['stateId']);
            $rate   = $taxes['TP_Rate']/100;
            if($taxes['TP_Code'] == "QC")
                $taxValue = ($amount + self::federalTax($amount)) * $rate;
            else
                $taxValue = $amount * $rate;


            $taxValue = (float) $taxValue;
            $session  = new Zend_Session_Namespace('order');
            $session->order['rateProv'] = $taxes;
            $session->tvq = $taxValue;

            return $taxValue;
    }

    public static function federalTax($amount = 0)
    {

        $oOrderParams = new ParametersObject();

        $tps = $oOrderParams->getValueByName('CP_TauxTaxeFed');
        $tps = $tps / 100;

        $taxValue = $amount * $tps;
        $taxValue = (float) $taxValue;

        if (Zend_Session::namespaceIsset('order'))
        {
            $session = New Zend_Session_Namespace('order');
            $session->tps = $taxValue;
        }
        return $taxValue;
    }

    /**
     * Return the page url title in a string for rewriting the url.
     *
     * @param string $stringUrl The url of the page
     * @param bool $$pageRemove <OPTIONAL> Default = false. Remove the last 2 params to get the page url title.
     *
     * @return string $title The page url title
     */
    public static function getTitleFromPath($stringUrl,$pageRemove=false){
        $stringUrlRev = strrev($stringUrl);
        $arrayRev = explode("/", $stringUrlRev);
        if(($pageRemove==true)&&(count($arrayRev)>=3)&&($arrayRev[1]=="egap")){
            $title = $arrayRev[2];
            return strrev($title);
        }
        else if(count($arrayRev)>=2){
            $title = strrev($arrayRev[0]);
            $arrayRev = explode("-uid", $title);
            if(count($arrayRev)>1){

                return $arrayRev[0];
            }
            else{

                return $title;
            }

        }

        return "";
    }

    /**
     * Return the page url title in a string for rewriting the url.
     *
     * @param string $stringUrl The url of the page
     * @param bool $$pageRemove <OPTIONAL> Default = false. Remove the last 2 params to get the page url title.
     *
     * @return string $title The page url title
     */
    public static function getDateFromPath($stringUrl,$pageRemove=false){
        $stringUrlRev = strrev($stringUrl);
        $arrayRev = explode("/", $stringUrlRev);
        if(($pageRemove==true)&&(count($arrayRev)>=3)&&($arrayRev[1]=="egap")){
            $title = $arrayRev[2];
            return strrev($title);
        }
        else if(count($arrayRev)>=2){
            $title = strrev($arrayRev[1]);
            $arrayRev = explode("-uid", $title);
            if(count($arrayRev)>1){

                return $arrayRev[0];
            }
            else{

                return $title;
            }

        }

        return "";
    }



    /**
     * Return the page url title in a string for rewriting the url.
     *
     * @param string $stringUrl The url of the page
     * @param bool $$pageRemove <OPTIONAL> Default = false. Remove the last 2 params to get the page url title.
     *
     * @return string $title The page url title
     */
    public static function getDateFromPathArticle($stringUrl,$pageRemove=false){
        $stringUrlRev = strrev($stringUrl);
        $arrayRev = explode("/", $stringUrlRev);
        if(($pageRemove==true)&&(count($arrayRev)>=3)&&($arrayRev[1]=="egap")){
            $title = $arrayRev[2];
            return strrev($title);
        }
        else if(count($arrayRev)>=2){
            $title = strrev($arrayRev[2]);
            $arrayRev = explode("-uid", $title);
            if(count($arrayRev)>1){

                return $arrayRev[0];
            }
            else{

                return $title;
            }

        }

        return "";
    }








    /**
     * Return the page number for the url that has not the good arguments number.
     *
     * @param string $stringUrl The url of the page
     *
     * @return int the actual page's number
     */
    public static function getPageNumberWithoutParamOrder($stringUrl){
        $stringUrlRev = strrev($stringUrl);
        $arrayRev = explode("/", $stringUrlRev);
        if((count($arrayRev)>=2)&&($arrayRev[1]=="egap")){
            return $arrayRev[0];
        }
        return 1;
    }

    /**
     * Return the string without the extra page.
     *
     * @param string $stringUrl The url of the page
     *
     * @return string with the new url without extra page param
     */
    public static function getUrlWithoutExtraPage($stringUrl){

        $arrayStr = explode("/",$stringUrl);
        $returnStringUrl = "";
        for($x = 0; $x<count($arrayStr);$x++){
            if($arrayStr[$x]=="page"){
                if($arrayStr[$x+2]!="page"){
                    $returnStringUrl .= "/" . $arrayStr[$x];
                }
                else{
                    $x++;
                }
            }
            else{
                if($returnStringUrl==""){
                    $returnStringUrl = $arrayStr[$x];
                }
                else{
                    $returnStringUrl .= "/" . $arrayStr[$x];
                }
            }
        }
        return $returnStringUrl;
    }


    /**
     * Format and return a menu made out of 2 or more menus
     *
     * @param string    $arrayOption    A string to put in the ul of the menu. Example: " class='blueMenu' id='ulTopMenu'"
     *
     * @param array     $arrayMenu      An array that contains the menus and their options.
     *                                  Example: ( array(array($menuTrio2," class='menuHaut2'"),array($menuTrio1," class='menuHaut1'")) )
     *
     * @param bool      $reverse        Wheter or not the menu li will be reverse.
     *                                  Example: <ul><li>111</li><li>222</li></ul> will become <ul><li>222</li><li>111</li></ul>
     *
     * @param bool      $stripTagA      Remove everything inside the li except the <a> tag.
     *
     * @param array     $arrayOption    options supported:  'addSeparator' => 'image or character'
     *                                                      'addSeparatorBeforeFirst' => 'bool'
     *                                                      'addSeparatorAfterLast' => 'bool'
     *
     * @return a menu with concatenated menus
     */
    public static function returnMenuFromMenus($stringOption, $arrayMenu, $reverse=false, $stripTagA=true, $arrayOption = array()){

         $returnStr = "<ul" . $stringOption . ">";
         $separator = "";
         $separatorBool = false;
         $separatorLast = false;
         if(isset($arrayOption['addSeparator'])){
             $separator = $arrayOption['addSeparator'];
             if($separator!=""){
                $separatorBool = true;
             }
         }
         if(isset($arrayOption['addSeparatorBeforeFirst'])){
             if($arrayOption['addSeparatorBeforeFirst']==true){
                 $returnStr .= "<li class='verticalSeparator'>" . $separator . "</li>";
             }
         }
         if(isset($arrayOption['addSeparatorAfterLast'])){
             if($arrayOption['addSeparatorAfterLast']==true){
                $separatorLast = true;
             }
         }
         $arrayString = array();
         foreach($arrayMenu as $items){
            $item = $items[0];
            $option = "";
            if(isset($items[1])){
                $option = $items[1];
            }
            $ulInside = "";
            $subStringUL = substr_count($item, '</ul>');
            if($subStringUL>1){
                $oneU = strpos($item,"<ul");
                $twoU = strrpos($item,"</ul>");
                $threeU = substr($item, $oneU+1, ($twoU-$oneU-2));
                $oneU = strpos($threeU,"<li");
                $threeU = substr($threeU, $oneU);
                array_push($arrayString,$threeU);
             }
             else{
                 $subStringOcc =  substr_count($item, '</li>');
                 for($x = 0; $x < $subStringOcc; $x++){
                     $oneP = strpos($item,"<li");
                     $twoP = strpos($item,"</li>");
                     $subS = substr($item, $oneP, ($twoP-$oneP));
                     $item = substr($item, $twoP+5);
                     if($stripTagA==true){
                        $subS = strip_tags($subS, '<a>');
                     }
                     $tempStr = "<li " . $option . ">";
                     $tempStr .= $subS;
                     $tempStr .= "</li>";
                     array_push($arrayString,$tempStr);
                }
            }
        }
        if($reverse==true){
            $arrayString = array_reverse($arrayString);
        }
        $numberArray = count($arrayString);
        $x = 1;
        foreach($arrayString as $item){
            $returnStr .= $item;
            if(($separatorBool==true)&&($x<$numberArray)){
                $returnStr .= "<li class='verticalSeparator'>" . $separator . "</li>";
            }
            $x++;
        }
        if($separatorLast){
            $returnStr .= "<li class='verticalSeparator'>" . $separator . "</li>";
        }
        $returnStr .= "</ul>";
        return $returnStr;
     }

   /**
     * Explodes a string into pairs of key|values to retrieve parameters.
     *
     * @param string $string Comment string from column which contains the
     *                       parameters
     * @param string $split     The delimiter between paramters.
     * @param string $pairSplit The delimiter between key:value in the parameter
     *
     * @return array
     */
    public static function fetchParams($string, $split = '|', $pairSplit = ':')
    {
        $params = array();
        if (!empty($string))
        {
            $tmp = explode($split, $string);
            foreach ($tmp as $value)
            {
                if (!empty($value)){
                    $split = explode($pairSplit, $value);
                    $params[$split[0]] = $split[1];
                }
            }
        }
        return $params;
    }

    /**
     * Build the path to feed the robots.txt. <br />
     * This path is the a virtual xml file which will be created by modules.
     *
     * @param string $path
     * @param type $title
     * @return string
     */
    public static function getXMLFilesString($title = "")
    {
        $db = Zend_Registry::get('db');
        $xmlString = "";
        (array) $array = array();

        $langs = Cible_FunctionsGeneral::getAllLanguage(true);

        foreach ($langs as $lang){
                $xmlString .= "Sitemap: " . Zend_Registry::get('absolute_web_root') . "/" . $title . "/index/site-map/lang/" . $lang['L_ID'] . "\r\n";
        }

        return $xmlString;
    }


    /**
     * Add a text to an image and create that image in the same directory
     *
     * @param string $path of image
     * @param string $text to add
     * @param string $newNamePath of the created image
     * @param array $option
     *      $option can be the following :
     *          - positionX     the position of the left text in X axe OR if alignH is set it is used has the starting or the ending position to align
     *          - positionY     the position of the bottom text in Y axe
     *          - fontfile      the font file containing .ttf
     *          - size          the font size
     *          - colorR        the font color red
     *          - colorG        the font color green
     *          - colorB        the font color blue
     *          - alignH        left, center or right from the positionX value (if this is set, zoneWidth will be used to determine the correct positionX)
     *          - zoneWidth     it is only used if the alignH option is set
     *          - alignV        top, middle or bottom from the positionY (if this is set, zoneHeight will be used to determine the correct positionY)
     *          - zoneWidth     it is only used if the alignV option is set
     *
     * @return string  new image path
     */
    public static function addTextToImage($path, $text, $newNamePath, $option = array())
    {

        if(!empty($option["positionX"]))
            $positionX = $option["positionX"];
        else
            $positionX = 0;

        if(!empty($option["positionY"]))
            $positionY = $option["positionY"];
        else
            $positionY = 0;

        if(!empty($option["size"]))
            $size = $option["size"];
        else
            $size = 13;


        if(!empty($option["colorR"]))
            $red = $option["colorR"];
        else
            $red = 0;

        if(!empty($option["colorG"]))
            $green = $option["colorG"];
        else
            $green = 0;

        if(!empty($option["colorB"]))
            $blue = $option["colorB"];
        else
            $blue = 0;

        if(!empty($option["fontfile"]))
            $fontfile = "../../www/themes/default/fonts/" . $option["fontfile"];
        else
             $fontfile =  "../../www/themes/default/fonts/arial.ttf";

        if(!empty($option["angle"]))
            $angle = $option["angle"];
        else
            $angle = 0;


        if(!empty($option["alignH"])){
            $zoneWidth = 0;
            if(!empty($option["zoneWidth"])){
                $zoneWidth = $option['zoneWidth'];
            }
            if($option["alignH"]=='right'){
                list($left,, $right) = imageftbbox($size, 0, $fontfile, $text);
                $length = $right-$left;
                $positionX = $positionX - $length;
            }
            else if($option["alignH"]=='center'){
                list($left,, $right) = imageftbbox($size, 0, $fontfile, $text);
                $center = ceil($zoneWidth / 2);
                $positionX = $positionX + $center - (ceil(($right-$left)/2));
            }
        }
        if(!empty($option["alignV"])){
            $zoneHeight = 0;
            if(!empty($option["zoneHeight"])){
                $zoneHeight = $option['zoneHeight'];
            }
            if($option["alignV"]=='top'){
                list(,$bottom,,,,$top) = imageftbbox($size, 0, $fontfile, $text);
                $height = $bottom-$top;
                $positionY = $positionY - $zoneHeight + $height;
            }
            else if($option["alignV"]=='middle'){
                list(,$bottom,,,,$top) = imageftbbox($size, 0, $fontfile, $text);
                $height = $bottom-$top;
                $center = ceil($zoneHeight / 2);
                $positionY = $positionY - $center + (ceil(($height)/2));
            }
        }

        $image['ext'] = strtolower(substr($path, strrpos($path, '.') + 1));
        list($image['width'], $image['height'], $image['type'], $image['attr']) = getimagesize($path);

        if($image['ext'] == 'jpeg' || $image['ext'] == 'jpg'){
            $newImage = imagecreatefromjpeg($path);
            $thumb = imagecreatetruecolor($image['width'],$image['height']);
            $text_color = imagecolorallocate($newImage, $red, $green, $blue);
            imagettftext($newImage, $size , $angle , $positionX, $positionY , $text_color , $fontfile , $text );
            imagecopyresampled($thumb,$newImage,0,0,0,0,$image['width'],$image['height'],$image['width'],$image['height']);
            imagejpeg($thumb, $newNamePath, 100);
            imagedestroy($newImage);
        }

        elseif($image['ext'] == 'gif'){
            $newImage = imagecreatefromgif($path);
            $thumb = imagecreate($image['width'],$image['height']);
            $text_color = imagecolorallocate($newImage, $red, $green, $blue);
            imagettftext($newImage, $size , $angle , $positionX, $positionY , $text_color , $fontfile , $text );
            $trans_color = imagecolorallocate($thumb, 255, 0, 0);
            imagecolortransparent($thumb, $trans_color);
            imagecopyresampled($thumb,$newImage,0,0,0,0,$image['width'],$image['height'],$image['width'],$image['height']);
            imagegif($thumb, $newNamePath);
            imagedestroy($newImage);
        }
        elseif($image['ext'] == 'png'){
            $image_source = imagecreatefrompng($path);
            $newImage = imagecreatetruecolor($image['width'],$image['height']);
            $text_color = imagecolorallocate($newImage, $red, $green, $blue);
            imagettftext($image_source, $size , $angle , $positionX, $positionY , $text_color , $fontfile , $text );
            if (function_exists('imagecolorallocatealpha')){
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $image['width'], $image['height'], $transparent);
                imagecolortransparent($newImage, $transparent);
            }

            imagecopyresampled($newImage, $image_source, 0, 0, 0, 0, $image['width'],$image['height'],$image['width'],$image['height']);
            imagepng($newImage, $newNamePath);
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


    public static function calculateThemrometersData($accomplishPercent, $small=false){
        $returnData = array();
        $accompliHide = 10;
        $accompli = $accomplishPercent;


          if($accomplishPercent>=100){
              $accompli = 105;
          }
          else{
                if($small==false){

                    switch ($accomplishPercent){
                       case 0:
                            $accompliHide = 0;
                            break;
                      case $accomplishPercent > 0 && $accomplishPercent<=10:
                            $accompliHide = 0;
                            break;
                    }
                }
                else{

                    $accompli = $accomplishPercent + 12;
                    switch ($accomplishPercent){
                        case 0:
                            $accompli = 0;
                            $accompliHide = 0;
                            break;
                        case $accomplishPercent > 0 && $accomplishPercent<=12:
                            $accompliHide = 0;
                            break;

                    }
                }
          }

          $returnData["accompli"] = $accompli;
          $returnData["accompliHide"] = $accompliHide;
          return $returnData;
    }





}
