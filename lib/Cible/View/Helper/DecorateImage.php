<?php

class Cible_View_Helper_DecorateImage extends Zend_View_Helper_Abstract
{

    /**
     * Explodes the text to replace an image with the class photo_borders into a table
     *
     * @param string $textT the text
     *
     * @return array
     */
    public static function decorateImage($textT, $raw = false)
    {
        $returnS = "";
        $arrayT = explode("<", $textT);

        if (count($arrayT) > 1 && !$raw)
        {
            for ($x = 0; $x < count($arrayT); $x++)
            {
                $text = $arrayT[$x];
                $pos = strrpos($text, 'photo_borders');
                if ($pos === false)
                {
                    if ($text != "")
                        $returnS .= "<" . $text;
                }
                else
                {
                    if ($text != "")
                    {
                        $returnS .= self::addTable($text);
                    }
                }
            }
            return $returnS;
        }
        else
        {
            if ($raw)
            {
                $textT = self::addTable($textT, $raw);
            }

            return $textT;
        }
    }

    private static function addTable($text, $raw = false)
    {
        $endPos = 0;
        if (!$raw)
        {
            $endPos = strrpos($text, '/>');
            if (empty($endPos))
                $endPos = strrpos($text, '</');
        }
        $string = "";
        $pos = strrpos($text, 'right');
        if ($pos === false)
        {
            $string = '<table align="left" class="decorator">';
        }
        else{
            $string = '<table align="right" class="decorator">';
        }
        $string .='<tr>
                        <td class="imgTopLeft"></td>
                        <td class="imgTopMiddle"></td>
                        <td class="imgTopRight"></td>
                    </tr>
                        <tr>
                            <td class="imgLeftBorder"></td>
                            <td class="imgCenter">';
                        $string .= $raw ? '' : '<';
        $string .= $raw ? $text : substr($text, 0, $endPos +2);
                        $string .= '</td>
                        <td class="imgRightBorder"></td>
                        </tr>
                        <tr>
                            <td class="imgBottomLeft"></td>
                            <td class="imgBottomMiddle"></td>
                            <td class="imgBottomRight"></td>
                        </tr>
                    </table>';

        if (!$raw)
            $string .= substr_replace($text, '', 0, $endPos +2);

        return $string;
    }
}