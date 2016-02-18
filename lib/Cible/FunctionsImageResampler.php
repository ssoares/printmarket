<?php
    abstract class Cible_FunctionsImageResampler
    {
        public static function resampled($image){
            $image['ext'] = strtolower(substr($image['src'], strrpos($image['src'], '.') + 1));
            list($image['width'], $image['height'], $image['type'], $image['attr']) = getimagesize($image['src']);
            $image['width'] >= $image['height'] ?  $image['format'] = 'landscape' : $image['format'] = 'portrait';
            
            $newDimensions = Cible_FunctionsImageResampler::newDimensions($image);
            
            if($image['ext'] == 'jpeg' || $image['ext'] == 'jpg'){
                // returns an image identifier representing the image obtained from the given filename. 
                $newImage = imagecreatefromjpeg($image['src']);
                
                // returns an image identifier representing a black image of the specified size. 
                $thumb = imagecreatetruecolor($newDimensions['width'],$newDimensions['height']);
                
                // copies a rectangular portion of one image to another image, smoothly interpolating pixel values so that, in particular, reducing the size of an image still retains a great deal of clarity. 
                imagecopyresampled($thumb,$newImage,0,0,0,0,$newDimensions['width'],$newDimensions['height'],$image['width'],$image['height']);
                
                if (function_exists('imagefilter')&&(isset($image['grayScale']))){
                    imagefilter($thumb, IMG_FILTER_GRAYSCALE);
                }  
                
                // creates a JPEG file from the given image
                imagejpeg($thumb, $image['src'], 100);
                
                // frees any memory associated with newImage 
                imagedestroy($newImage);
                
            }
            
            elseif($image['ext'] == 'gif'){
                // returns an image identifier representing the image obtained from the given filename. 
                $newImage = imagecreatefromgif($image['src']);
                
                // returns an image identifier representing a black image of the specified size. 
                $thumb = imagecreate($newDimensions['width'],$newDimensions['height']);

                // returns a color identifier representing the color composed of the given RGB components.
                $trans_color = imagecolorallocate($thumb, 255, 0, 0);
                
                // sets the transparent color in the given image . 
                imagecolortransparent($thumb, $trans_color);

                // copies a rectangular portion of one image to another image, smoothly interpolating pixel values so that, in particular, reducing the size of an image still retains a great deal of clarity. 
                imagecopyresampled($thumb,$newImage,0,0,0,0,$newDimensions['width'],$newDimensions['height'],$image['width'],$image['height']);
                
               
                if (function_exists('imagefilter')&&(isset($image['grayScale'])))
                {
                    imagefilter($thumb, IMG_FILTER_GRAYSCALE);
                }
                // creates the GIF  file in filename from the image image
                imagegif($thumb, $image['src']);
                
                // détruit une image
                imagedestroy($newImage);
            }
            elseif($image['ext'] == 'png'){
                $image_source = imagecreatefrompng($image['src']);
                $new_image = imagecreatetruecolor($newDimensions['width'],$newDimensions['height']);
                 if (function_exists('imagecolorallocatealpha')){
                    imagealphablending($new_image, false);
                    imagesavealpha($new_image, true);
                    $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
                    imagefilledrectangle($new_image, 0, 0, $newDimensions['width'], $newDimensions['height'], $transparent);
                    imagecolortransparent($new_image, $transparent);
                }
                
                imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $newDimensions['width'],$newDimensions['height'],$image['width'],$image['height']);
                
                if (function_exists('imagefilter')&&(isset($image['grayScale'])))
                {
                    imagefilter($thumb, IMG_FILTER_GRAYSCALE);
                }
                
                imagepng($new_image, $image['src']);
                imagedestroy($new_image);
            }
        }
        
        private static function newDimensions($image){
            $newDimensions['width'] = $image['width'];
            $newDimensions['height'] = $image['height'];
            
            if($image['format'] == 'landscape'){
                if($image['width'] > $image['maxWidth'] ){
                    $reduce = $image['maxWidth']/$image['width'];
                    $newDimensions['width'] = $image['maxWidth'];
                    $newDimensions['height']= ceil($image['height']*$reduce);
                    
                    if($newDimensions['height'] > $image['maxHeight']){
                        $reduce = $image['maxHeight']/$image['height'];
                        $newDimensions['width'] = ceil($image['width']*$reduce);
                        $newDimensions['height']= $image['maxHeight'];            
                    }
                             
                }
            }
            else{
                if($image['height'] > $image['maxHeight'] ){
                    $reduce = $image['maxHeight']/$image['height'];
                    $newDimensions['width'] = ceil($image['width']*$reduce);
                    $newDimensions['height']= $image['maxHeight'];            
                    
                    if($newDimensions['width'] > $image['maxWidth']){
                        $reduce = $image['maxWidth']/$image['width'];
                        $newDimensions['width'] = $image['maxWidth'];
                        $newDimensions['height']= ceil($image['height']*$reduce);
                    }
                }
            }
            return $newDimensions;
        } 
    }  
?>
