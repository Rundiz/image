<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestCrop(array $test_data_set)
{
    $cropPoses = [[0, 0, 'transparent'], [90, 90, 'black'], ['center', 'middle', 'white']];
    $cropSize = [900, 900];
    $cropSizeLarger = [2000, 2000];
    $cropPoseLarger = ['center', 'middle'];
    echo '<h1>Crop the images (Imagick)</h1>'."\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>'.$img_type_name.'</h3>'."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            echo '<table><tbody>' . "\n";
            echo '<tr>' . "\n";
            echo '<td>Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a><br>';
            $imgData = getimagesize($item['source_image_path']);
            if (is_array($imgData)) {
                echo $imgData[0] . 'x' . $imgData[1] . ' ';
                echo 'Mime type: ' . $imgData['mime'];
            }
            unset($imgData);
            echo '</td>'."\n";
            echo '</tr>' . "\n";
            $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
            $source_image_exp = explode('.', $item['source_image_path']);
            $file_ext = '.';
            if (is_array($source_image_exp)) {
                $file_ext .= $source_image_exp[count($source_image_exp)-1];
            }
            unset($source_image_exp);
            echo '<tr>' . "\n";
            echo '<td></td>' . "\n";
            foreach ($cropPoses as $crop_xy) {
                if (isset($crop_xy[0]) && isset($crop_xy[1]) && isset($crop_xy[2])) {
                    $file_name = '../processed-images/' . basename(__FILE__, '.php') . '_src'.str_replace(' ', '-', strtolower($img_type_name)).'_' . $cropSize[0] . 'x' . $cropSize[1] . '-start'.$crop_xy[0].','.$crop_xy[1].'-fill'.$crop_xy[2];
                    echo '<td>' . "\n";
                    echo '<a href="'.$file_name.$file_ext.'"><img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"></a><br>';
                    echo 'Cropping ' . $cropSize[0] . 'x' . $cropSize[1] . ' ';
                    echo 'at '.$crop_xy[0].','.$crop_xy[1].' fill '.$crop_xy[2]."\n";
                    $Image->crop($cropSize[0], $cropSize[1], $crop_xy[0], $crop_xy[1], $crop_xy[2]);
                    $save_result = $Image->save($file_name.$file_ext);
                    if ($save_result != true) {
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg.'</span>'."\n";
                    }
                    unset($file_name, $save_result);
                    $Image->clear();
                    echo '</td>' . "\n";
                }
            }// endforeach;
            $Image->crop($cropSizeLarger[0], $cropSizeLarger[1], $cropPoseLarger[0], $cropPoseLarger[1], 'white');
            $file_name = '../processed-images/' . basename(__FILE__, '.php') . '_src'.str_replace(' ', '-', strtolower($img_type_name)).'_' . $cropSizeLarger[0] . 'x' . $cropSizeLarger[1] . '-start' . $cropPoseLarger[0] . ',' . $cropPoseLarger[1] . '-fillwhite';
            echo '<td>' . "\n";
            echo '<a href="'.$file_name.$file_ext.'"><img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"></a><br>';
            echo 'Cropping ' . $cropSizeLarger[0] . 'x' . $cropSizeLarger[1] . ' ';
            echo 'at ' . $cropPoseLarger[0] . ',' . $cropPoseLarger[1] . ' fill white'."\n";
            echo ' (Crop larger)' . "\n";
            echo '</td>' . "\n";
            $Image->save($file_name.$file_ext);
            $Image->clear();
            echo '</tr>' . "\n";
            unset($crop_xy, $file_ext, $Image);

            echo '</tbody></table>' . "\n";
        }
        echo "\n\n";
    }// endforeach;
    unset($cropSize, $cropSizeLarger, $cropPoseLarger, $cropPoses);
    echo "\n\n";
}// displayTestCrop
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
        // add animated GIF after GIF.
        $test_data_set = array_slice($test_data_set, 0, 3, true) +
            ['GIF Animation' => [
                'source_image_path' => $source_image_animated_gif,
            ]] +
        array_slice($test_data_set, 3, NULL, true);
        // display test
        displayTestCrop($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>