<?php
require __DIR__.'/include-imagick-functions.php';

$source_image_gif = '../source-images/city-amsterdam.gif';
echo '<a href="'.$source_image_gif.'">original image</a>'."\n";
echo '<hr>'."\n\n";
$processed_images_folder = '../processed-images/';
$processed_images_fullpath = realpath($processed_images_folder).DIRECTORY_SEPARATOR;



list($width, $height) = getimagesize($source_image_gif);



// watermark png ----------------------------------------------------------------------------
$watermark_path = '../source-images/watermark.png';
echo '<a href="'.$watermark_path.'">watermark</a>';
list($wm_width, $wm_height) = getimagesize($watermark_path);
$ImagickWatermark = new \Imagick(realpath($watermark_path));
// -----------------------------------------------------------------------------------------------


$Imagick = new \Imagick(realpath($source_image_gif));


// copy watermark for source gif image and png watermark -----------------------------
$Imagick->compositeImage($ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, 100, 200);
// -----------------------------------------------------------------------------------------------


$save_image_link = 'imagick-source-gif-watermarkimage-png.gif';
$save_result = $Imagick->writeimage($processed_images_fullpath.$save_image_link);
$Imagick->clear();
$ImagickWatermark->clear();
var_dump($save_result);
echo '<a href="'.$processed_images_folder.$save_image_link.'">watermarked image</a>'."\n";
echo '<br>'."\n";
echo '<img src="'.$processed_images_folder.$save_image_link.'" alt="" style="width: 900px;">'."\n";
echo '<hr>'."\n\n";