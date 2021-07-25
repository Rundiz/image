<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
$sourceImageSquare = '../source-images/sample-square.jpg';
$chineseText = '他们';
$watermarkText = 'Hello สั้น ญ ญู ฎฐ โอ ไอ อำ อ้า bye!';
$font = '../source-images/font.ttf';
$fontSize = 20;


function displayWatermarkTextImage($positionx, $positiony, $driver = 'Gd', $txtColor = 'white', $fillBg = true)
{
    global $font, $fontSize;
    global $sourceImageSquare;
    global $watermarkText;

    $saveImage = '../processed-images/' . basename(__FILE__, '.php') . '-use' . strtolower($driver) . 'position' . $positionx . ',' . $positiony . '-txtc' . $txtColor . '-fillc' . $fillBg . '.jpg';
    if (strtolower($driver) === 'imagick') {
        $Image = new \Rundiz\Image\Drivers\Imagick($sourceImageSquare);
    } else {
        $Image = new \Rundiz\Image\Drivers\Gd($sourceImageSquare);
    }
    $Image->wmTextBottomPadding = 10;
    $Image->watermarkText(
        $watermarkText, 
        $font, 
        $positionx, 
        $positiony, 
        $fontSize, 
        $txtColor,
        60,
        [
            'fillBackground' => $fillBg,
            'backgroundColor' => 'debug',
        ]
    );
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
}// displayWatermarkTextImage
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Watermark text comparison</h1>
        <p>Source image: <a href="<?=$sourceImageSquare; ?>"><img class="thumbnail" src="<?=$sourceImageSquare; ?>" alt=""></a></p>
        <p>
            Watermark text: <em><?=$watermarkText; ?></em><br>
            Font size: <?=$fontSize; ?> 
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
                        displayWatermarkTextImage($positionSet[0], $positionSet[1]);
                        ?> 
                    </td>
                    <td>
                        <?php
                        displayWatermarkTextImage($positionSet[0], $positionSet[1], 'Imagick');
                        ?> 
                    </td>
                </tr>
                    <?php
                }// endforeach;
                unset($positionSet);
                unset($positions);
                ?> 
                <tr>
                    <td>0, 0 (gd only, no background)</td>
                    <td>
                        <?php
                        displayWatermarkTextImage(0, 0, 'Gd', 'black', false);
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