<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


function displayTestCrop(array $test_data_set)
{
    $cropPoses = [[0, 0, 'transparent'], [90, 90, 'black'], ['center', 'middle', 'white']];
    $cropSize = [900, 900];
    $cropSizeLarger = [2000, 2000];
    $cropPoseLarger = ['center', 'middle'];
    echo '<h1>Crop the images (GD)</h1>'."\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>' . $img_type_name . '</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            $file_ext = '.' . pathinfo($item['source_image_path'], PATHINFO_EXTENSION);
            echo '<table><tbody>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td>Source image</td>' . "\n";
            echo '        <td>' . "\n";
            debugImage($item['source_image_path']);
            echo '        </td>'."\n";
            echo '    </tr>' . "\n";
            $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
            echo '    <tr>' . "\n";
            echo '        <td></td>' . "\n";
            foreach ($cropPoses as $crop_xy) {
                if (isset($crop_xy[0]) && isset($crop_xy[1]) && isset($crop_xy[2])) {
                    echo '        <td>' . "\n";
                    $Image->crop($cropSize[0], $cropSize[1], $crop_xy[0], $crop_xy[1], $crop_xy[2]);
                    $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-crop-' . $cropSize[0] . 'x' . $cropSize[1] . '-start' . $crop_xy[0] . ',' . $crop_xy[1] . '-fill' . $crop_xy[2];
                    $save_result = $Image->save($file_name . $file_ext);
                    echo '            <a href="' . $file_name . $file_ext . '"><img src="' . $file_name . $file_ext . '" alt="" class="thumbnail"></a><br>';
                    echo 'Cropping ' . $cropSize[0] . '&times;' . $cropSize[1] . ' ';
                    echo 'at ' . $crop_xy[0] . ',' . $crop_xy[1] . ' fill ' . $crop_xy[2] . "\n";
                    debugImage($file_name . $file_ext, ['dataOnly' => true]);
                    if ($save_result != true) {
                        echo '<br>' . "\n";
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span>' . "\n";
                    }
                    unset($file_name, $save_result);
                    $Image->clear();
                    echo '        </td>' . "\n";
                }
            }// endforeach;
            unset($crop_xy);
            echo '        <td>' . "\n";
            $Image->crop($cropSizeLarger[0], $cropSizeLarger[1], $cropPoseLarger[0], $cropPoseLarger[1], 'white');
            $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)).'-crop-' . $cropSizeLarger[0] . 'x' . $cropSizeLarger[1] . '-start' . $cropPoseLarger[0] . ',' . $cropPoseLarger[1] . '-fillwhite';
            $save_result = $Image->save($file_name . $file_ext);
            echo '            <a href="' . $file_name . $file_ext . '"><img src="' . $file_name . $file_ext . '" alt="" class="thumbnail"></a><br>';
            echo 'Cropping ' . $cropSizeLarger[0] . '&times;' . $cropSizeLarger[1] . ' ';
            echo 'at ' . $cropPoseLarger[0] . ',' . $cropPoseLarger[1] . ' fill white'."\n";
            echo ' (Crop larger)' . "\n";
            debugImage($file_name . $file_ext, ['dataOnly' => true]);
            if ($save_result != true) {
                echo '<br>' . "\n";
                echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span>' . "\n";
            }
            unset($file_name, $save_result);
            echo '        </td>' . "\n";
            echo '    </tr>' . "\n";
            echo '</tbody></table>' . "\n";
            unset($file_ext, $Image);
        }
        echo "\n\n";
    }// endforeach;
    unset($cropSize, $cropSizeLarger, $cropPoseLarger, $cropPoses);
    echo "\n\n";
}// displayTestCrop


function displayTestSaveCrossExts(array $test_data_set)
{
    $saveExts = ['gif', 'jpg', 'png', 'webp'];
    echo '<h2>Crop &amp; Save across different extensions.</h2>' . "\n";
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
            $Image = new Rundiz\Image\Drivers\Gd($item['source_image_path']);
            echo '    </tr>' . "\n";

            echo '    <tr>' . "\n";
            echo '        <td>Save as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
                $Image->crop(800, 800, 'center', 0);
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '-crop-800x800-start-center,middle' .
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
            echo '        <td>Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveExts as $eachExt) {
                $linkTo = 'rdimage-gd-show-image.php?source_image_file=' . rawurldecode($item['source_image_path']) . 
                    '&amp;show_ext=' . $eachExt .
                    '&amp;act=crop' .
                    '&amp;width=' . 800 .
                    '&amp;height=' . 800 .
                    '&amp;startx=center' .
                    '&amp;starty=0';
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

    unset($saveExts);
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
        displayTestCrop($test_data_set);
        displayTestSaveCrossExts($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>