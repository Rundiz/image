<?php
$sourceImageFile = '../source-images/city-amsterdam-animated.webp';
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
        <?php if (version_compare(PHP_VERSION, '7.3', '<')) { ?><p>
            PHP &lt; 7.3 does not supported animated WEBP.
        </p><?php } ?> 
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
                        $frames = isAnimated($sourceImageFile);
                        if ($frames > 1) {
                            echo '<p>This is animated WEBP.</p>';
                        } else {
                            echo '<p class="alert">This is NOT animated WEBP.</p>';
                        }
                        unset($frames);
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
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // crop on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->cropImage($newDimension[0], $newDimension[1], 0, 0);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end crop animated WEBP
                        $saveImgLink = autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // crop on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->cropImage($newDimension[0], $newDimension[1], 200, 150);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end crop animated WEBP
                        $saveImgLink = autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // rotate on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($rotate));
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end rotate animated WEBP
                        $saveImgLink = autoImageFilename() . '-rotate-' . $rotate . '-from-crop-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // rotate on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), calculateCounterClockwise($rotate));
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end rotate animated WEBP
                        $saveImgLink = autoImageFilename() . '-rotate-' . $rotate . '-from-source' . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // flip on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->flopImage();
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end flip animated WEBP
                        $saveImgLink = autoImageFilename() . '-flip-' . $flip . '-from-rotate-' . 270 . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // flip on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Imagick->flopImage();
                                $Imagick->flipImage();
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end flip animated WEBP
                        $saveImgLink = autoImageFilename() . '-flip-' . $flip . '-from-source' . '.webp';
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        $saveAs = 'gif';
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
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
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if (!isset($ImagickFirst)) {
                                    $ImagickFirst = $Frame->getImage();
                                }
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        // use first frame as image.
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // end use first frame as image.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-non-animated-' . $saveAs . '.' . $saveAs;
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
                        
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if (!isset($ImagickFirst)) {
                                    $ImagickFirst = $Frame->getImage();
                                }
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        // use first frame as image.
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // end use first frame as image.
                        // Convert transparency GIF to white before save to another extension. For GIF source.
                        $Imagick->setImageBackgroundColor('white');// convert from transparent to white.
                        $Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);// convert from transparent to white.
                        // End convert transparency GIF to white.
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
                        
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if (!isset($ImagickFirst)) {
                                    $ImagickFirst = $Frame->getImage();
                                }
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        // use first frame as image.
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // end use first frame as image.
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
                        
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $Imagick->setImageCompressionQuality(100);
                        $saveResult = $Imagick->writeImages($processImagesFullpath . $saveImgLink, true);// save animated WEBP need to use `writeImages()`.
                        debugImage($processImagesFolder . $saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        if (version_compare(PHP_VERSION, '7.3', '<')) {
                            echo '<div class="text-error">PHP prior 7.3 and Imagick does not supported create animated WEBP.</div>' . "\n";
                        }
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
                        
                        // resize on animated WEBP needs special step (coalesceImages()).
                        $Imagick = $Imagick->coalesceImages();
                        if (is_object($Imagick)) {
                            foreach ($Imagick as $Frame) {
                                $Frame->resizeImage($newDimension[0], $newDimension[1], \Imagick::FILTER_LANCZOS, 1);
                                $Frame->setImagePage(0, 0, 0, 0);
                                if (!isset($ImagickFirst)) {
                                    $ImagickFirst = $Frame->getImage();
                                }
                            }
                        }
                        unset($Frame);
                        // end resize animated WEBP.
                        // use first frame as image.
                        if (isset($ImagickFirst)) {
                            $Imagick->clear();
                            $Imagick = $ImagickFirst;// use first frame as an image.
                            unset($ImagickFirst);
                        }
                        // end use first frame as image.
                        $saveImgLink = autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-non-animated-' . $saveAs . '.' . $saveAs;
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