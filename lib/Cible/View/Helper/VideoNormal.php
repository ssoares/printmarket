<?php
    class Cible_View_Helper_VideoNormal extends Zend_View_Helper_Abstract
    {
        public function videoNormal($objectID,$path,$data,$option=null){

            $_image_tag .= '<div id="video_' . $data['IFI_Video'] . '" class="videoNormal">';
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

            return $_image_tag;
        }
    }