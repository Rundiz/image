<?php
require __DIR__.'/include-imagick-functions.php';

$source_image_gif = '../source-images/city-amsterdam.gif';
echo '<a href="'.$source_image_gif.'">original image</a>'."\n";
echo '<hr>'."\n\n";
$processed_images_folder = '../processed-images/';
$processed_images_fullpath = realpath($processed_images_folder).DIRECTORY_SEPARATOR;


list($width, $height) = getimagesize($source_image_gif);

$new_width1 = 900;
$new_height1 = 600;

$new_width2 = 700;
$new_height2 = 467;

$crop_width = 460;
$crop_height = 460;

// resize 1
$Imagick = new \Imagick(realpath($source_image_gif));
$Imagick->resizeImage($new_width1, $new_height1, \Imagick::FILTER_LANCZOS, 1);
$save_image_link = 'imagick-resize-900x600.gif';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 2 from resized 1
$Imagick->resizeImage($new_width2, $new_height2, \Imagick::FILTER_LANCZOS, 1);
$save_image_link = 'imagick-resize-700x467.gif';
$save_result = $Imagick->writeImages($processed_images_fullpath.$save_image_link, false);// for non animated gif, it works like writeImage() method.
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 3 (crop)
// crop on gif needs special step (coalesceImages()) for crop animate gif.
//$Imagick = $Imagick->coalesceImages();
//if (is_object($Imagick)) {
//    foreach ($Imagick as $Frame) {
//        $Frame->cropImage($crop_width, $crop_height, 0, 0);
//        $Frame->setImagePage(0, 0, 0, 0);
//    }
//}
// ---------------------------------------------------------------------------------
// crop normal gif that is NOT animated gif. ---------------------------------
$Imagick->cropImage($crop_width, $crop_height, 0, 0);
$Imagick->setImagePage(0, 0, 0, 0);// required to crop NON animated gif
// ---------------------------------------------------------------------------------
$save_image_link = 'imagick-crop-460x460.gif';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 4 (rotate)
$Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise(90));
$save_image_link = 'imagick-crop-460x460-rotate.gif';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 5 (rotate from original source)
$Imagick = new \Imagick(realpath($source_image_gif));
$Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise(270));
$save_image_link = 'imagick-crop-1920x1281-rotate-from-original-source.gif';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 6 (save to .png)
$Imagick = new \Imagick(realpath($source_image_gif));
$Imagick->resizeImage($new_width1, $new_height1, \Imagick::FILTER_LANCZOS, 1);
$Imagick->setImagePage(0, 0, 0, 0);// required to resize NON animated gif
$save_image_link = 'imagick-resize-900x600-from-gif.png';
// convert transparency gif to white before save to another extension
$Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
$Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
$Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
// -----------------------------------------------------------------------------------------------
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($processed_images_folder.$save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";

// resize 7 (save to .jpg)
$save_image_link = 'imagick-resize-900x600-from-gif.jpg';
// convert transparency gif to white before save to another extension
$Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
$Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
$Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
// -----------------------------------------------------------------------------------------------
$Imagick->setImageCompressionQuality(100);
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($processed_images_folder.$save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";
include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';