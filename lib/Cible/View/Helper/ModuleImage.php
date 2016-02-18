<?php
    class Cible_View_Helper_ModuleImage extends Zend_View_Helper_Abstract
    {
        public function moduleImage($module, $id, $image, $size, $options = null){

            $_image_tag = '<img src="%SOURCE%" alt="%ALT%" %ATTR%  />';

            $_config = Zend_Registry::get('config');
            $imgParam = 'image';
            if (isset($options['key'])){
                $imgParam = $options['key'];
                unset($options['key']);
            }
            if( array_key_exists($size, $_config->$module->$imgParam->toArray()) )
            {
                $_alt = !empty($options['alt']) ? $options['alt'] : '';

                $_thickboxEnabled = isset( $options['thickbox'] ) ? true : false;
                $_prettyPhotoEnabled = isset( $options['prettyPhoto'] ) ? true : false;
                $magicZoomEnabled = isset( $options['magicZoom'] ) ? true : false;

                $_attr = '';

                $_image = "{$_config->$module->$imgParam->$size->maxWidth}x{$_config->$module->$imgParam->$size->maxHeight}_{$image}";
                $_source = Zend_Registry::get('rootImgPath');
                if (!preg_match('/'.$module.'/', Zend_Registry::get('rootImgPath')))
                {
                    if ('menu' != $module){
                        $tmp = (explode('/', Zend_Registry::get('rootImgPath')));
                        $tmp[4] = $module;
                        $_source = implode('/', $tmp);
                    }else{
                        $_source = rtrim(Zend_Registry::get('rootImgPath'),'/') . '/'.$module . '/';
                    }
                }
                $_source .= "{$id}/$_image";

                 // Pour retourner seulement le source de l'image
                if(isset($options["getSource"]))
                {
                    if($_prettyPhotoEnabled)
                    {
//                        $this->view->jQuery()->addJavascriptFile($this->view->baseUrl().'/js/jquery/jquery.prettyPhoto.js');
//                        $this->view->headLink()->offsetSetStylesheet(0, $this->view->locateFile('prettyPhoto.css'), 'all');
//        $this->view->headLink()->appendStylesheet($this->view->locateFile('prettyPhoto.css'), 'all');
                    }

                    return $_source;
                }

                $exludeOptions = array(
                    'alt',
                    'prettyPhoto',
                    'magicZoom',
                    'thickbox',
                    'noGroup',
                    'useSize'
                    );

                if( !empty($options) )
                {
                    foreach($options as $key => $value)
                    {
                        if(!in_array($key,$exludeOptions))
                            $_attr .= "$key=\"$value\" ";
                    }
                }

                if( $_thickboxEnabled ){
                    $this->view->jQuery()->addJavascriptFile($this->view->baseUrl().$_config->edith_root.'/js/thickbox/thickbox.js');
                    $this->view->headLink()->appendStylesheet($this->view->baseUrl().$_config->edith_root.'/js/thickbox/thickbox.css', 'screen');

                    $_thickBox_config = $options['thickbox'];

                    if( $_thickBox_config['size']){
                        $_thickbox_size = $_thickBox_config['size'];
                        $_thickbox_image = "{$_config->$module->image->$_thickbox_size->maxWidth}x{$_config->$module->image->$_thickbox_size->maxHeight}_{$image}";

                        $_thickbox_image_source = "{$this->view->baseUrl()}/data/images/{$module}/{$id}/$_thickbox_image";

                        return $this->view->link($_thickbox_image_source, str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag), array('class'=>'thickbox'));
                    }
                }
                elseif( $_prettyPhotoEnabled ){
//                    $this->view->jQuery()->addJavascriptFile($this->view->locateFile('jquery.prettyPhoto.js', 'jquery'));
//                    $this->view->headLink()->offsetSetStylesheet(0, $this->view->locateFile('prettyPhoto.css'), 'all');
//        $this->view->headLink()->appendStylesheet($this->view->locateFile('prettyPhoto.css'), 'all');

                    $_prettyPhoto_config = $options['prettyPhoto'];

                    if( $_prettyPhoto_config['size']){
                        $_prettyPhoto_size = $_prettyPhoto_config['size'];
                        $_prettyPhoto_image = "{$_config->$module->image->$_prettyPhoto_size->maxWidth}x{$_config->$module->image->$_prettyPhoto_size->maxHeight}_{$image}";
                        $_prettyPhoto_image_source = Zend_Registry::get('rootImgPath');
                        if (!preg_match('/'.$module.'/', Zend_Registry::get('rootImgPath')))
                        {
                            if ('menu' != $module){
                                $tmp = (explode('/', Zend_Registry::get('rootImgPath')));
                                $tmp[4] = $module;
                                $_source = implode('/', $tmp);
                            }else{
                                $_source = rtrim(Zend_Registry::get('rootImgPath'),'/') . '/'.$module . '/';
                            }
                        }
                        $_prettyPhoto_image_source .= "{$id}/$_prettyPhoto_image";

                        $alt = "";
                        $title = "";
                        if(isset($_prettyPhoto_config['alt'])){
                            $alt = strip_tags($_prettyPhoto_config['alt']);
                        }
                        if(isset($_prettyPhoto_config['title'])){
                            $title .= strip_tags($_prettyPhoto_config['title']);
                        }

                        if(isset($options['noGroup'])){
                            return $this->view->link($_prettyPhoto_image_source, str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag), array('class' => 'prettyPhoto','title' => $title));
                        }
                        else{
                            return $this->view->link($_prettyPhoto_image_source, str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag), array('class'=> 'prettyPhoto', 'title' => $title));
                        }
                    }
                }
                elseif( $magicZoomEnabled ){
                    $this->view->jQuery()->addJavascriptFile($this->view->locateFile('magiczoom.js'));
                    $this->view->headLink()->appendStylesheet($this->view->locateFile('magiczoom.css'), 'screen');

                    $magicZoomConfig = $options['magicZoom'];

                    if( $magicZoomConfig['size']){
                        $magicZoomSize = $magicZoomConfig['size'];
                        $magicZoomImage = "{$_config->$module->$imgParam->$magicZoomSize->maxWidth}x{$_config->$module->$imgParam->$magicZoomSize->maxHeight}_{$image}";

                        $magicZoomImageSource = "{$this->view->baseUrl()}/data/images/{$module}/{$id}/$magicZoomImage";

                        return $this->view->link(
                            $magicZoomImageSource,
                            str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag),
                            array(
                                'rel'   => $magicZoomConfig['rel'],
                                'class' => $options['class']
                                )
                            );
                    }
                }

                return str_replace(array('%SOURCE%','%ALT%','%ATTR%'), array($_source, $_alt, $_attr), $_image_tag);

            } else {

                return '<span class="error>invalid format</span>';

            }


        }
    }