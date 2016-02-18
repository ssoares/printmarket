<?php
    class Cible_View_Helper_VideoFeature extends Zend_View_Helper_Abstract
    {
        public function videoFeature($objectID,$path,$data,$option=null){

            $_image_tag ='<video width="' . $data['V_Width'] . '" height="' . $data['V_Height'] . '" poster="' . Zend_Registry::get('absolute_web_root') . $path . $data['VI_Poster'] . '" controls="" preload="" tabindex="0">';
            $_image_tag .='<source type="video/mp4; codecs=&quot;avc1.42E01E, mp4a.40.2&quot;" src="' . $path . $data['VI_MP4'] . '"></source>';

            $_image_tag .='<source type="video/webm; codecs=&quot;vp8, vorbis&quot;" src="' . $path . $data['VI_WEBM'] . '"></source>';
            $_image_tag .='<source type="video/ogg; codecs=&quot;theora, vorbis&quot;" src="' . $path . $data['VI_OGG'] . '"></source>';
            $_image_tag .='<object class="vjs-flash-fallback" width="' . $data['V_Width'] . '" height="' . $data['V_Height'] . '" data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" type="application/x-shockwave-flash">';
            $_image_tag .='<param value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" name="movie">';
            $_image_tag .='<param value="true" name="allowfullscreen">';
            $_image_tag .= '<param name="flashvars" value=';
            $_image_tag .= "'config={";
            $_image_tag .= '"playlist":["' . Zend_Registry::get('absolute_web_root') . $path . $data['VI_Poster'] . '", {"url": "' . Zend_Registry::get('absolute_web_root') . $path . $data['VI_MP4'] . '","autoPlay":false,"autoBuffering":true}]}';
            $_image_tag .= "'/>";
            $_image_tag .='</object>';
            $_image_tag .= '</video>';

            return $_image_tag;



            /********************************************/
            /*  Other way with background image         */
            /********************************************/
           /*  $_image_tag = '<a href="#hiddenVideo_' . $data['IFI_Video'] . '" rel="prettyPhoto">';
            if(!empty($data['imageReplace'])){
                $_image_tag .= '<img alt="" src="' . $data['imageReplace'] . '" />';
            }
            else{
                $_image_tag .= '<img alt="" src="themes/default/images/common/pix.gif" />';
            }
            $_image_tag .= '</a>';
            $_image_tag .= '<div id="hiddenVideo_' . $data['IFI_Video'] . '" class="hiddenVideo">';
            $_image_tag .= '<video poster="' . $path . $data['VI_Poster'] . '" controls="controls" preload="" tabindex="0">';
            $_image_tag .= '<source src="' . $path . $data['VI_MP4'] . '" type="video/mp4; codecs=&quot;avc1.42E01E, mp4a.40.2&quot;" />';
            $_image_tag .= '<source src="' . $path . $data['VI_WEBM'] . '" type="video/webm; codecs=&quot;vp8, vorbis&quot;" />';
            $_image_tag .= '<source src="' . $path . $data['VI_OGG'] . '" type="video/ogg; codecs=&quot;theora, vorbis&quot;" />';
            $_image_tag .= '<object data="extranet/js/tiny_mce/plugins/media/moxieplayer.swf" type="application/x-shockwave-flash">';
            $_image_tag .= '<param name="allowfullscreen" value="true" />';
            $_image_tag .= '<param name="flashvars" value="url=' . $path . $data['VI_MP4'] . '" poster="' . $path . $data['VI_Poster'] . '" />';
            $_image_tag .= '<param name="src" value="extranet/js/tiny_mce/plugins/media/moxieplayer.swf" />';
            $_image_tag .= '<param name="allowscriptaccess" value="true" /><img title="No video playback capabilities." alt="Poster Image" src="" />';
            $_image_tag .= '</object> </video>';
            $_image_tag .= '</div>';

            return $_image_tag;  */








        }
    }