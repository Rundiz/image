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
        $image_destination_object = imagecreatefrompng($watermarkImageFile);
        // -----------------------------------------------------------------------------------------------


        $image_source_object = imagecreatefromgif($sourceImageFile);
        // for set source gif image transparency after imagecreatefromgif() function.----------
        imagesavealpha($image_source_object, true);// added for transparency gif
        // -----------------------------------------------------------------------------------------------


        // copy watermark for source gif image and png watermark -----------------------------
        $cut_resource_object = imagecreatetruecolor($wm_width, $wm_width);
        imagecopy($cut_resource_object, $image_source_object, 0, 0, 100, 200, $wm_width, $wm_height);
        imagecopy($cut_resource_object, $image_destination_object, 0, 0, 0, 0, $wm_width, $wm_height);
        imagecopymerge($image_source_object, $cut_resource_object, 100, 200, 0, 0, $wm_width, $wm_height, 100);
        // -----------------------------------------------------------------------------------------------


        // original copy watermark (destination object) to image (source object) ---------------
        //imagealphablending($image_source_object, true);// add this for transparent watermark thru image.
        //imagecopy($image_source_object, $image_destination_object, 100, 200, 0, 0, $wm_width, $wm_height);
        // -----------------------------------------------------------------------------------------------


        $saveImageLink = '../processed-images/' . basename(__FILE__, '.php') . '-source-gif-watermark-png.gif';
        $saveResult = imagegif($image_source_object, $saveImageLink);
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