<?php
$source_image_gif = '../source-images/city-amsterdam.gif';
echo '<a href="'.$source_image_gif.'">original image</a>'."\n";
echo '<hr>'."\n\n";



list($width, $height) = getimagesize($source_image_gif);



// watermark png ----------------------------------------------------------------------------
$watermark_path = '../source-images/watermark.png';
echo '<a href="'.$watermark_path.'">watermark</a>';
list($wm_width, $wm_height) = getimagesize($watermark_path);
$image_destination_object = imagecreatefrompng($watermark_path);
// -----------------------------------------------------------------------------------------------


$image_source_object = imagecreatefromgif($source_image_gif);
// for set source gif image transparency after imagecreatefromgif() function.----------
imagesavealpha($image_source_object, true);// added for transparency gif
// -----------------------------------------------------------------------------------------------


// copy watermark for source gif image and png watermark -----------------------------
$cut_resource_object = imagecreatetruecolor($wm_width, $wm_width);
imagecopy($cut_resource_object, $image_source_object, 0, 0, 100, 200, $wm_width, $wm_height);
imagecopy($cut_resource_object, $image_destination_object, 0, 0, 0, 0, $wm_width, $wm_height);
imagecopymerge($image_source_object, $cut_resource_object, 100, 200, 0, 0, $wm_width, $wm_height, 100);
// -----------------------------------------------------------------------------------------------


// original copy watermark (destination object) to image (source object) ---------------
//imagealphablending($image_source_object, true);// add this for transparent watermark thru image.
//imagecopy($image_source_object, $image_destination_object, 100, 200, 0, 0, $wm_width, $wm_height);
// -----------------------------------------------------------------------------------------------


$save_image_link = '../processed-images/gd-source-gif-watermarkimage-png.gif';
$save_result = imagegif($image_source_object, $save_image_link);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">watermarked image</a>'."\n";
echo '<hr>'."\n\n";