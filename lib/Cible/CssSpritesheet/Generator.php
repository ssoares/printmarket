<?php

/**
 * Description of CssSpritesheet
 *
 * @author draluc
 */
class Cible_CssSpritesheet_Generator extends Cible_CssSpritesheet {

    protected $_filetypes = array(
        'jpg' => true,
        'png' => true,
        'jpeg' => true,
        'gif' => true);
    protected $_outputfiletypes = array('png' => true);
    protected $_replaceCSS = array('-hover' => ':hover');
    protected $_files = array();
    protected $_fileWidth = 64;
    protected $_fileHeight = 64;

    public function __construct(array $config = array()) {
        $extension = pathinfo(self::$_outputImage, PATHINFO_EXTENSION);

        $isValidImagePath = !(self::$_outputImage[0] == '.' || !isset($this->_outputfiletypes[$extension]));

        if ($isValidImagePath && $this->_openFiles() && $this->_verifyLastModified()) {
            $this->_createSprite();
        }
    }

    protected function _openFiles() {
        // Read through the directory for suitable images
        if ($handle = opendir(self::$_folder)) {
            while (false !== ($file = readdir($handle))) {
                $split = explode('.', $file);
                // Ignore non-matching file extensions
                if ($file[0] == '.' || !isset($this->_filetypes[$split[count($split) - 1]]))
                    continue;

                $fullpath = self::$_folder . '/' . $file;
                $cssClass = explode('.', $file);
                $cssClass = $cssClassTmp = $cssClass[0];
                $cssClassOrigin = $cssClass;
                $nameModified = false;
                foreach ($this->_replaceCSS as $search => $replace) {
                    $cssClass = str_replace($search, $replace, $cssClass);
                    if ($cssClassTmp != $cssClass) {
                        $nameModified = true;
                    }
                }
                
                // Image will be added to sprite, add to array
                $this->_files[] = array(
                    'filename' => $file,
                    'cssclass' => $cssClass,
                    'cssclassorigin' => $cssClassOrigin,
                    'nameModified' => $nameModified,
                    'fullpath' => $fullpath,
                    'width' => NULL,
                    'height' => NULL,
                    'x' => NULL,
                    'y' => NULL);
            }
            closedir($handle);
            return (count($this->_files) > 0);
        } else
            return false;
    }

    protected function _verifyLastModified() {
        $dateFolder = filemtime(self::$_folder . "/.");
        $dateCSS = (is_file(self::$_outputCSS)) ? filemtime(self::$_outputCSS) : 0;
        $dateImage = (is_file(self::$_outputImage)) ? filemtime(self::$_outputImage) : 0;

        if ($dateFolder > $dateCSS || $dateFolder > $dateImage)
            return true;

        $date1 = filemtime(self::$_outputImage);
        foreach ($this->_files as $file) {
            $date2 = filemtime($file['fullpath']);
            if ($date2 > $date1)
                return true;
        }

        return false;
    }

    protected function _calculatePositions() {
        //on pogne les dimensions en premier
        foreach ($this->_files as $index => $file) {
            $imageSize = getimagesize($file['fullpath']);
            $this->_files[$index]['width'] = $imageSize[0];
            $this->_files[$index]['height'] = $imageSize[1];
        }
        //maintenant le code pour toute pacter dans le plus petit espace possible
        $restart = true;
        while ($restart) {
            $restart = false;
            $packer = new Cible_CssSpritesheet_RectanglePacking($this->_fileWidth, $this->_fileHeight);
            foreach ($this->_files as $index => $file) {
                $position = $packer->findCoords($file['width'], $file['height']);
                if ($position == null) {

                    if ($this->_fileHeight == $this->_fileWidth)
                        $this->_fileHeight *= 2;
                    else
                        $this->_fileWidth = $this->_fileHeight;

                    $restart = true;
                    break;
                }
                $this->_files[$index]['x'] = $position['x'];
                $this->_files[$index]['y'] = $position['y'];
            }
        }
    }

    protected function _createSprite() {
        //calcule les positions avec un joli algorythme
        $this->_calculatePositions();

        //on regarde si le fichier généré est un fichier less
        $isLess = strpos(self::$_outputCSS, '.less') !== false;

        //initialise le fichier css
        $cssPath = $this->_findRelativePath(self::$_outputCSS, self::$_outputImage);
        $cssContent = ".sprite {display:inline-block;overflow:hidden;background-repeat:no-repeat;font-size:0;background-image:url({$cssPath}) }\n\n";
//        if ($isLess)
//            $cssMixinsContent = ".import-sprite(@name) when (default()) {}\n";

        //initialise l'image
        $im = imagecreatetruecolor($this->_fileWidth, $this->_fileHeight);
        imagesavealpha($im, true);
        $alpha = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $alpha);

        //on rempli les deux fichiers
        foreach ($this->_files as $file) {
            //image
            $im2 = imagecreatefrompng($file['fullpath']);
            imagecopy($im, $im2, $file['x'], $file['y'], 0, 0, $file['width'], $file['height']);
            //css
            $cssContent .= ".{$file["cssclass"]} { width:{$file["width"]}px; height:{$file["height"]}px; background-position: -{$file["x"]}px -{$file["y"]}px } \n";

            //less mixins
            if ($isLess) {
                $ruleset = '.' . $file["cssclassorigin"];
                if ($file["nameModified"] === true) {
                    $ruleset = "width:{$file["width"]}px; height:{$file["height"]}px; background-position: -{$file["x"]}px -{$file["y"]}px";
                }
                $cssMixinsContent .= ".import-sprite(@name) when(@name = '" . $file["cssclassorigin"] . "') {.sprite;" . $ruleset . ";}\n";
                $cssMixinsContent .= ".import-sprite-only(@name) when(@name = '" . $file["cssclassorigin"] . "') {" . $ruleset . ";}\n";
            }
        }
        //on sauvegarde le fichier css final
        if ($isLess)
            file_put_contents(self::$_outputCSS, $cssContent . $cssMixinsContent);
        else
            file_put_contents(self::$_outputCSS, $cssContent);

        //on sauvegarde l'image finale
        imagepng($im, self::$_outputImage);
        imagedestroy($im);
    }

    protected function _findRelativePath($from, $to) {
        // some compatibility fixes for Windows paths
        $from = explode('/', $from);
        $to = explode('/', $to);
        foreach ($from as $depth => $dir) {

            if (isset($to[$depth])) {
                if ($dir === $to[$depth]) {
                    unset($to[$depth]);
                    unset($from[$depth]);
                } else {
                    break;
                }
            }
        }
        //$rawresult = implode('/', $to);
        for ($i = 0; $i < count($from) - 1; $i++) {
            array_unshift($to, '..');
        }
        $result = implode('/', $to);
        return $result;
    }

}
