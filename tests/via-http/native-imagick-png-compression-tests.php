<?php
$sourceImageFile = '../source-images/city-amsterdam.png';
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

for ($compression = 0; $compression <= 9; $compression++) {
    echo 'Compression: ' . $compression . 5 . '.';
    $ImagickCloned = clone $Imagick;
    $ImagickCloned->setImageFormat('png');
    // @link https://stackoverflow.com/questions/9710118/convert-multipage-pdf-to-png-and-back-linux/12046542#12046542 PNG compression quality explain.
    $ImagickCloned->setCompressionQuality(intval($compression . 5));
    $saveImgLink = autoImageFilename() . '-compression' . $compression . '.png';
    $ImagickCloned->writeImage('png:' . $processImagesFullpath . $saveImgLink);
    $ImagickCloned->clear();
    unset($ImagickCloned);
    clearstatcache();
    echo ' ';
    echo '<a href="' . $processImagesFolder . $saveImgLink . '">File size: </a>' . filesize($processImagesFullpath . $saveImgLink) . ' bytes.<br>' . PHP_EOL;
}// endfor;
unset($compression);

$Imagick->clear();
unset($Imagick);

echo '<p><a href="https://stackoverflow.com/questions/9710118/convert-multipage-pdf-to-png-and-back-linux/12046542#12046542">Read more description</a></p>' . "\n";