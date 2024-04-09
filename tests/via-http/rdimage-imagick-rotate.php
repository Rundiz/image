<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


$imgType = (isset($_GET['imgType']) ? $_GET['imgType'] : 'JPG');
$imgType = strip_tags($imgType);


function displayTestRotate(array $test_data_set)
{
    $rotates = [90, 180, 270, 'hor', 'vrt', 'horvrt'];
    echo '<h1>Rotate &amp; flip the images (Imagick)</h1>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>' . $img_type_name . '</h3>' . "\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            $file_ext = '.' . pathinfo($item['source_image_path'], PATHINFO_EXTENSION);
            echo '<table><tbody>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td>Source image</td>' . "\n";
            echo '        <td>' . "\n";
            debugImage($item['source_image_path']);
            echo '        </td>' . "\n";
            echo '    </tr>' . "\n";
            $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            echo '    <tr>' . "\n";
            echo '        <td></td>' . "\n";
            $i = 1;
            $countRotate = 1;
            foreach ($rotates as $rotate) {
                echo '        <td>' . "\n";
                $Image->rotate($rotate);
                $file_name = '../processed-images/' . autoImageFilename() . '_src' . str_replace(' ', '-', strtolower($img_type_name)) . '_' . $rotate;
                $saveResult = $Image->save($file_name . $file_ext);
                echo '<a href="' . $file_name . $file_ext . '"><img src="' . $file_name . $file_ext . '" alt="" class="thumbnail"></a><br>';
                echo 'Rotate ' . $rotate . "\n";
                debugImage($file_name . $file_ext, ['dataOnly' => true]);
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span><br>' . "\n";
                }
                echo '        </td>' . "\n";
                unset($file_name, $saveResult);
                $Image->clear();
                $countRotate++;
                $i++;
                if ($i > 3) {
                    $i = 1;
                    if ($countRotate <= count($rotates)) {
                        echo '    </tr>' . "\n";
                        echo '    <tr>' . "\n";
                        echo '        <td></td>' . "\n";
                    }
                }
            }// endforeach;
            echo '    </tr>' . "\n";
            echo '</tbody></table>' . "\n";
            unset($countRotate, $file_ext, $i, $Image, $rotate);
        }
        echo "\n\n";
    }// endforeach;
    unset($rotates);
    echo "\n\n";
}// displayTestRotate


function displayTestSaveCrossExts(array $test_data_set)
{
    global $saveAsExts;

    echo '<h2>Rotate &amp; Save across different extensions.</h2>' . "\n";
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
            echo '        </td>'."\n";
            $Image = new Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Rotate &amp; Save as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $Image->rotate(270);
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-rotate-270' .
                    '-saveas-' . trim($eachExt) . '.' . $eachExt;
                $saveResult = $Image->save($file_name);
                $statusMsg = $Image->status_msg;
                $Image->clear();
                echo '        <td>' . "\n";
                debugImage($file_name);
                if ($saveResult != true) {
                    echo '            &nbsp; &nbsp; <span class="text-error">Error: '.$statusMsg.'</span>'."\n";
                }
                echo '        </td>' . "\n";
                unset($file_name, $saveResult, $statusMsg);
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Rotate &amp; Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $linkTo = 'rdimage-imagick-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=rotate' .
                    '&amp;degree=' . 270;
                echo '        <td>';
                echo '<a href="' . $linkTo . '"><img class="thumbnail" src="' . $linkTo . '" alt=""></a><br>';
                echo 'Extension: ' . $eachExt;
                unset($linkTo);
                echo '</td>' . "\n";
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Flip &amp; Save as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $Image->rotate('vrt');
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-rotate-vrt' .
                    '-saveas-' . trim($eachExt) . '.' . $eachExt;
                $saveResult = $Image->save($file_name);
                $statusMsg = $Image->status_msg;
                $Image->clear();
                echo '        <td>' . "\n";
                debugImage($file_name);
                if ($saveResult != true) {
                    echo '            &nbsp; &nbsp; <span class="text-error">Error: '.$statusMsg.'</span>'."\n";
                }
                echo '        </td>' . "\n";
                unset($file_name, $saveResult, $statusMsg);
            }// endforeach; save extensions
            unset($eachExt);
            echo '    </tr>' . "\n";
            unset($Image);

            echo '    <tr>' . "\n";
            echo '        <td>Flip &amp; Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveAsExts as $eachExt) {
                $linkTo = 'rdimage-imagick-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=rotate' .
                    '&amp;degree=vrt';
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
        <?php
        // default do test data set.
        $doTestData = [
            $imgType => [],
        ];
        // set do test data from parameter.
        if (array_key_exists($imgType, $test_data_set)) {
            $doTestData = [$imgType => $test_data_set[$imgType]];
        } else {
            if (array_key_exists($imgType, $test_data_falsy)) {
                $doTestData = [$imgType => $test_data_falsy[$imgType]];
            } elseif (array_key_exists($imgType, $test_data_anim)) {
                $doTestData = [$imgType => $test_data_anim[$imgType]];
            }
        }
        displayTestRotate($doTestData);
        displayTestSaveCrossExts($doTestData);
        unset($doTestData);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>