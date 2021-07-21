<?php
$sourceImageFile = '../source-images/city-amsterdam.gif';
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
        Original image <a href="<?=$sourceImageFile; ?>"><img class="thumbnail" src="<?=$sourceImageFile; ?>" alt=""></a>
        <hr>
        <table>
            <tbody>
                <tr>
                    <th>Resize</th>
                    <?php
                    $sizes = [
                        [900, 600],
                        [700, 467],
                    ];
                    $previousSize = [];
                    foreach ($sizes as $size) {
                        if (!isset($Imagick) || !is_object($Imagick)) {
                            $Imagick = new \Imagick(realpath($sourceImageFile));
                        }

                        // resize
                        $Imagick->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . (isset($previousSize[0]) ? '-from-' . $previousSize[0] . 'x' . $previousSize[1] : '') . '.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<td>' . PHP_EOL;
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        echo '</td>' . PHP_EOL;

                        // set previous size and object.
                        $previousSize[0] = $size[0];
                        $previousSize[1] = $size[1];

                        unset($saveImgLink, $saveResult);
                    }// endforeach;
                    //imagedestroy($imgDestinationObject);
                    unset($size, $sizes);
                    ?>
                </tr>
                <tr>
                    <th>Crop</th>
                    <td>
                        <?php
                        $cropWidth = 460;
                        $cropHeight = 460;

                        // crop from previous resized image.
                        // crop normal gif that is NOT animated gif. For animated gif, please look at native-imagick-animated-gif.php file.
                        $Imagick->cropImage($cropWidth, $cropHeight, 0, 0);
                        $Imagick->setImagePage(0, 0, 0, 0);// required to crop NON animated gif
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-' . $previousSize[0] . 'x' . $previousSize[1] . '.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $cropWidth . 'x' . $cropHeight . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        $Imagick->clear();
                        unset($Imagick, $saveImgLink, $saveResult);
                        unset($previousSize);
                        ?> 
                    </td>
                    <td>
                        <?php
                        // crop from source image.
                        $Imagick = new \Imagick();
                        $Imagick->readImage(realpath($sourceImageFile));// same as new \Imagick(realpath($sourceImageFile));
                        $Imagick->cropImage($cropWidth, $cropHeight, 0, 0);
                        $Imagick->setImagePage(0, 0, 0, 0);// required to crop NON animated gif
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $cropWidth . 'x' . $cropHeight . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Rotate</th>
                    <td>
                        <?php
                        // rotate from previous cropped.
                        $deg = 90;
                        // rotate
                        // @link https://www.php.net/manual/en/imagick.rotateimage.php check out this
                        $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($deg));
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $cropWidth . 'x' . $cropHeight . ' rotate ' . $deg . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        $Imagick->clear();
                        unset($Imagick, $saveImgLink, $saveResult);
                        unset($cropHeight, $cropWidth);
                        ?> 
                    </td>
                    <td>
                        <?php
                        // rotate from source image.
                        $deg = 270;
                        $Imagick = new \Imagick(realpath($sourceImageFile));
                        // rotate
                        $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($deg));
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        list($width, $height) = getimagesize($processImagesFolder . $saveImgLink);
                        echo $width . 'x' . $height . ' rotate ' . $deg . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);
                        unset($deg, $height, $width);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save to extensions</th>
                    <td>
                        <?php
                        $size = [900, 600];
                        list($width, $height) = getimagesize($sourceImageFile);
                        $Imagick = new \Imagick(realpath($sourceImageFile));
                        // resize
                        $Imagick->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                        // convert transparency gif to white before save to another extension
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                        $Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-gif.png';
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . '<br>' . PHP_EOL;
                        $Finfo = new finfo();
                        echo 'Mime type using <code>finfo()</code>: ' . $Finfo->file($processImagesFolder . $saveImgLink, FILEINFO_MIME_TYPE) . '<br>' . PHP_EOL;
                        $Imagick->clear();
                        unset($Finfo, $Imagick, $saveImgLink, $saveResult);
                        unset($height, $width);
                        ?> 
                    </td>
                    <td>
                        <?php
                        list($width, $height) = getimagesize($sourceImageFile);
                        $Imagick = new \Imagick(realpath($sourceImageFile));
                        // resize
                        $Imagick->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                        // convert transparency gif to white before save to another extension
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                        $Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-gif.jpg';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImage($processImagesFullpath . $saveImgLink);
                        echo '<a href="' . $processImagesFolder  . $saveImgLink . '"><img class="thumbnail" src="' . $processImagesFolder  . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . '<br>' . PHP_EOL;
                        $Finfo = new finfo();
                        echo 'Mime type using <code>finfo()</code>: ' . $Finfo->file($processImagesFolder . $saveImgLink, FILEINFO_MIME_TYPE) . '<br>' . PHP_EOL;
                        $Imagick->clear();
                        unset($Finfo, $Imagick, $saveImgLink, $saveResult);
                        unset($height, $width, $size);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <?php 
        unset($processImagesFolder, $processImagesFullpath, $sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>