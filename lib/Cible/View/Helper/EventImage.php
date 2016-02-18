<?php
    class Cible_View_Helper_EventImage extends Cible_View_Helper_ModuleImage
    {
        public function eventImage($id, $image, $size, $options = null){

            return parent::moduleImage('events', $id, $image, $size, $options);
        }
    }