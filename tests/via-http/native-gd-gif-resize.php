<?php
$source_image_gif = '../source-images/city-amsterdam.gif';
echo '<a href="'.$source_image_gif.'">original image</a>'."\n";
echo '<hr>'."\n\n";


list($width, $height) = getimagesize($source_image_gif);

$new_width1 = 900;
$new_height1 = 600;

$new_width2 = 700;
$new_height2 = 467;

$crop_width = 460;
$crop_height = 460;

// resize 1
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefromgif($source_image_gif);
// for set source gif image transparency after imagecreatefromgif() function.
imagesavealpha($image_source_object, true);// added for transparency gif
// -----------------------------------------------------------------------------------------------
// for transparency gif before use imagecopyresampled() function.
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);// added for transparency gif
imagefill($image_destination_object, 0, 0, $transwhite);// added for transparency gif
imagecolortransparent($image_destination_object, $transwhite);// added for transparency gif
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600.gif';
$save_result = imagegif($image_destination_object, $save_image_link);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 2 from resized 1
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($new_width2, $new_height2);
// for transparency gif before use imagecopyresampled() function.
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);// added for transparency gif
imagefill($image_destination_object, 0, 0, $transwhite);// added for transparency gif
imagecolortransparent($image_destination_object, $transwhite);// added for transparency gif
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width2, $new_height2, $new_width1, $new_height1);
$save_image_link = '../processed-images/gd-resize-700x467.gif';
$save_result = imagegif($image_destination_object, $save_image_link);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 3 (crop)
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($crop_width, $crop_height);
// for transparency gif before use imagecopy() function.
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);// added for transparency gif
imagefill($image_destination_object, 0, 0, $transwhite);// added for transparency gif. if not transparency png just use this fill no any alpha and color transparent function call.
imagecolortransparent($image_destination_object, $transwhite);// added for transparency gif
// -----------------------------------------------------------------------------------------------
imagecopy($image_destination_object, $image_source_object, 0, 0, 0, 0, $crop_width, $crop_height);
$save_image_link = '../processed-images/gd-crop-460x460.gif';
$save_result = imagegif($image_destination_object, $save_image_link);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 4 (rotate)
$image_destination_object = imagerotate($image_destination_object, 90, imagecolorallocate($image_destination_object, 255, 255, 255));
$save_image_link = '../processed-images/gd-crop-460x460-rotate.gif';
$save_result = imagegif($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 5 (rotate from original source)
$image_source_object = imagecreatefromgif($source_image_gif);
// for transparency gif before rotate.
$image_destination_object = imagecreatetruecolor(1920, 1281);
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);
imagefill($image_destination_object, 0, 0, $transwhite);
imagecolortransparent($image_destination_object, $transwhite);
imagecopy($image_destination_object, $image_source_object, 0, 0, 0, 0, 1920, 1281);
// -----------------------------------------------------------------------------------------------
$image_destination_object = imagerotate($image_destination_object, 270, $transwhite);
$save_image_link = '../processed-images/gd-crop-1920x1281-rotate-from-original-source.gif';
$save_result = imagegif($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 6 (save to .png)
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefromgif($source_image_gif);
// for set source gif image transparency after imagecreatefromgif() function.
imagesavealpha($image_source_object, true);// added for transparency gif
// -----------------------------------------------------------------------------------------------
// for transparency gif before use imagecopyresampled() function.
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);// added for transparency gif
imagefill($image_destination_object, 0, 0, $transwhite);// added for transparency gif
imagecolortransparent($image_destination_object, $transwhite);// added for transparency gif
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600-from-gif.png';
// for convert transparency gif to white before save to another extension
$temp_image_object = imagecreatetruecolor($new_width1, $new_height1);// added for convert from transparency png to other
$white = imagecolorallocate($temp_image_object, 255, 255, 255);// added for convert from transparency png to other
imagefill($temp_image_object, 0, 0, $white);// added for convert from transparency png to other
imagecopy($temp_image_object, $image_destination_object, 0, 0, 0, 0, $new_width1, $new_height1);// added for convert from transparency png to other
$image_destination_object = $temp_image_object;
// -----------------------------------------------------------------------------------------------
$save_result = imagepng($image_destination_object, $save_image_link, 0);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";

// resize 7 (save to .jpg)
$save_image_link = '../processed-images/gd-resize-900x600-from-gif.jpg';
// for convert transparency gif to white before save to another extension
$temp_image_object = imagecreatetruecolor($new_width1, $new_height1);// added for convert from transparency png to other
$white = imagecolorallocate($temp_image_object, 255, 255, 255);// added for convert from transparency png to other
imagefill($temp_image_object, 0, 0, $white);// added for convert from transparency png to other
imagecopy($temp_image_object, $image_destination_object, 0, 0, 0, 0, $new_width1, $new_height1);// added for convert from transparency png to other
$image_destination_object = $temp_image_object;
// -----------------------------------------------------------------------------------------------
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";
include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';