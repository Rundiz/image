<?php
$source_image_jpg = '../source-images/city-amsterdam.jpg';
echo '<a href="'.$source_image_jpg.'">original image</a>'."\n";
echo '<hr>'."\n\n";


list($width, $height) = getimagesize($source_image_jpg);

$new_width1 = 900;
$new_height1 = 600;

$new_width2 = 700;
$new_height2 = 467;

$crop_width = 460;
$crop_height = 460;

// resize 1
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefromjpeg($source_image_jpg);
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 2 from resized 1
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($new_width2, $new_height2);
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width2, $new_height2, $new_width1, $new_height1);
$save_image_link = '../processed-images/gd-resize-700x467.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";

// resize 3 (crop)
$image_source_object = $image_destination_object;
$image_destination_object = imagecreatetruecolor($crop_width, $crop_height);
imagecopy($image_destination_object, $image_source_object, 0, 0, 0, 0, $crop_width, $crop_height);
$save_image_link = '../processed-images/gd-crop-460x460.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 4 (crop from original source)
$image_destination_object = imagecreatetruecolor($crop_width, $crop_height);
$image_source_object = imagecreatefromjpeg($source_image_jpg);
imagecopy($image_destination_object, $image_source_object, 0, 0, 0, 0, $crop_width, $crop_height);
$save_image_link = '../processed-images/gd-crop-460x460-from-original-source.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">cropped image</a>'."\n";
echo '<hr>'."\n\n";

// resize 5 (rotate)
$image_destination_object = imagerotate($image_destination_object, 90, imagecolorallocate($image_destination_object, 255, 255, 255));
$save_image_link = '../processed-images/gd-crop-460x460-rotate.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 6 (rotate from original source)
$image_source_object = imagecreatefromjpeg($source_image_jpg);
$image_destination_object = imagerotate($image_source_object, 270, imagecolorallocate($image_source_object, 255, 255, 255));
$save_image_link = '../processed-images/gd-crop-1920x1281-rotate-from-original-source.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
imagedestroy($image_destination_object);
imagedestroy($image_source_object);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">rotated image</a>'."\n";
echo '<hr>'."\n\n";

// resize 7 (save to .png)
$image_destination_object = imagecreatetruecolor($new_width1, $new_height1);
$image_source_object = imagecreatefromjpeg($source_image_jpg);
imagecopyresampled($image_destination_object, $image_source_object, 0, 0, 0, 0, $new_width1, $new_height1, $width, $height);
$save_image_link = '../processed-images/gd-resize-900x600-from-jpg.png';
$save_result = imagepng($image_destination_object, $save_image_link, 0);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";

// resize 8 (save to .gif)
$save_image_link = '../processed-images/gd-resize-900x600-from-jpg.gif';
$save_result = imagegif($image_destination_object, $save_image_link);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
$image_data = getimagesize($save_image_link);
echo '<pre>'.print_r($image_data, true).'</pre>';
echo '<hr>'."\n\n";
include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';