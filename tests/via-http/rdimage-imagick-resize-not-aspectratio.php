<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestResizeNotRatio(array $test_data_set)
{
    $saveAsExt = ['gif', 'jpg', 'png'];
    $resizeDim = [900, 400];
    echo '<h1>Resize by NOT aspect ratio (Imagick)</h1>'."\n";
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
            $file_name = '../processed-images/' . basename(__FILE__, '.php') . '_src'.str_replace(' ', '-', strtolower($img_type_name)).'_' . $resizeDim[0] . 'x' . $resizeDim[1];
            echo '<tr>' . "\n";
            echo '<td>Save as</td>'."\n";
            foreach ($saveAsExt as $ext) {
                $Image->resizeNoRatio($resizeDim[0], $resizeDim[1]);
                $save_result = $Image->save($file_name . '.' . $ext);
                $Image->clear();
                echo '<td>' . "\n";
                if ($save_result === true) {
                    echo '<a href="' . $file_name . '.' . $ext . '"><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail"></a><br>';
                    echo 'Extension: ' . $ext . '<br>' . "\n";
                    $img_data = getimagesize($file_name . '.' . $ext);
                    if (is_array($img_data) && isset($img_data[0]) && isset($img_data[1])) {
                        echo $img_data[0] . 'x' . $img_data[1] . ' ';
                    }
                    if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                        echo ' Mime type: ' . $img_data['mime'];
                    }
                } else {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg . '</span>' . "\n";
                }
                echo '</td>' . "\n";
                unset($img_data, $save_result);
            }
            echo '</tr>'."\n";
            unset($ext, $file_name, $Image);

            echo '<tr>' . "\n";
            echo '<td>Use <code>show()</code> method as</td>' . "\n";
            foreach ($saveAsExt as $ext) {
                $image_class_show_url = 'rdimage-imagick-show-image.php?source_image_file='.$item['source_image_path'].'&amp;show_ext='.$ext.'&amp;act=resizenoratio&amp;width=' . $resizeDim[0] . '&amp;height=' . $resizeDim[1];
                echo '<td><a href="'.$image_class_show_url.'"><img src="'.$image_class_show_url.'" alt="" class="thumbnail"></a><br>';
                echo 'Extension: '. $ext . '</td>' . "\n";
            }
            echo '</tr>' . "\n";
            unset($ext);

            echo '</tbody></table>' . "\n";
        }
        echo "\n\n";
    }// endforeach;
    unset($resizeDim, $saveAsExt);
    echo "\n\n";
}// displayTestResizeNotRatio
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
        displayTestResizeNotRatio($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>