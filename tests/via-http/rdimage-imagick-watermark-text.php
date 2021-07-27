<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


$fontFile = '../source-images/font.ttf';
$fontSize = 30;
$wmTextBottomPadding = 13;
$wmTextBoundingBoxYPadding = 4;
$imagickBaseline = 1;
$watermarkText = 'Hello สั้น ญ ญู ฎฐ โอ ไอ อำ อ้า bye!';


function displayTestWatermarkTextPositions($sourceImage)
{
    global $fontFile, $fontSize;
    global $watermarkText, $wmTextBottomPadding, $wmTextBoundingBoxYPadding;
    global $imagickBaseline;

    echo '<h3>Position tests.</h3>' . "\n";
    echo '<table><tbody>' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="width: 200px;">Source image</td>' . "\n";
    echo '<td><a href="' . $sourceImage . '"><img class="thumbnail" src="' . $sourceImage . '" alt=""></a><br>';
    $srcImgSize = getimagesize($sourceImage);
    if (is_array($srcImgSize)) {
        echo $srcImgSize[0] . 'x' . $srcImgSize[1] . ' ';
        echo 'Mime type: ' . $srcImgSize['mime'];
    }
    unset($srcImgSize);
    echo '</td>' . "\n";
    echo '</tr>' . "\n";
    echo '<tr>' . "\n";
    echo '<td>Font</td><td><a href="' . $fontFile . '">' . $fontFile . '</a></td>' . "\n";
    echo '</tr>' . "\n";

    echo "\n" . '<!-- display test in positions -->' . "\n";
    $positions = [
        [100, 300],
        ['left', 'top'],
        ['center', 'top'],
        ['right', 'top'],
        ['left', 'middle'],
        ['center', 'middle'],
        ['right', 'middle'],
        ['left', 'bottom'],
        ['center', 'bottom'],
        ['right', 'bottom'],
    ];
    $totalCol = 4;
    $col = 1;
    foreach ($positions as $positionXY) {
        $Image = new Rundiz\Image\Drivers\Imagick($sourceImage);

        if ($col === 1) {
            echo '  <tr>' . "\n";
            echo '    <td></td>' . "\n";
            $col++;
        }
        $sourceExt = pathinfo($sourceImage, PATHINFO_EXTENSION);
        $fileName = '../processed-images/' . basename(__FILE__, '.php') . '_src' . $sourceExt .
            '_position-' . $positionXY[0].','.$positionXY[1] .
            '.' . $sourceExt;
        $Image->wmTextBottomPadding = $wmTextBottomPadding;
        $Image->wmTextBoundingBoxYPadding = $wmTextBoundingBoxYPadding;
        $Image->imagickWatermarkTextBaseline = $imagickBaseline;
        $wmResult = $Image->watermarkText(
            $watermarkText, 
            $fontFile, 
            $positionXY[0], 
            $positionXY[1], 
            $fontSize, 
            'transwhitetext',
            60,
            [
                'fillBackground' => false,
                'backgroundColor' => 'debug',
            ]
        );
        if ($wmResult !== true) {
            $wmStatusMsg = $Image->status_msg;
        }
        $saveResult = $Image->save($fileName);
        $Image->clear();
        unset($sourceExt, $wmResult);

        echo '    <td>';
        echo '<a href="' . $fileName . '"><img class="thumbnail" src="' . $fileName . '" alt=""></a><br>';
        echo $positionXY[0] . ',' . $positionXY[1];
        if (isset($wmStatusMsg)) {
            echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$wmStatusMsg.'</span>';
        }
        if ($saveResult != true) {
            echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg.'</span>';
        }
        unset($saveResult, $wmStatusMsg);
        echo '</td>' . "\n";
        $col++;

        if ($col > $totalCol || is_numeric($positionXY[0]) || is_numeric($positionXY[1])) {
            if (is_numeric($positionXY[0]) || is_numeric($positionXY[1])) {
                echo '    <td colspan="' . ($totalCol - ($col - 1)) . '"></td>' . "\n";
            }
            echo '  </tr>' . "\n";
            $col = 1;
        }
        unset($fileName);

        unset($Image);
    }// endforeach positions
    unset($positionXY);
    unset($col, $positions, $totalCol);
    echo '<!-- END display test in positions -->' . "\n\n";

    echo '</tbody></table>' . "\n";
}// displayTestWatermarkTextPositions


function displayTestWatermarkTextSaveExts(array $test_data_set)
{
    global $fontFile, $fontSize;
    global $watermarkText, $wmTextBottomPadding, $wmTextBoundingBoxYPadding;
    global $imagickBaseline;

    $positions = [
        ['left', 'top'],
        ['center', 'top'],
        ['right', 'top'],
    ];
    $saveExts = ['gif', 'jpg', 'png'];

    echo '<h3>Save across different extensions.</h3>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h4>' . $img_type_name . '</h4>' . "\n";
        echo '<table><tbody>' . "\n";
        echo '<tr>' . "\n";
        echo '<td style="width: 200px;">Source image</td>' . "\n";
        echo '<td colspan="' . count($saveExts) . '"><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a><br>';
        $srcImgSize = getimagesize($item['source_image_path']);
        if (is_array($srcImgSize)) {
            echo $srcImgSize[0] . 'x' . $srcImgSize[1] . ' ';
            echo 'Mime type: ' . $srcImgSize['mime'];
        }
        unset($srcImgSize);
        echo '</td>'."\n";
        echo '</tr>' . "\n";
        echo '<tr>' . "\n";
        echo '<td>Font</td><td colspan="' . count($saveExts) . '"><a href="' . $fontFile . '">' . $fontFile . '</a></td>' . "\n";
        echo '</tr>' . "\n";
        $Image = new Rundiz\Image\Drivers\Imagick($item['source_image_path']);
        foreach ($positions as $positionXY) {
            echo '<tr>' . "\n";
            echo '<td>Position ' . $positionXY[0] . ',' . $positionXY[1] . '</td>' . "\n";

            foreach ($saveExts as $eachExt) {
                $fileName = '../processed-images/' . basename(__FILE__, '.php') . '_src' . $img_type_name .
                    '_position-' . $positionXY[0].','.$positionXY[1] .
                    '_target' . $eachExt .
                    '.' . $eachExt;
                $Image->wmTextBottomPadding = $wmTextBottomPadding;
                $Image->wmTextBoundingBoxYPadding = $wmTextBoundingBoxYPadding;
                $Image->imagickWatermarkTextBaseline = $imagickBaseline;
                $wmResult = $Image->watermarkText(
                    $watermarkText, 
                    $fontFile, 
                    $positionXY[0], 
                    $positionXY[1], 
                    $fontSize, 
                    'transwhitetext',
                    60,
                    [
                        'fillBackground' => false,
                        'backgroundColor' => 'debug',
                    ]
                );
                if ($wmResult !== true) {
                    $wmStatusMsg = $Image->status_msg;
                }
                $saveResult = $Image->save($fileName);
                $Image->clear();
                unset($wmResult);

                echo '<td>';
                echo '<a href="' . $fileName . '"><img class="thumbnail" src="' . $fileName . '"></a><br>';
                echo 'Save as ' . $eachExt;
                if (isset($wmStatusMsg)) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$wmStatusMsg.'</span>';
                }
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg.'</span>';
                } else {
                    $Finfo = new finfo();
                    echo '; Mime type: ' . $Finfo->file($fileName, FILEINFO_MIME_TYPE);
                    unset($Finfo);
                }
                echo '</td>' . "\n";
                unset($saveResult, $wmStatusMsg);
            }// endforeach save extensions
            unset($eachExt);

            echo '</tr>' . "\n";
        }// endforeach; positions
        unset($positionXY);
        unset($Image);
        echo '</tbody></table>' . "\n";
    }// endforeach;
    unset($img_type_name, $item);

    unset($positions, $saveExts);
}// displayTestWatermarkTextSaveExts
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Watermark text (Imagick)</h1>
        <?php
        displayTestWatermarkTextPositions($source_image_jpg);
        ?>
        <hr>
        <?php
        displayTestWatermarkTextSaveExts($test_data_set);
        ?>
        <hr>
        <?php
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>