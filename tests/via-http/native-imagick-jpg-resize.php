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
$save_image_link = '../processed-images/imagick-resize-900x600.jpg';
$save_result = imagejpeg($image_destination_object, $save_image_link, 100);
var_dump($save_result);
echo '<a href="'.$save_image_link.'">resized image</a>'."\n";
echo '<hr>'."\n\n";


include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';