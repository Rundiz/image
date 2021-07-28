<?php
require_once 'include-rundiz-image.php';


$source_image_file = (isset($_GET['source_image_file']) ? $_GET['source_image_file'] : null);
$show_ext = (isset($_GET['show_ext']) ? $_GET['show_ext'] : '');
$act = (isset($_GET['act']) ? $_GET['act'] : 'resize');
$width = (isset($_GET['width']) ? intval($_GET['width']) : 100);
$height = (isset($_GET['height']) ? intval($_GET['height']) : 100);
$degree = (isset($_GET['degree']) ? $_GET['degree'] : 0);
$start_x = (isset($_GET['startx']) ? $_GET['startx'] : 0);
$start_y = (isset($_GET['starty']) ? $_GET['starty'] : 0);
$fontsize = (isset($_GET['fontsize']) ? $_GET['fontsize'] : 15);

if ($source_image_file == null) {
    die('Unable to load source image.');
}
if (!is_file($source_image_file)) {
    die('Source image is not exists.');
}
$Finfo = new finfo();
$sourceMimetype = $Finfo->file($source_image_file, FILEINFO_MIME_TYPE);
unset($Finfo);
if (stripos($sourceMimetype, 'image/') === false) {
    die('Source image is not an image file.');
}

if (strpos($show_ext, '.') !== false) {
    $show_ext = str_replace('.', '', $show_ext);
}
$show_ext = str_ireplace('jpeg', 'jpg', $show_ext);
$show_ext = strtolower($show_ext);

switch ($show_ext) {
    case 'gif':
        $mimetype = 'image/gif';
        break;
    case 'png':
        $mimetype = 'image/png';
        break;
    case 'webp':
        $mimetype = 'image/webp';
        break;
    case 'jpg':
    default:
        $mimetype = 'image/jpeg';
        break;
}

if ($width < 0) {
    $width = 100;
}
if ($height < 0) {
    $height = 100;
}

if (is_numeric($degree) && ($degree < 0 || $degree > 360)) {
    $degree = 0;
} elseif (is_numeric($degree)) {
    $degree = intval($degree);
} else {
    if ($degree != 'hor' && $degree != 'vrt' && $degree != 'horvrt') {
        $degree = 0;
    }
}

if (is_numeric($start_x)) {
    $start_x = intval($start_x);
}
if (is_numeric($start_y)) {
    $start_y = intval($start_y);
}

if (!is_numeric($fontsize)) {
    $fontsize = 15;
} else {
    $fontsize = intval($fontsize);
}


$Image = new \Rundiz\Image\Drivers\Gd($source_image_file);

switch ($act) {
    case 'watermarktext':
        $Image->watermarkText('Rundiz watermark สั้น ญู ให้ ทดสอบสระ.', '../source-images/font.ttf', $start_x, $start_y, $fontsize);
        break;
    case 'crop':
        $Image->crop($width, $height);
        break;
    case 'rotate':
        $Image->rotate($degree);
        break;
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

header('Content-type: ' . $mimetype);
$Image->show($show_ext);
$Image->clear();