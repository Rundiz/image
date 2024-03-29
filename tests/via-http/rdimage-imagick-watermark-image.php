<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


$imgType = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
$imgType = strip_tags($imgType);


function displayTestWatermarkImagePositions($sourceImage)
{
    $watermarkImage = '../source-images/watermark.jpg';

    echo '<h3>Position tests.</h3>' . "\n";
    echo '<table><tbody>' . "\n";
    echo '    <tr>' . "\n";
    echo '        <td style="width: 200px;">Source image</td>' . "\n";
    echo '        <td colspan="3">' . "\n";
    debugImage($sourceImage);
    echo '        </td>' . "\n";
    echo '    </tr>' . "\n";
    echo '    <tr>' . "\n";
    echo '        <td>Watermark</td>' . "\n";
    echo '        <td colspan="3">' . "\n";
    debugImage($watermarkImage);
    echo '        </td>' . "\n";
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
            '_wmimg-' . pathinfo($watermarkImage, PATHINFO_EXTENSION) .
            '.' . $sourceExt;
        unset($imgTypeQuerystring, $sourceExtAppend);
        $wmResult = $Image->watermarkImage($watermarkImage, $positionXY[0], $positionXY[1]);
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
}// displayTestWatermarkImagePositions


function displayTestWatermarkImageDifferentWatermarkExts(array $test_data_set)
{
    $positions = [
        ['left', 'top'],
        ['center', 'top'],
        ['right', 'top'],
    ];
    $wmExts = ['jpg', 'gif', 'png'];

    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h4>' . $img_type_name . '</h4>' . "\n";
        echo '<table><tbody>' . "\n";
        echo '<tr>' . "\n";
        echo '<td style="width: 200px;">Source image</td>' . "\n";
        echo '<td colspan="3">' . "\n";
        debugImage($item['source_image_path']);
        echo '</td>' . "\n";
        echo '</tr>' . "\n";
        foreach ($wmExts as $eachWmExt) {
            $watermarkImage = '../source-images/watermark.' . $eachWmExt;
            echo '<tr>' . "\n";
            echo '<td>Watermark</td>' . "\n";
            echo '<td colspan="3">' . "\n";
            debugImage($watermarkImage);
            echo '</td>' . "\n";
            echo '</tr>' . "\n";
            echo '<tr>' . "\n";
            echo '<td></td>' . "\n";
            $Image = new Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            foreach ($positions as $positionXY) {
                $eachExt = pathinfo($item['source_image_path'], PATHINFO_EXTENSION);// use in stead of save extensions.
                $fileName = '../processed-images/' . autoImageFilename() . '_src' . strtolower(str_replace(' ', '', $img_type_name)) .
                    '_position-' . $positionXY[0] . ',' . $positionXY[1] .
                    '_wmimg-' . $eachWmExt .
                    '_target' . $eachExt .
                    '.' . $eachExt;
                $wmResult = $Image->watermarkImage($watermarkImage, $positionXY[0], $positionXY[1]);
                if ($wmResult !== true) {
                    $wmStatusMsg = $Image->status_msg;
                }
                $saveResult = $Image->save($fileName);
                $Image->clear();
                unset($wmResult);

                echo '<td>';
                echo '<a href="' . $fileName . '"><img class="thumbnail" src="' . $fileName . '"></a><br>';
                echo 'Position ' . $positionXY[0] . ',' . $positionXY[1] . '<br>';
                if (isset($wmStatusMsg)) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $wmStatusMsg . '</span><br>';
                }
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span><br>';
                }
                echo '</td>' . "\n";
                unset($saveResult, $wmStatusMsg);
                unset($fileName, $eachExt);
            }// endforeach; positions
            unset($positionXY);
            unset($Image);
            echo '</tr>' . "\n";
            unset($watermarkImage);
        }// endforeach; watermark ext.
        unset($eachWmExt);
        echo '</tbody></table>' . "\n";
    }// endforeach;
    unset($img_type_name, $item);

    unset($positions, $wmExts);
}// displayTestWatermarkImageDifferentWatermarkExts
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Watermark image (Imagick)</h1>
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
        displayTestWatermarkImagePositions($doTestData[$imgType]['source_image_path']);
        ?>
        <hr>
        <?php
        displayTestWatermarkImageDifferentWatermarkExts($doTestData);
        unset($doTestData);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?> 
    </body>
</html>