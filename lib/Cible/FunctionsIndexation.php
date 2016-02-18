<?php

abstract class Cible_FunctionsIndexation
{

    //var $directory = '../../www/indexation/all_index';

    public static function indexation($indexationData, $directory = null)
    {
        if (is_null($directory))
            $directory = Zend_Registry::get('lucene_index');
        try
        {
            $index = Zend_Search_Lucene::open($directory);
        }
        catch (Zend_Search_Lucene_Exception $e)
        {
            try
            {
                $index = Zend_Search_Lucene::create($directory);
                //echo("Création d'un nouvel index : $path");
            }
            catch (Zend_Search_Lucene_Exception $e)
            {
                echo("Impossible d'ouvrir ou créer un index $directory");
                echo($e->getMessage());
                echo "Impossible d'ouvrir ou créer un index:" .
                "{$e->getMessage()}";
                exit(1);
            }
        }
        switch ($indexationData['action'])
        {
            case 'add':
                Cible_FunctionsIndexation::indexationAdd($indexationData, $directory);
                break;
            case 'delete':
                Cible_FunctionsIndexation::indexationDelete($indexationData, $directory);
                break;
            case 'update':
                Cible_FunctionsIndexation::indexationDelete($indexationData, $directory);
                Cible_FunctionsIndexation::indexationAdd($indexationData, $directory);
                break;

            default:
                break;
        }
    }

    public static function indexationAdd($indexationData, $directory)
    {
        $title = Cible_FunctionsGeneral::html2text($indexationData['title']);
        $encoding = mb_internal_encoding();
        $contentText = Cible_FunctionsGeneral::html2text(html_entity_decode($indexationData['contents'], null, $encoding));
        $content = mb_strtolower(Cible_FunctionsGeneral::removeAccents($contentText));
        Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
        $doc = new Zend_Search_Lucene_Document();
        $doc->addField(Zend_Search_Lucene_Field::Keyword('pageID', $indexationData['pageID']));
        if (isset($indexationData['object'])){
            $doc->addField(Zend_Search_Lucene_Field::Keyword('object', $indexationData['object']));
        }
        $doc->addField(Zend_Search_Lucene_Field::Keyword('moduleID', $indexationData['moduleID']));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('contentID', $indexationData['contentID']));
        $doc->addField(Zend_Search_Lucene_Field::Keyword('languageID', $indexationData['languageID']));
//        $doc->addField(Zend_Search_Lucene_Field::Text('text', $text, mb_detect_encoding($text)));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $title, $encoding));
        $doc->addField(Zend_Search_Lucene_Field::unIndexed('excerpt', $contentText, $encoding));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('link', $indexationData['link'], $encoding));
        $doc->addField(Zend_Search_Lucene_Field::UnStored('contents', $content, $encoding));

        $newIndex = !is_dir($directory);
        $index = new Zend_Search_Lucene($directory, $newIndex);
        $index->addDocument($doc);
        $index->commit();
    }

    public static function indexationDelete($indexationData, $directory)
    {
        if (is_dir($directory))
        {
            $index = Zend_Search_Lucene::open($directory);
            $term = new Zend_Search_Lucene_Index_Term($indexationData['contentID'], 'contentID');

            foreach ($index->termDocs($term) as $id)
            {
                $doc = $index->getDocument($id);
                if ($doc->languageID == $indexationData['languageID'] && $doc->moduleID == $indexationData['moduleID'])
                    $index->delete($id);
            }
        }
    }

    public static function indexationSearch($searchParams)
    {
        Zend_Search_Lucene::setDefaultSearchField(null);

        $words = strtolower(Cible_FunctionsGeneral::removeAccents(Cible_FunctionsGeneral::html2text($searchParams['words'])));

        $wordsArray = explode(' ', $words);
        $searchOpt = null;
        if (isset($searchParams['searchOption']))
            $searchOpt = $searchParams['searchOption'];
        if (count($wordsArray) > 1)
        {
            switch($searchOpt)
            {
                case 2:
                    $string = implode(' && ', $wordsArray);
                    $query = Zend_Search_Lucene_Search_QueryParser::parse($string);
                    break;
                case 3:
                    $string = '"' . $words . '"';
                    $query = Zend_Search_Lucene_Search_QueryParser::parse($string);
                    break;
                case 1:
                default:
                    $string = implode(' || ', $wordsArray);
                    $query = Zend_Search_Lucene_Search_QueryParser::parse($string);
                    break;
            }
        }
        else
        {
            if (strlen($words) >= 3)
            {
                Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);
                $pattern = new Zend_Search_Lucene_Index_Term("*$words*");
                $query = new Zend_Search_Lucene_Search_Query_Wildcard($pattern);
            }
            else
            {
                $term = new Zend_Search_Lucene_Index_Term($words);
                $query = new Zend_Search_Lucene_Search_Query_Term($term);
            }
        }

        $result = array();
        switch ($searchParams['sites'])
        {
            case $searchParams['currentSite']:
                $directoryIndex = Zend_Registry::get('lucene_index');
                $directoryPdf = Zend_Registry::get('lucene_pdf');
                $index = self::_getResults($query, $directoryIndex);
                $indexPdf = array();
//                $indexPdf = self::_getResults($query, $directoryPdf);
                $mergeData = array_merge($index, $indexPdf);
                $result[$searchParams['sites']] = $mergeData;
                break;
            case 'all':
                $config = Zend_Registry::get('config');

                foreach ($config->multisite as $data)
                {
                    if ((bool) $data->active)
                    {
                        $name = $data->name;
                        $directoryIndex = preg_replace(
                            '/\/' . $searchParams['currentSite'] . '\//'
                            , '/' . $name . '/'
                            , Zend_Registry::get('lucene_index'));
                        $directoryPdf = preg_replace(
                            '/\/' . $searchParams['currentSite'] . '\//'
                            , '/' . $name . '/'
                            , Zend_Registry::get('lucene_pdf'));
                        try
                        {
                            $index = self::_getResults($query, $directoryIndex);
                            $indexPdf = self::_getResults($query, $directoryPdf);
                            $mergeData = array_merge($index, $indexPdf);
                            $result[$name] = $mergeData;
                        }
                        catch (Exception $exc)
                        {
                            $result[$name] = array();
                        }
                    }
                }
                break;

            default:
                $directoryIndex = preg_replace('/\/' . $searchParams['currentSite'] . '\//'
                    , '/' . $searchParams['sites'] . '/'
                    , Zend_Registry::get('lucene_index'));
                $directoryPdf = preg_replace('/\/' . $searchParams['currentSite'] . '\//'
                    , '/' . $searchParams['sites'] . '/'
                    , Zend_Registry::get('lucene_pdf'));
                $index = self::_getResults($query, $directoryIndex);
                $indexPdf = self::_getResults($query, $directoryPdf);
                $mergeData = array_merge($index, $indexPdf);
                $result[$searchParams['sites']] = $mergeData;
                break;
        }

        return $result;
    }

    private static function _getResults($query, $directory)
    {
        $i = 0;
        $result = array();
        try
        {
            Zend_Search_Lucene_Analysis_Analyzer::setDefault(new Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
            $index = new Zend_Search_Lucene($directory);
            $hits = $index->find($query);
            foreach ($hits as $hit)
            {
                $result[$i]['ID'] = $hit->id;
                $result[$i]['contentID'] = $hit->contentID;
                $result[$i]['languageID'] = $hit->languageID;
                $result[$i]['object'] = $hit->object;

                try
                {
                    $result[$i]['link'] = $hit->link;
                }
                catch (Exception $e)
                {
                    $result[$i]['link'] = '';
                }

                try
                {
//                if (strpos($hit->title , $query))
                    $result[$i]['title'] = $query->highlightMatches($hit->title);
//                else
//                    $result[$i]['title'] = $hit->title;
                }
                catch (Exception $e)
                {
                    $result[$i]['title'] = '';
                }

                try
                {
                    //                if (strpos($hit->text , $query))
                    //                    $result[$i]['text'] = $query->highlightMatches($hit->text);
                    //                else
                    $result[$i]['text'] = $hit->text;
                }
                catch (Exception $e)
                {
                    $result[$i]['text'] = '';
                }

                try
                {
                    $result[$i]['moduleID'] = $hit->moduleID;
                }
                catch (Exception $e)
                {
                    $result[$i]['moduleID'] = '';
                }
                try
                {
                    $result[$i]['excerpt'] = $query->highlightMatches($hit->excerpt);
                }
                catch (Exception $e)
                {
                    $result[$i]['excerpt'] = '';
                }

                try
                {
                    $result[$i]['pageID'] = $hit->pageID;
                }
                catch (Exception $e)
                {
                    $result[$i]['pageID'] = '';
                }

                try
                {
                    $result[$i]['languageID'] = $hit->languageID;
                }
                catch (Exception $e)
                {
                    $result[$i]['languageID'] = '';
                }
                $i++;
            }
        }
        catch (Exception $exc)
        {
            echo "<pre>";
            echo $directory . '<br />';
            echo $exc;
            echo "</pre>";
            exit;
        }

        return $result;
    }

    public static function indexationBuild()
    {
        set_time_limit(0);
        /*         * ****** PAGE ******* */
        $obj = new PagesObject();
        $obj->setIndexationData();
        $modules = Cible_FunctionsModules::getModules(true);

        foreach ($modules as $module)
        {
            $objName = ucfirst($module['M_MVCModuleTitle']) . 'Object';
            $oMod = new $objName();
            $oMod->setIndexationData();
        }
    }

    public static function indexationBuildPdf($path)
    {
        $directory = Zend_Registry::get('lucene_pdf');
        set_time_limit(0);
        $globOut = self::_getFiles($path);
        if (count($globOut) > 0)
        {
            $files = array();
            foreach ($globOut as $filename)
            {
                $lastModified = filemtime($filename);
                $now = time();
//                $reindex = abs($now - $lastModified) < 60*60*24*7 ? true:false;
                $reindex = true;
                if ($reindex)
                {
                    $metaValues = array(
                        'Title' => '',
                        'Author' => '',
                        'Subject' => '',
                        'Keywords' => '',
                        'Creator' => '',
                        'Producer' => '',
                        'CreationDate' => '',
                        'ModDate' => '',
                    );
                    $pdfParse = new Cible_View_Helper_PdfParser();
                    $contents = $pdfParse->pdf2txt($filename);
                    try
                    {
                        $pdf = Zend_Pdf::load($filename);
                        foreach ($metaValues as $meta => $metaValue)
                        {
                            if (isset($pdf->properties[$meta]))
                                $metaValues[strtolower($meta)] = $pdf->properties[$meta];
                            else
                                $metaValues[strtolower($meta)] = '';
                        }
                    }
                    catch (Exception $exc)
                    {
                        $metaValues = $pdfParse->getMeta();
                    }

                    $link = str_replace(Zend_Registry::get('fullDocumentRoot'), '/', $filename);

                    $indexData['action'] = "add";
                    $indexData['pageID'] = 0;
                    $indexData['moduleID'] = 999;
                    $indexData['contentID'] = basename($filename);
                    $indexData['languageID'] = 1;
                    $indexData['title'] = $metaValues['title'] . ' ' . basename($filename);
                    $indexData['text'] = $metaValues['author'] . ' ' . $metaValues['subject'] . ' ' . $metaValues['keywords'];
                    $indexData['object'] = '';
                    $indexData['link'] = $link;
                    $indexData['contents'] = $contents;

                    Cible_FunctionsIndexation::indexation($indexData, $directory);
                }
            }
            if ($reindex)
                $pdfParse->clearTxt();
//            $this->view->files = $files;
        }
    }

    public static function indexationDeleteAll($directory = "")
    {
        if (empty($directory))
            $directory = Zend_Registry::get('lucene_index');

        try
        {
            $index = Zend_Search_Lucene::open($directory);
        }
        catch (Zend_Search_Lucene_Exception $e)
        {
            try
            {
                $index = Zend_Search_Lucene::create($directory);
            }
            catch (Zend_Search_Lucene_Exception $e)
            {
                exit(1);
            }
        }

        for ($count = 0; $count < $index->maxDoc(); $count++)
        {
            $index->delete($count);
        }
    }

    public static function indexationDeleteAllPdf()
    {
        $directory = Zend_Registry::get('lucene_pdf');
        self::indexationDeleteAll($directory);
    }

    private static function _getFiles($path, $data = array())
    {
        // Si $chemin est un dossier => on appelle la fonction explorer() pour chaque élément (fichier ou dossier) du dossier$chemin
        if (is_dir($path))
        {
            $tmp = glob($path . '*.pdf');
            $data = array_merge($data, $tmp);
            $me = opendir($path);
            while ($child = readdir($me))
            {
                if (is_dir($path . $child) && $child != '.' && $child != '..')
                    $data = self::_getFiles($path . $child . DIRECTORY_SEPARATOR, $data);
            }
        }

        return $data;
    }

}
