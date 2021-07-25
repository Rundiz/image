<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
$sourceImage = '../source-images/sample-square.jpg';
$watermarkImage = '../source-images/watermark.jpg';


function displayWatermarkImage($positionx, $positiony, $driver = 'Gd', $saveExt = 'gif')
{
    global $font, $fontSize;
    global $sourceImage;
    global $watermarkImage;

    $saveImage = '../processed-images/' . basename(__FILE__, '.php') . '-use' . strtolower($driver) . 'position' . $positionx . ',' . $positiony . '.' . $saveExt;
    if (strtolower($driver) === 'imagick') {
        $Image = new \Rundiz\Image\Drivers\Imagick($sourceImage);
    } else {
        $Image = new \Rundiz\Image\Drivers\Gd($sourceImage);
    }
    $Image->watermarkImage($watermarkImage, $positionx, $positiony);
    $Image->save($saveImage);
    $Image->clear();
    unset($Image);

    echo '<a href="' . $saveImage . '"><img class="img-responsive" src="' . $saveImage . '?v=' . date('YmdHis') . '" alt=""></a><br>' . PHP_EOL;
    $Finfo = new finfo();
    echo 'Mime type: ' . $Finfo->file($saveImage, FILEINFO_MIME_TYPE);
    unset($Finfo);
    echo '; ';
    echo 'File size: ' . filesize($saveImage) . ' bytes<br>' . PHP_EOL;
    list($width, $height) = getimagesize($saveImage);
    echo 'Image resolution: ' . $width . 'x' . $height . '<br>' . PHP_EOL;
    unset($height, $width);
}// displayWatermarkImage
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Watermark image comparison</h1>
        <p>Source image: <a href="<?=$sourceImage; ?>"><img class="thumbnail" src="<?=$sourceImage; ?>" alt=""></a></p>
        <p>
            Watermark image: <a href="<?=$watermarkImage; ?>"><img class="thumbnail" src="<?=$watermarkImage; ?>" alt=""></a>
        </p>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Position</th>
                    <th style="width: 45%;">GD</th>
                    <th style="width: 45%;">Imagick</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $positions = [
                    [0, 0],
                    ['left', 'bottom'],
                    ['right', 'top'],
                ];
                foreach ($positions as $positionSet) {
                    ?> 
                <tr>
                    <td>
                        <?php
                        echo $positionSet[0] . ', ' . $positionSet[1];
                        ?> 
                    </td>
                    <td>
                        <?php
                        displayWatermarkImage($positionSet[0], $positionSet[1]);
                        ?> 
                    </td>
                    <td>
                        <?php
                        displayWatermarkImage($positionSet[0], $positionSet[1], 'Imagick');
                        ?> 
                    </td>
                </tr>
                    <?php
                }// endforeach;
                unset($positionSet);
                unset($positions);
                ?> 
                <tr>
                    <td>0, 0 save as gif</td>
                    <td>
                        <?php
                        displayWatermarkImage(0, 0, 'Gd', 'gif');
                        ?> 
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>0, 0 save as png</td>
                    <td>
                        <?php
                        displayWatermarkImage(0, 0, 'Gd', 'png');
                        ?> 
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>