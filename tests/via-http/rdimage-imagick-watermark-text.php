<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


$imgType = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
$imgType = strip_tags($imgType);


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
    echo '    <tr>' . "\n";
    echo '        <td style="width: 200px;">Source image</td>' . "\n";
    echo '        <td colspan="3">' . "\n";
    debugImage($sourceImage);
    echo '        </td>' . "\n";
    echo '    </tr>' . "\n";
    echo '    <tr>' . "\n";
    echo '        <td>Font</td><td colspan="3"><a href="' . $fontFile . '">' . $fontFile . '</a></td>' . "\n";
    echo '    </tr>' . "\n";

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
            echo '    <tr>' . "\n";
            echo '        <td></td>' . "\n";
            $col++;
        }

        $imgTypeQuerystring = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
        if (stripos($imgTypeQuerystring, 'animat') !== false) {
            $sourceExtAppend = 'animated';
        } elseif (stripos($imgTypeQuerystring, 'non transparent') !== false) {
            $sourceExtAppend = 'nontransparent';
        } else {
            $sourceExtAppend = '';
        }
        $sourceExt = pathinfo($sourceImage, PATHINFO_EXTENSION);
        $fileName = '../processed-images/' . autoImageFilename() . '_src' . $sourceExt . $sourceExtAppend .
            '_position-' . $positionXY[0] . ',' . $positionXY[1] .
            '.' . $sourceExt;
        unset($imgTypeQuerystring, $sourceExtAppend);
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
            20,
            [
                'fillBackground' => false,
                'backgroundColor' => 'debug',
            ]
        );
        if ($wmResult !== true) {
            // if there is watermark errors.
            // keep in variable before it can be changed via `save()`.
            $wmStatusMsg = $Image->status_msg;
        }
        $saveResult = $Image->save($fileName);
        echo '        <td>';
        echo '<a href="' . $fileName . '"><img class="thumbnail" src="' . $fileName . '" alt=""></a><br>';
        echo $positionXY[0] . ',' . $positionXY[1];
        if (isset($wmStatusMsg)) {
            echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $wmStatusMsg . '</span><br>';
        }
        if ($saveResult != true) {
            echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span><br>';
        }
        $Image->clear();
        unset($sourceExt, $wmResult, $wmStatusMsg);
        unset($fileName, $saveResult);
        echo '</td>' . "\n";
        $col++;

        if ($col > $totalCol || is_numeric($positionXY[0]) || is_numeric($positionXY[1])) {
            if (is_numeric($positionXY[0]) || is_numeric($positionXY[1])) {
                echo '        <td colspan="' . ($totalCol - ($col - 1)) . '"></td>' . "\n";
            }
            echo '    </tr>' . "\n";
            $col = 1;
        }

        unset($Image);
    }// endforeach positions
    unset($positionXY);
    unset($col, $positions, $totalCol);
    echo '<!-- END display test in positions -->' . "\n\n";

    echo '</tbody></table>' . "\n";
}// displayTestWatermarkTextPositions


function displayTestSaveCrossExts(array $test_data_set)
{
    global $fontFile, $fontSize;
    global $watermarkText, $wmTextBottomPadding, $wmTextBoundingBoxYPadding;
    global $imagickBaseline;

    $saveExts = ['gif', 'jpg', 'png', 'webp'];
    $positionXY = [430, 350];

    echo '<h2>Watermark text &amp; Save across different extensions.</h2>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td style="width: 200px;">Source image</td>' . "\n";
            echo '        <td>' . "\n";
            $srcImageSize = false;
            if (is_file($item['source_image_path'])) {
                $srcImageSize = getimagesize($item['source_image_path']);
            }
            debugImage($item['source_image_path']);
            echo '        </td>' . "\n";
            echo '        <td>Position ' . $positionXY[0] . ',' . $positionXY[1] . '</td>' . "\n";
            $Image = new Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Save as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
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
                    20,
                    [
                        'fillBackground' => false,
                        'backgroundColor' => 'debug',
                    ]
                );
                if ($wmResult !== true) {
                    // if there is watermark errors.
                    // keep in variable before it can be changed via `save()`.
                    $wmStatusMsg = $Image->status_msg;
                }
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-800x600' .
                    '-saveas-' . trim($eachExt) . '.' . $eachExt;
                $saveResult = $Image->save($file_name);
                $statusMsg = $Image->status_msg;
                $Image->clear();
                echo '        <td>' . "\n";
                debugImage($file_name);
                if (isset($wmStatusMsg)) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $wmStatusMsg . '</span><br>';
                }
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$statusMsg.'</span>'."\n";
                }
                echo '        </td>' . "\n";
                unset($wmResult, $wmStatusMsg);
                unset($file_name, $saveResult, $statusMsg);
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";
            unset($Image);

            echo '    <tr>' . "\n";
            echo '        <td>Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
                $linkTo = 'rdimage-imagick-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=watermarktext' .
                    '&amp;startx=' . $positionXY[0] .
                    '&amp;starty=' . $positionXY[1] .
                    '&amp;fontsize=' . $fontSize;
                echo '        <td>';
                echo '<a href="' . $linkTo . '"><img class="thumbnail" src="' . $linkTo . '" alt=""></a><br>';
                echo 'Extension: ' . $eachExt;
                unset($linkTo);
                echo '</td>' . "\n";
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";

            echo '</tbody></table>' . "\n";
            
            unset($srcImageSize);
        }// endif;
        echo "\n\n";
    }// endforeach;
    unset($img_type_name, $item);

    echo "\n\n";
    unset($positionXY);
    unset($saveExts);
}// displayTestSaveCrossExts
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
        // default do test data set.
        $doTestData = [
            $imgType => [],
        ];
        // set do test data from parameter.
        if (array_key_exists($imgType, $test_data_set)) {
            $doTestData = [$imgType => $test_data_set[$imgType]];
        } else {
            if (array_key_exists($imgType, $test_data_pngnt)) {
                $doTestData = [$imgType => $test_data_pngnt[$imgType]];
            } elseif (array_key_exists($imgType, $test_data_falsy)) {
                $doTestData = [$imgType => $test_data_falsy[$imgType]];
            } elseif (array_key_exists($imgType, $test_data_anim)) {
                $doTestData = [$imgType => $test_data_anim[$imgType]];
            }
        }
        displayTestWatermarkTextPositions($doTestData[$imgType]['source_image_path']);
        displayTestSaveCrossExts($doTestData);
        unset($doTestData);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?> 
    </body>
</html>