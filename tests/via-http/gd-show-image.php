<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';


$source_image_file = (isset($_GET['source_image_file']) ? $_GET['source_image_file'] : null);
$show_ext = (isset($_GET['show_ext']) ? $_GET['show_ext'] : '');
$act = (isset($_GET['act']) ? $_GET['act'] : 'resize');
$width = (isset($_GET['width']) ? intval($_GET['width']) : 100);
$height = (isset($_GET['height']) ? intval($_GET['height']) : 100);

if ($source_image_file == null) {
    die('Unable to load source image.');
}
if (!is_file($source_image_file)) {
    die('Source image is not exists.');
}
$image_data = getimagesize($source_image_file);
if (!is_array($image_data) || (is_array($image_data) && !array_key_exists('mime', $image_data))) {
    die('Source image is not an image file.');
}

if (strpos($show_ext, '.') !== false) {
    $show_ext = str_replace('.', '', $show_ext);
}
$show_ext = str_ireplace('jpeg', 'jpg', $show_ext);
$show_ext = strtolower($show_ext);
if ($show_ext != 'jpg' && $show_ext != 'gif' && $show_ext != 'png') {
    $show_ext = '';
}

if ($width < 0) {
    $width = 100;
}
if ($height < 0) {
    $height = 100;
}


$Image = new \Rundiz\Image\Drivers\Gd($source_image_file);

switch ($act) {
    case 'resizenoratio':
        $Image->resizeNoRatio($width, $height);
        break;
    case 'resize':
    default:
        $Image->resize($width, $height);
        break;
}

if ($Image->status === false) {
    die($Image->status_msg);
}

header('Content-type: '.$Image->source_image_data['mime']);
$Image->show($show_ext);
$Image->clear();