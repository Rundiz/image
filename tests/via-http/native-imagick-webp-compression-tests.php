<?php
$sourceImageFile = '../source-images/city-amsterdam.webp';
$processImagesFolder = '../processed-images/';
$processImagesFullpath = realpath($processImagesFolder) . DIRECTORY_SEPARATOR;


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
    $ImagickCloned->setImageFormat('webp');
    $ImagickCloned->setImageCompressionQuality($compression);
    //$ImagickCloned->setCompressionQuality($compression);
    //$ImagickCloned->setOption('webp:method', $compression);
    //$ImagickCloned->setOption('webp:lossless', 'false');
    $saveImgLink = basename(__FILE__, '.php') . '-compression' . $compression . '.webp';
    $ImagickCloned->writeImage('webp:' . $processImagesFullpath . $saveImgLink);
    $ImagickCloned->clear();
    unset($ImagickCloned);
    clearstatcache();
    echo ' ';
    echo '<a href="' . $processImagesFolder . $saveImgLink . '">File size: </a>' . filesize($processImagesFullpath . $saveImgLink) . ' bytes.<br>' . PHP_EOL;
}// endfor;
unset($compression);

//echo '<hr>' . PHP_EOL;
//echo 'Compress all types tests<br>' . PHP_EOL;

//compressAllTypes($Imagick, $processImagesFullpath . 'compress-all-types-tests');

$Imagick->clear();
unset($Imagick);
