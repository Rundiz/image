<?php
$sourceImageFile = '../source-images/city-amsterdam.gif';
$watermarkImageFile = '../source-images/watermark.png';
$processImagesFolder = '../processed-images/';
$processImagesFullpath = realpath($processImagesFolder) . DIRECTORY_SEPARATOR;

require_once __DIR__.'/include-imagick-functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <p>Original image <a href="<?=$sourceImageFile; ?>"><img class="thumbnail" src="<?=$sourceImageFile; ?>" alt=""></a></p>
        <p>Watermark image <a href="<?=$watermarkImageFile; ?>"><img class="thumbnail" src="<?=$watermarkImageFile; ?>" alt=""></a></p>
        <hr>
        <?php
        list($width, $height) = getimagesize($sourceImageFile);


        // watermark png ----------------------------------------------------------------------------
        list($wm_width, $wm_height) = getimagesize($watermarkImageFile);
        $ImagickWatermark = new \Imagick(realpath($watermarkImageFile));
        // -----------------------------------------------------------------------------------------------


        $Imagick = new \Imagick(realpath($sourceImageFile));


        // copy watermark for source gif image and png watermark -----------------------------
        $Imagick->compositeImage($ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, 100, 200);
        // -----------------------------------------------------------------------------------------------


        $saveImageLink = 'imagick-source-gif-watermarkimage-png.gif';
        $saveResult = $Imagick->writeimage($processImagesFullpath . $saveImageLink);

        $Imagick->clear();
        $ImagickWatermark->clear();
        unset($Imagick, $ImagickWatermark);
        ?>
        Image that applied watermark.
        <a href="<?=$processImagesFolder . $saveImageLink; ?>"><img class="thumbnail" src="<?=$processImagesFolder . $saveImageLink; ?>" alt=""></a> (position top left)<br>
        Save result: <?php echo var_export($saveResult, true); ?> 
        <hr>
        <?php 
        unset($sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>