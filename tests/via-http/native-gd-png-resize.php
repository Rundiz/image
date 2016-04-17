<?php
$source_image_png = '../source-images/city-amsterdam.png';
echo '<a href="'.$source_image_png.'">original image</a>'."\n";
echo '<hr>'."\n\n";


list($width, $height) = getimagesize($source_image_png);

$new_width1 = 900;
$new_height1 = 600;

$new_width2 = 700;
$new_height2 = 467;

$crop_width = 460;
$crop_height = 460;

// resize 1
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefrompng($source_image_png);
// for set source png image transparency after imagecreatefrompng() function.
imagealphablending($image_source_object, false);// added for transparency png
imagesavealpha($image_source_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
// for transparency png before use imagecopyresampled() function.
imagealphablending($image_destination_object, false);// added for transparency png
imagesavealpha($image_destination_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600.png';
$save_result = imagepng($image_destination_object, $save_image_link, 0);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 2 from resized 1
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($new_width2, $new_height2);
// for transparency png before use imagecopyresampled() function.
imagealphablending($image_destination_object, false);// added for transparency png
imagesavealpha($image_destination_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width2, $new_height2, $new_width1, $new_height1);
$save_image_link = '../processed-images/gd-resize-700x467.png';
$save_result = imagepng($image_destination_object, $save_image_link, 0);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 3 (crop)
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($crop_width, $crop_height);
// for transparency png before use imagecopy() function.
$black = imagecolorallocate($image_destination_object, 0, 0, 0);// added for transparency png
$transwhite = imagecolorallocatealpha($image_destination_object, 255, 255, 255, 127);// added for transparency png
imagefill($image_destination_object, 0, 0, $transwhite);// added for transparency png. if not transparency png just use this fill no any alpha and color transparent function call.
imagecolortransparent($image_destination_object, $black);// added for transparency png
imagealphablending($image_destination_object, false);// added for transparency png
imagesavealpha($image_destination_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
imagecopy($image_destination_object, $image_source_object, 0, 0, 0, 0, $crop_width, $crop_height);
$save_image_link = '../processed-images/gd-crop-460x460.png';
$save_result = imagepng($image_destination_object, $save_image_link, 0);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 4 (rotate)
$image_destination_object = imagerotate($image_destination_object, 90, imagecolorallocate($image_destination_object, 255, 255, 255));
// for transparency png after use imagerotate() function.
imagealphablending($image_destination_object, false);// added for transparency png
imagesavealpha($image_destination_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
$save_image_link = '../processed-images/gd-crop-460x460-rotate.png';
$save_result = imagepng($image_destination_object, $save_image_link, 0);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 5 (save to .jpg)
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefrompng($source_image_png);
// for set source png image transparency after imagecreatefrompng() function.
imagealphablending($image_source_object, false);// added for transparency png
imagesavealpha($image_source_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
// for transparency png before use imagecopyresampled() function.
imagealphablending($image_destination_object, false);// added for transparency png
imagesavealpha($image_destination_object, true);// added for transparency png
// -----------------------------------------------------------------------------------------------
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600-from-png.jpg';
// for convert transparency png to white before save to another extension
$temp_image_object = imagecreatetruecolor($new_width1, $new_height1);// added for convert from transparency png to other
$white = imagecolorallocate($temp_image_object, 255, 255, 255);// added for convert from transparency png to other
imagefill($temp_image_object, 0, 0, $white);// added for convert from transparency png to other
imagecopy($temp_image_object, $image_destination_object, 0, 0, 0, 0, $new_width1, $new_height1);// added for convert from transparency png to other
$image_destination_object = $temp_image_object;
// -----------------------------------------------------------------------------------------------
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
//imagedestroy($temp_image_object);// added for convert from transparency png to other
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";

// resize 6 (save to .gif)
$save_image_link = '../processed-images/gd-resize-900x600-from-png.gif';
// for convert transparency png to white before save to another extension
$temp_image_object = imagecreatetruecolor($new_width1, $new_height1);// added for convert from transparency png to other
$white = imagecolorallocate($temp_image_object, 255, 255, 255);// added for convert from transparency png to other
imagefill($temp_image_object, 0, 0, $white);// added for convert from transparency png to other
imagecopy($temp_image_object, $image_destination_object, 0, 0, 0, 0, $new_width1, $new_height1);// added for convert from transparency png to other
$image_destination_object = $temp_image_object;
// -----------------------------------------------------------------------------------------------
$save_result = imagegif($image_destination_object, $save_image_link);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";
include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';