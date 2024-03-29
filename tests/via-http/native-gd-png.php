<?php
$sourceImageFile = '../source-images/city-amsterdam.png';
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
                    $previousSourceObject = null;
                    foreach ($sizes as $size) {
                        if (empty($previousSize)) {
                            $previousSize = getimagesize($sourceImageFile);
                        }

                        if (is_null($previousSourceObject)) {
                            $imgDestinationObject = imagecreatetruecolor($size[0], $size[1]);
                            $imgSourceObject = imagecreatefrompng($sourceImageFile);
                            // for set source png image transparency after imagecreatefrompng() function.
                            imagealphablending($imgSourceObject, false);// added for transparency png
                            imagesavealpha($imgSourceObject, true);// added for transparency png
                        } else {
                            $imgSourceObject = $previousSourceObject;
                            $imgDestinationObject = imagecreatetruecolor($size[0], $size[1]);
                        }

                        // for transparency png before use imagecopyresampled() function.
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        // resize from previous resized file. if not found, resize from source file.
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $previousSize[0], $previousSize[1]);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . (!empty($previousSourceObject) ? '-from-' . $previousSize[0] . 'x' . $previousSize[1] : '') . '.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        echo '<td>' . PHP_EOL;
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        echo '</td>' . PHP_EOL;

                        // set previous size and object.
                        $previousSize[0] = $size[0];
                        $previousSize[1] = $size[1];
                        $previousSourceObject = $imgDestinationObject;

                        imagedestroy($imgSourceObject);
                        unset($imgSourceObject, $saveImgLink, $saveResult);
                    }// endforeach;
                    //imagedestroy($imgDestinationObject);
                    unset($imgDestinationObject, $size, $sizes);
                    ?>
                </tr>
                <tr>
                    <th>Crop</th>
                    <td>
                        <?php
                        $cropWidth = 460;
                        $cropHeight = 460;

                        // crop from previous resized image.
                        $imgSourceObject = $previousSourceObject;
                        $imgDestinationObject = imagecreatetruecolor($cropWidth, $cropHeight);
                        // for transparency png before use imagecopy() function.
                        $black = imagecolorallocate($imgDestinationObject, 0, 0, 0);// added for transparency png
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency png
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency png. if not transparency png just use this fill no any alpha and color transparent function call.
                        imagecolortransparent($imgDestinationObject, $black);// added for transparency png
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        // imagecopy (crop)
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $cropWidth, $cropHeight);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-' . $previousSize[0] . 'x' . $previousSize[1] . '.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $cropWidth . 'x' . $cropHeight . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        imagedestroy($imgSourceObject);
                        imagedestroy($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject, $previousSourceObject, $saveImgLink, $saveResult);
                        unset($previousSize);
                        ?> 
                    </td>
                    <td>
                        <?php
                        // crop from source image.
                        $imgDestinationObject = imagecreatetruecolor($cropWidth, $cropHeight);
                        $imgSourceObject = imagecreatefrompng($sourceImageFile);
                        // for set source png image transparency after imagecreatefrompng() function.
                        imagealphablending($imgSourceObject, false);// added for transparency png
                        imagesavealpha($imgSourceObject, true);// added for transparency png
                        // for transparency png before use imagecopy() function.
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        // imagecopy (crop)
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $cropWidth, $cropHeight);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
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
                        // imagerotate()
                        $imgDestinationObject = imagerotate($imgDestinationObject, $deg, imagecolorallocate($imgDestinationObject, 255, 255, 255));
                        // for transparency png after use imagerotate() function.
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $cropWidth . 'x' . $cropHeight . ' rotate ' . $deg . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        imagedestroy($imgDestinationObject);
                        imagedestroy($imgSourceObject);
                        unset($imgDestinationObject, $imgSourceObject, $saveImgLink, $saveResult);
                        unset($cropHeight, $cropWidth);
                        ?> 
                    </td>
                    <td>
                        <?php
                        // rotate from source image.
                        $deg = 270;
                        $imgSourceObject = imagecreatefrompng($sourceImageFile);
                        // for set source png image transparency after imagecreatefrompng() function.
                        imagealphablending($imgSourceObject, false);// added for transparency png
                        imagesavealpha($imgSourceObject, true);// added for transparency png
                        // imagerotate()
                        $imgDestinationObject = imagerotate($imgSourceObject, $deg, imagecolorallocate($imgSourceObject, 255, 255, 255));
                        // for transparency png after use imagerotate() function.
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-source-image.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        list($width, $height) = getimagesize($saveImgLink);
                        echo $width . 'x' . $height . ' rotate ' . $deg . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        imagedestroy($imgDestinationObject);
                        imagedestroy($imgSourceObject);
                        unset($imgDestinationObject, $imgSourceObject, $saveImgLink, $saveResult);
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
                        $imgDestinationObject = imagecreatetruecolor($size[0], $size[1]);
                        $imgSourceObject = imagecreatefrompng($sourceImageFile);
                        // for set source png image transparency after imagecreatefrompng() function.
                        imagealphablending($imgSourceObject, false);// added for transparency png
                        imagesavealpha($imgSourceObject, true);// added for transparency png
                        // for transparency png before use imagecopyresampled() function.
                        imagealphablending($imgDestinationObject, false);// added for transparency png
                        imagesavealpha($imgDestinationObject, true);// added for transparency png
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
                        // for convert transparency png to white before save to another extension
                        $tmpImgObject = imagecreatetruecolor($size[0], $size[1]);// added for convert from transparency png to other
                        $white = imagecolorallocate($tmpImgObject, 255, 255, 255);// added for convert from transparency png to other
                        imagefill($tmpImgObject, 0, 0, $white);// added for convert from transparency png to other
                        imagecopy($tmpImgObject, $imgDestinationObject, 0, 0, 0, 0, $size[0], $size[1]);// added for convert from transparency png to other
                        $imgDestinationObject = $tmpImgObject;
                        // save
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-png.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . '<br>' . PHP_EOL;
                        $Finfo = new finfo();
                        echo 'Mime type using <code>finfo()</code>: ' . $Finfo->file($saveImgLink, FILEINFO_MIME_TYPE) . '<br>' . PHP_EOL;
                        imagedestroy($imgDestinationObject);
                        imagedestroy($imgSourceObject);
                        unset($Finfo, $imgDestinationObject, $imgSourceObject, $saveImgLink, $saveResult);
                        unset($height, $width);
                        ?> 
                    </td>
                    <td>
                        <?php
                        list($width, $height) = getimagesize($sourceImageFile);
                        $imgDestinationObject = imagecreatetruecolor($size[0], $size[1]);
                        $imgSourceObject = imagecreatefrompng($sourceImageFile);
                        // for set source png image transparency after imagecreatefrompng() function.
                        imagealphablending($imgSourceObject, false);// added for transparency png
                        imagesavealpha($imgSourceObject, true);// added for transparency png
                        // for convert transparency png to gif before use imagecopyresampled() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for convert to transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for convert to transparency gif
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for convert to transparency gif
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
                        // save
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-png.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . '<br>' . PHP_EOL;
                        $Finfo = new finfo();
                        echo 'Mime type using <code>finfo()</code>: ' . $Finfo->file($saveImgLink, FILEINFO_MIME_TYPE) . '<br>' . PHP_EOL;
                        imagedestroy($imgDestinationObject);
                        imagedestroy($imgSourceObject);
                        unset($Finfo, $imgDestinationObject, $imgSourceObject, $saveImgLink, $saveResult, $tmpImgObject);
                        unset($height, $width, $size);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <?php 
        unset($black, $transwhite);
        unset($sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>