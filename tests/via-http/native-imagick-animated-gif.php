<?php
$sourceImageFile = '../source-images/city-amsterdam-animated.gif';
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
        <?php
        $frames = isAnimatedGif($sourceImageFile);
        if ($frames > 1) {
            echo '<p>This is animated GIF.</p>';
        } else {
            echo '<div class="alert">This is NOT animated GIF.</div>';
        }
        unset($frames);
        ?> 
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

                        // resize on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . (isset($previousSize[0]) ? '-from-' . $previousSize[0] . 'x' . $previousSize[1] : '') . '.gif';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // crop on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->cropImage($cropWidth, $cropHeight, 0, 0);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-' . $previousSize[0] . 'x' . $previousSize[1] . '.gif';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // crop on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->cropImage($cropWidth, $cropHeight, 190, 120);// change crop position to see animated gif text.
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // rotate on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($deg));
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // rotate on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($deg));
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-source-image.gif';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // resize on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            $i = 1;
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1) {
                                    $ImagickFirst = $Frame->getImage();// get only first frame.
                                }
                                $i++;
                            }
                            unset($i);
                        }
                        unset($Frame);
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // convert transparency gif to white before save to another extension
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                        $Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-gif.png';
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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
                        // resize on animated gif needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            $i = 1;
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($size[0], $size[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1) {
                                    $ImagickFirst = $Frame->getImage();// get only first frame.
                                }
                                $i++;
                            }
                            unset($i);
                        }
                        unset($Frame);
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // convert transparency gif to white before save to another extension
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white. for GIF source
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white. for GIF source
                        $Imagick = $Imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);// convert from transparent to white. for GIF source
                        // save
                        $saveImgLink = basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-gif.jpg';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// writeImages() for animated gif.
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