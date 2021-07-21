<?php
$sourceImageFile = '../source-images/city-amsterdam.gif';
$watermarkImageFile = '../source-images/watermark.png';
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
        $imgDestinationObject = imagecreatefrompng($watermarkImageFile);
        // -----------------------------------------------------------------------------------------------


        $imgSourceObject = imagecreatefromgif($sourceImageFile);
        // for set source gif image transparency after imagecreatefromgif() function.----------
        imagesavealpha($imgSourceObject, true);// added for transparency gif
        // -----------------------------------------------------------------------------------------------


        // copy watermark for source gif image and png watermark -----------------------------
        $watermarkCanvas = imagecreatetruecolor($wm_width, $wm_width);
        imagecopy($watermarkCanvas, $imgSourceObject, 0, 0, 100, 200, $wm_width, $wm_height);
        imagecopy($watermarkCanvas, $imgDestinationObject, 0, 0, 0, 0, $wm_width, $wm_height);
        imagecopymerge($imgSourceObject, $watermarkCanvas, 100, 200, 0, 0, $wm_width, $wm_height, 100);
        // -----------------------------------------------------------------------------------------------


        $saveImageLink = '../processed-images/' . basename(__FILE__, '.php') . '.gif';
        $saveResult = imagegif($imgSourceObject, $saveImageLink);

        imagedestroy($imgDestinationObject);
        imagedestroy($imgSourceObject);
        unset($imgDestinationObject, $imgSourceObject, $watermarkCanvas);
        unset($height, $width, $wm_height, $wm_width);
        ?>
        Image that applied watermark.
        <a href="<?=$saveImageLink; ?>"><img class="thumbnail" src="<?=$saveImageLink; ?>" alt=""></a> (position top left)<br>
        Save result: <?php echo var_export($saveResult, true); ?> 
        <hr>
        <?php 
        unset($sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>