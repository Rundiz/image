<?php
$sourceImageFile = '../source-images/source-image.png';
$processImagesFolder = '../processed-images/';
$processImagesFullpath = realpath($processImagesFolder) . DIRECTORY_SEPARATOR;

include_once 'includes/include-functions.php';
require_once 'includes/include-imagick-functions.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Native PHP Imagick class</h1>
        <hr>
        <table>
            <thead>
                <tr>
                    <th>Action</th>
                    <th>From</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Original image</th>
                    <td>Source</td>
                    <td>
                        <?php
                        debugImage($sourceImageFile);
                        $Imagick = new \Imagick(realpath($sourceImageFile));// Imagick can't read relative path.
                        ?> 
                    </td>
                </tr>

                <!-- resize -->
                <tr>
                    <th>Resize</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $newDimension = [900, 600];
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize</th>
                    <td>Previous processed</td>
                    <td>
                        <?php
                        // inherit previous processed.
                        $previousDimension = $newDimension;
                        unset($newDimension);

                        $newDimension = [700, 467];
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        ?> 
                    </td>
                </tr>

                <!-- crop -->
                <tr>
                    <th>Crop</th>
                    <td>Previous processed</td>
                    <td>
                        <?php
                        // inherit previous processed.
                        $previousDimension = $newDimension;
                        unset($newDimension);

                        $newDimension = [460, 460];
                        $Imagick->cropImage($newDimension[0], $newDimension[1], 0, 0);
                        $saveImgLink = autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Crop</th>
                    <td>Source</td>
                    <td>
                        <?php
                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);

                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $newDimension = [460, 460];
                        $Imagick->cropImage($newDimension[0], $newDimension[1], 0, 0);
                        $saveImgLink = autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        ?> 
                    </td>
                </tr>

                <!-- rotate -->
                <tr>
                    <th>Rotate</th>
                    <td>Previous processed</td>
                    <td>
                        <?php
                        // inherit previous processed.
                        $previousDimension = $newDimension;
                        unset($newDimension);

                        $rotate = 90;
                        $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($rotate));
                        $saveImgLink = autoImageFilename() . '-rotate-' . $rotate . '-from-crop-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        unset($rotate);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Rotate</th>
                    <td>Source</td>
                    <td>
                        <?php
                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);

                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $rotate = 270;
                        $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($rotate));
                        $saveImgLink = autoImageFilename() . '-rotate-' . $rotate . '-from-source' . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        unset($rotate);
                        ?> 
                    </td>
                </tr>

                <!-- flip -->
                <tr>
                    <th>Flip</th>
                    <td>Previous processed</td>
                    <td>
                        <?php
                        $flip = 'horizontal';
                        $Imagick->flopImage();
                        $saveImgLink = autoImageFilename() . '-flip-' . $flip . '-from-rotate-' . 270 . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        unset($rotate);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Flip</th>
                    <td>Source</td>
                    <td>
                        <?php
                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);

                        $Imagick = new \Imagick(realpath($sourceImageFile));
                        $flip = 'horizontal';
                        $Imagick->flopImage();
                        $Imagick->flipImage();
                        $saveImgLink = autoImageFilename() . '-flip-' . $flip . '-from-source' . '.png';
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        unset($rotate);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>

                <!-- resize & save as -->
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $newDimension = [800, 534];
                        $saveAs = 'avif';
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $Imagick->setCompressionQuality(100);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $newDimension = [800, 534];
                        $saveAs = 'gif';
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $saveAs = 'jpg';
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        // Convert transparency PNG to white before save to another extension. Without this, the transparent part will become black.
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white.
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white.
                        // Or you may use `$Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);` instead.
                        // End convert transparency PNG to white.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $saveAs = 'png';
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $Imagick->setCompressionQuality(05);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $Imagick = new \Imagick(realpath($sourceImageFile));

                        $saveAs = 'webp';
                        $Imagick->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        unset($Imagick, $previousDimension);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        unset($newDimension, $previousDimension);
        ?> 
        <hr>
        <?php 
        unset($processImagesFolder, $processImagesFullpath, $sourceImageFile);
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>