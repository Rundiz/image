<?php
$sourceImageFile = '../source-images/source-image.gif';
$watermarkImageFile = '../source-images/watermark.png';
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
            <tbody>
                <tr>
                    <th>
                        Source image
                    </th>
                    <td>
                        <?php
                        debugImage($sourceImageFile);
                        $Imagick = new \Imagick(realpath($sourceImageFile));
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>
                        Watermark
                    </th>
                    <td>
                        <?php
                        debugImage($watermarkImageFile);
                        $ImagickWatermark = new \Imagick(realpath($watermarkImageFile));
                        $wmPosition = [180, 200];
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>
                        Result
                    </th>
                    <td>
                        <?php
                        $Imagick->compositeImage($ImagickWatermark, \Imagick::COMPOSITE_DEFAULT, $wmPosition[0], $wmPosition[1]);

                        $saveImgLink = autoImageFilename() . '.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        debugImage($processImagesFolder . $saveImgLink, ['imgClass' => 'img-fluid thumbnail larger']);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        unset($Imagick, $ImagickWatermark);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <?php 
        unset($sourceImageFile, $watermarkImageFile, $processImagesFolder, $processImagesFullpath);
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>