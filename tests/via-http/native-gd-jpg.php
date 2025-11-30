<?php
$sourceImageFile = '../source-images/source-image.jpg';

include_once 'includes/include-functions.php';


if (!function_exists('destroyGdImage')) {
    /**
     * Destroy GD image resource/object
     * 
     * @see imagedestroy()
     * @param resource|object $image
     * @return bool
     */
    function destroyGdImage(&$image)
    {
        if (is_resource($image) || is_object($image)) {
            if (version_compare(PHP_VERSION, '8.0', '<')) {
                return imagedestroy($image);
            }
            $image = null;
            return true;
        }
        return false;
    }// destroyGdImage
}


/**
 * Create new resource/object (GDImage for PHP 8.0+) from image file.
 * 
 * @global string $sourceImageFile
 * @return \GdImage|false Returns an image object on success, false on errors.
 */
function newGdFromFile()
{
    global $sourceImageFile;
    return imagecreatefromjpeg($sourceImageFile);
}// newGdFromFile
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>Native PHP GD functions</h1>
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
                        $imgSourceObject = newGdFromFile();
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
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        destroyGdImage($imgSourceObject);
                        $imgSourceObject = $imgDestinationObject;
                        unset($imgDestinationObject, $newDimension);

                        $newDimension = [700, 467];
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        destroyGdImage($imgSourceObject);
                        $imgSourceObject = $imgDestinationObject;
                        unset($imgDestinationObject, $newDimension);

                        $newDimension = [460, 460];
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1]);
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);

                        $imgSourceObject = newGdFromFile();

                        $newDimension = [460, 460];
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1]);
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-crop-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source' . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        destroyGdImage($imgSourceObject);
                        $imgSourceObject = $imgDestinationObject;
                        unset($imgDestinationObject, $newDimension);

                        $rotate = 90;
                        $imgDestinationObject = imagerotate($imgSourceObject, $rotate, imagecolorallocate($imgSourceObject, 255, 255, 255));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-rotate-' . $rotate . '-from-crop-' . $previousDimension[0] . 'x' . $previousDimension[1] . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);

                        $imgSourceObject = newGdFromFile();

                        $rotate = 270;
                        $imgDestinationObject = imagerotate($imgSourceObject, $rotate, imagecolorallocate($imgSourceObject, 255, 255, 255));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-rotate-' . $rotate . '-from-source' . '.jpg';
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
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
                        if (function_exists('imageflip')) {
                            $flip = 'horizontal';
                            imageflip($imgDestinationObject, IMG_FLIP_HORIZONTAL);
                            $saveImgLink = '../processed-images/' . autoImageFilename() . '-flip-' . $flip . '-from-rotate-' . 270 . '.jpg';
                            $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                            debugImage($saveImgLink);
                            echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                            unset($saveImgLink, $saveResult);
                            unset($flip);
                        } else {
                            echo '<p class="text-error">This version of PHP does not supported <code>imageflip()</code>.</p>' . "\n";
                        }
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Flip</th>
                    <td>Source</td>
                    <td>
                        <?php
                        if (function_exists('imageflip')) {
                            // clear everything before begins again from source.
                            destroyGdImage($imgSourceObject);
                            destroyGdImage($imgDestinationObject);
                            unset($imgDestinationObject, $imgSourceObject);

                            $imgSourceObject = newGdFromFile();

                            $flip = 'both';
                            imageflip($imgSourceObject, IMG_FLIP_BOTH);
                            // create new canvas for be able to use with next process.
                            $imgDestinationObject = imagecreatetruecolor(imagesx($imgSourceObject), imagesy($imgSourceObject));
                            imagecopy($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, imagesx($imgSourceObject), imagesy($imgSourceObject));
                            // end create new canvas for be able to use with next process.
                            $saveImgLink = '../processed-images/' . autoImageFilename() . '-flip-' . $flip . '-from-source' . '.jpg';
                            $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                            debugImage($saveImgLink);
                            echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                            unset($saveImgLink, $saveResult);
                            unset($flip);
                        } else {
                            echo '<p class="text-error">This version of PHP does not supported <code>imageflip()</code>.</p>' . "\n";
                        }

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>

                <!-- resize & save as -->
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $imgSourceObject = newGdFromFile();

                        $newDimension = [800, 534];
                        $saveAs = 'avif';
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        if (function_exists('imageavif')) {
                            $saveResult = imageavif($imgDestinationObject, $saveImgLink);
                        } else {
                            $saveResult = false;
                        }
                        debugImage($saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $imgSourceObject = newGdFromFile();

                        $newDimension = [800, 534];
                        $saveAs = 'gif';
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = imagegif($imgDestinationObject, $saveImgLink);
                        debugImage($saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $imgSourceObject = newGdFromFile();

                        $saveAs = 'jpg';
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = imagejpeg($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $imgSourceObject = newGdFromFile();

                        $saveAs = 'png';
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = imagepng($imgDestinationObject, $saveImgLink, 0);
                        debugImage($saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>Resize &amp; save as&hellip;</th>
                    <td>Source</td>
                    <td>
                        <?php
                        $imgSourceObject = newGdFromFile();

                        $saveAs = 'webp';
                        $imgDestinationObject = imagecreatetruecolor($newDimension[0], $newDimension[1]);
                        imagecopyresampled($imgDestinationObject, $imgSourceObject, 0, 0, 0, 0, $newDimension[0], $newDimension[1], imagesx($imgSourceObject), imagesy($imgSourceObject));
                        $saveImgLink = '../processed-images/' . autoImageFilename() . '-resize-' . $newDimension[0] . 'x' . $newDimension[1] . '-from-source-saveas-' . $saveAs . '.' . $saveAs;
                        $saveResult = imagewebp($imgDestinationObject, $saveImgLink, 100);
                        debugImage($saveImgLink);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        // clear everything before begins again from source.
                        destroyGdImage($imgSourceObject);
                        destroyGdImage($imgDestinationObject);
                        unset($imgDestinationObject, $imgSourceObject);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        // cleanup.
        unset($imgDestinationObject, $imgSourceObject);
        unset($newDimension, $previousDimension);
        unset($saveAs);
        ?> 
        <hr>
        <?php 
        unset($sourceImageFile);
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>