<?php

class Cible_View_Helper_SplitHighlightSearch extends Zend_View_Helper_Abstract
{

    /**
     * Highlight specified words int the search results.
     *
     * @param  string|array $options
     * @param  string $text
     * @param  array $options
     *
     * @return string
     */
    public function splitHighlightSearch($text, $fullString = false)
    {
        $highlightTxt = '';
        $highlight = array();
        $endStr = $fullString ? '' : '...';
        $body = preg_split("/(\<body\>|\<\/body\>)/", $text);
        $parts = array_chunk(preg_split("/\s+(?![^<>]+>)/m", $body[1]), 15);
        foreach ($parts as $part)
        {
            $add = false;
            foreach ($part as $string)
            {
                $add = (preg_match ('/^<b/', $string))?true:false;
                if ($add)
                    break;
            }
            if ($add)
                $highlight[] = implode (' ', $part) .  $endStr;
        }

        if (count($highlight) == 0)
            $highlightTxt = substr($body[1], 0, 100). $endStr;
        else
            $highlightTxt = implode ('', $highlight);

        return $highlightTxt;
    }
}
