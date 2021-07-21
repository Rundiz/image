<?php
$sourceImageFile = '../source-images/city-amsterdam.gif';
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
                            $imgSourceObject = imagecreatefromgif($sourceImageFile);
                            // for set source gif image transparency after imagecreatefromgif() function.
                            imagesavealpha($imgSourceObject, true);// added for transparency gif
                        } else {
                            $imgSourceObject = $previousSourceObject;
                            $imgDestinationObject = imagecreatetruecolor($size[0], $size[1]);
                        }

                        // for transparency gif before use imagecopyresampled() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency gif
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for transparency gif
                        // resize from previous resized file. if not found, resize from source file.
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $previousSize[0], $previousSize[1]);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . (!empty($previousSourceObject) ? '-from-' . $previousSize[0] . 'x' . $previousSize[1] : '') . '.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
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
                        // for transparency gif before use imagecopy() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency gif. if not transparency png just use this fill no any alpha and color transparent function call.
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for transparency gif
                        // imagecopy (crop)
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $cropWidth, $cropHeight);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-' . $previousSize[0] . 'x' . $previousSize[1] . '.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
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
                        $imgSourceObject = imagecreatefromgif($sourceImageFile);
                        // for set source gif image transparency after imagecreatefromgif() function.
                        imagesavealpha($imgSourceObject, true);// added for transparency gif
                        // for transparency gif before use imagecopy() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency gif. if not transparency png just use this fill no any alpha and color transparent function call.
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for transparency gif
                        // imagecopy (crop)
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $cropWidth, $cropHeight);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
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
                        // rotate
                        $imgDestinationObject = imagerotate($imgDestinationObject, $deg, imagecolorallocate($imgDestinationObject, 255, 255, 255));
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-crop-' . $cropWidth . 'x' . $cropHeight . '-from-source-image.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
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
                        list($width, $height) = getimagesize($sourceImageFile);
                        $deg = 270;
                        $imgSourceObject = imagecreatefromgif($sourceImageFile);
                        // for set source gif image transparency after imagecreatefromgif() function.
                        imagesavealpha($imgSourceObject, true);// added for transparency gif
                        $imgDestinationObject = imagecreatetruecolor($width, $height);
                        // for transparency gif before rotate.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);
                        imagefill($imgDestinationObject, 0, 0, $transwhite);
                        imagecolortransparent($imgDestinationObject, $transwhite);
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, 1920, 1281);
                        // rotate
                        $imgDestinationObject = imagerotate($imgDestinationObject, $deg, $transwhite);
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-' . $deg . '-from-source-image.gif';
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
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
                        $imgSourceObject = imagecreatefromgif($sourceImageFile);
                        // for set source gif image transparency after imagecreatefromgif() function.
                        imagesavealpha($imgSourceObject, true);// added for transparency gif
                        // for transparency gif before use imagecopyresampled() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency gif
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for transparency gif
                        // imagecopyresampled (resize)
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
                        // for convert transparency gif to white before save to another extension
                        $tmpImgObject = imagecreatetruecolor($size[0], $size[1]);// added for convert from transparency png to other
                        $white = imagecolorallocate($tmpImgObject, 255, 255, 255);// added for convert from transparency png to other
                        imagefill($tmpImgObject, 0, 0, $white);// added for convert from transparency png to other
                        imagecopy($tmpImgObject, $imgDestinationObject, 0, 0, 0, 0, $size[0], $size[1]);// added for convert from transparency png to other
                        $imgDestinationObject = $tmpImgObject;
                        unset($tmpImgObject);
                        // save
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-jpg.png';
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
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
                        $imgSourceObject = imagecreatefromgif($sourceImageFile);
                        // for set source gif image transparency after imagecreatefromgif() function.
                        imagesavealpha($imgSourceObject, true);// added for transparency gif
                        // for transparency gif before use imagecopyresampled() function.
                        $transwhite = imagecolorallocatealpha($imgDestinationObject, 255, 255, 255, 127);// added for transparency gif
                        imagefill($imgDestinationObject, 0, 0, $transwhite);// added for transparency gif
                        imagecolortransparent($imgDestinationObject, $transwhite);// added for transparency gif
                        // imagecopyresampled (resize)
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $size[0], $size[1], $width, $height);
                        // for convert transparency gif to white before save to another extension
                        $tmpImgObject = imagecreatetruecolor($size[0], $size[1]);// added for convert from transparency png to other
                        $white = imagecolorallocate($tmpImgObject, 255, 255, 255);// added for convert from transparency png to other
                        imagefill($tmpImgObject, 0, 0, $white);// added for convert from transparency png to other
                        imagecopy($tmpImgObject, $imgDestinationObject, 0, 0, 0, 0, $size[0], $size[1]);// added for convert from transparency png to other
                        $imgDestinationObject = $tmpImgObject;
                        unset($tmpImgObject);
                        // save
                        $saveImgLink = '../processed-images/' . basename(__FILE__, '.php') . '-resize-' . $size[0] . 'x' . $size[1] . '-from-jpg.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        echo '<a href="' . $saveImgLink . '"><img class="thumbnail" src="' . $saveImgLink . '" alt=""></a><br>' . PHP_EOL;
                        echo $size[0] . 'x' . $size[1] . '<br>' . PHP_EOL;
                        echo 'Save result: ' . var_export($saveResult, true) . '<br>' . PHP_EOL;
                        $Finfo = new finfo();
                        echo 'Mime type using <code>finfo()</code>: ' . $Finfo->file($saveImgLink, FILEINFO_MIME_TYPE) . '<br>' . PHP_EOL;
                        imagedestroy($imgDestinationObject);
                        imagedestroy($imgSourceObject);
                        unset($Finfo, $imgDestinationObject, $imgSourceObject, $saveImgLink, $saveResult);
                        unset($height, $width, $size);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <?php 
        unset($sourceImageFile);
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>