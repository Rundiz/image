<?php
require __DIR__.'/include-imagick-functions.php';

$source_image_jpg = '../source-images/city-amsterdam.jpg';
echo '<a href="'.$source_image_jpg.'">original image</a>'."\n";
echo '<hr>'."\n\n";
$processed_images_folder = '../processed-images/';
$processed_images_fullpath = realpath($processed_images_folder).DIRECTORY_SEPARATOR;


list($width, $height) = getimagesize($source_image_jpg);

$new_width1 = 900;
$new_height1 = 600;

$new_width2 = 700;
$new_height2 = 467;

$crop_width = 460;
$crop_height = 460;

// resize 1
$Imagick = new \Imagick(realpath($source_image_jpg));
$Imagick->resizeImage($new_width1, $new_height1, \Imagick::FILTER_LANCZOS, 1);
$save_image_link = 'imagick-resize-900x600.jpg';
$Imagick->setImageCompressionQuality(100);
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 2 from resized 1
$Imagick->resizeImage($new_width2, $new_height2, \Imagick::FILTER_LANCZOS, 1);
$save_image_link = 'imagick-resize-700x467.jpg';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 3 (crop)
$Imagick->cropImage($crop_width, $crop_height, 0, 0);
$save_image_link = 'imagick-crop-460x460.jpg';
$Imagick->setImageCompressionQuality(100);
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 4 (crop from original source)
$Imagick = new \Imagick();
$Imagick->readImage(realpath($source_image_jpg));// same as new \Imagick(realpath($source_image_jpg));
$Imagick->cropImage($crop_width, $crop_height, 0, 0);
$save_image_link = 'imagick-crop-460x460-from-original-source.jpg';
$Imagick->setImageCompressionQuality(100);
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 5 (rotate)
// @link check out this http://php.net/manual/en/imagick.rotateImage.php
$Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise(90));
$save_image_link = 'imagick-crop-460x460-rotate.jpg';
$Imagick->setImageCompressionQuality(100);
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 6 (rotate from original source)
$Imagick = new \Imagick(realpath($source_image_jpg));
$Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise(270));
$save_image_link = 'imagick-crop-1920x1281-rotate-from-original-source.jpg';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 7 (save to .png)
$Imagick = new \Imagick(realpath($source_image_jpg));
$Imagick->resizeImage($new_width1, $new_height1, \Imagick::FILTER_LANCZOS, 1);
$save_image_link = 'imagick-resize-900x600-from-jpg.png';
// png compression for imagick does not work!!!
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($processed_images_fullpath.$save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";

// resize 8 (save to .gif)
$save_image_link = 'imagick-resize-900x600-from-jpg.gif';
$save_result = $Imagick->writeImage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($processed_images_fullpath.$save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";
include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
