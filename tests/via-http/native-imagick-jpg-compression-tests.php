<?php
$sourceImageFile = '../source-images/source-image.jpg';
$processImagesFolder = '../processed-images/';
$processImagesFullpath = realpath($processImagesFolder) . DIRECTORY_SEPARATOR;


include_once 'includes/include-functions.php';

echo '<p>';
echo 'Original <a href="' . $sourceImageFile . '">image</a><br>';
clearstatcache();
echo 'File size: ' . filesize($sourceImageFile) . ' bytes';
echo '</p>' . PHP_EOL;

echo '<hr>' . PHP_EOL;

$Imagick = new Imagick(realpath($sourceImageFile));

for ($compression = 0; $compression <= 100; $compression += 10) {
    echo 'Compression: ' . $compression . '.';
    $ImagickCloned = clone $Imagick;
    $ImagickCloned->setImageFormat('jpg');
    $ImagickCloned->setImageCompressionQuality($compression);
    $saveImgLink = autoImageFilename() . '-compression' . $compression . '.jpg';
    $ImagickCloned->writeImage($processImagesFullpath . $saveImgLink);
    $ImagickCloned->clear();
    unset($ImagickCloned);
    clearstatcache();
    echo ' ';
    echo '<a href="' . $processImagesFolder . $saveImgLink . '">File size: </a>' . filesize($processImagesFullpath . $saveImgLink) . ' bytes.<br>' . PHP_EOL;
}// endfor;
unset($compression);

$Imagick->clear();
unset($Imagick);