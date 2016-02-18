<?php
    class Cible_View_Helper_NewsImage extends Cible_View_Helper_ModuleImage
    {
        public function newsImage($id, $image, $size, $options = null){

            return parent::moduleImage('news', $id, $image, $size, $options);
        }
    }