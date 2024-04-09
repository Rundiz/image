<?php
$sourceImageFile = '../source-images/source-image.gif';
$watermarkImageFile = '../source-images/watermark.png';

include_once 'includes/include-functions.php';


/**
 * Make image object (usually for new canvas or `imagecreatetruecolor()`) to be transparent.
 * 
 * @param \GdImage $image The Gd image resource or object.
 */
function makeTransparent($image) 
{
    imagealphablending($image, false);
    imagesavealpha($image, true);
}// makeTransparent


/**
 * Fill transparent-white to image object (usually for new canvas or `imagecreatetruecolor()`).
 * 
 * There is no alpha bending mark nor save alpha setting.
 * 
 * This may cause transparent becomes white in non-transparency format on save such as jpeg.
 * 
 * @param \GdImage $image The Gd image resource or object.
 */
function fillTransparentWhite($image)
{
    imagefill($image, 0, 0, imagecolorallocatealpha($image, 255, 255, 255, 127));
    imagecolortransparent($image, imagecolorallocatealpha($image, 255, 255, 255, 127));
}// fillTransparentWhite
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
            <tbody>
                <tr>
                    <th>
                        Source image
                    </th>
                    <td>
                        <?php
                        debugImage($sourceImageFile);
                        $imgSourceObject = imagecreatefromgif($sourceImageFile);
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
                        $wmSourceObject = imagecreatefrompng($watermarkImageFile);
                        $wmPosition = [180, 200];

                        // create new canvas that is true color.
                        $newWmDestinationObject = imagecreatetruecolor(imagesx($wmSourceObject), imagesy($wmSourceObject));
                        // fill new canvas with transparent-white.
                        fillTransparentWhite($newWmDestinationObject);
                        // copy watermark image to new canvas that is true color and contain transparent.
                        // this can prevent black border between two transparent images (based image and watermark).
                        imagecopy($newWmDestinationObject, $wmSourceObject, 0, 0, 0, 0, imagesx($wmSourceObject), imagesy($wmSourceObject));
                        // mark new canvas that is copied watermark image as watermark source.
                        imagedestroy($wmSourceObject);
                        $wmSourceObject = $newWmDestinationObject;
                        unset($newWmDestinationObject);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <th>
                        Result
                    </th>
                    <td>
                        <?php
                        // the `pct` argument of `imagecopymerge()`, if it is 0 then watermark image will be invisible on non-transparent part of based image but some part will be red on transparent part of based image.
                        // if it is 50 then watermark will be half transparent on based image and fully appears on transparent part of based image.
                        // if it is 100 then watermark will be fully appears on all part of based image.
                        imagecopymerge($imgSourceObject, $wmSourceObject, $wmPosition[0], $wmPosition[1], 0, 0, imagesx($wmSourceObject), imagesy($wmSourceObject), 100);

                        $saveImgLink = '../processed-images/' . autoImageFilename() . '.gif';
                        $saveResult = imagegif($imgSourceObject, $saveImgLink);
                        debugImage($saveImgLink, ['imgClass' => 'img-fluid thumbnail larger']);
                        echo 'Save result: ' . var_export($saveResult, true) . PHP_EOL;
                        unset($saveImgLink, $saveResult);

                        imagedestroy($imgSourceObject);
                        imagedestroy($wmSourceObject);
                        unset($imgSourceObject, $wmSourceObject);
                        unset($wmPosition);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <hr>
        <?php
        unset($sourceImageFile, $watermarkImageFile);
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>