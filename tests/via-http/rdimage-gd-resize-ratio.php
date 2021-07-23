<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

function displayTestResizeRatio(array $test_data_set)
{
    foreach ($test_data_set as $main_ext => $items) {
        echo '<h2><a href="'.$items['source_image_path'].'">'.$main_ext.'</a></h2>'."\n";
        echo '<table><tbody>' . "\n";
        echo '<tr><td>Source image</td>';
        echo '<td>';
        echo '<a href="'.$items['source_image_path'].'"><img src="'.$items['source_image_path'].'" alt="" class="thumbnail"></a><br>';
        list($swidth, $sheight) = getimagesize($items['source_image_path']);
        echo $swidth . 'x' . $sheight;
        unset($sheight, $swidth);
        echo '</td></tr>' . "\n";
        $Image = new \Rundiz\Image\Drivers\Gd($items['source_image_path']);
        $master_dim = 'auto';
        echo '<tr>' . "\n";
        echo '<td colspan="5"><h3>Not allow resize larger, master dimension '.$master_dim.'</h3></td>'."\n";
        echo '</tr>' . "\n";
        $base_save_file_name = '../processed-images/rundiz-gd-image-resizeratio-testpage';
        $base_save_file_name2 = $base_save_file_name . '-nolarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo '<tr>' . "\n";
        echo '<td>Saved as</td>' . "\n";
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            echo '<td>' . "\n";
            echo '<img src="'.$base_save_file_name3.'" alt="" class="thumbnail"><br>';
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a>';
            echo '</td>' . "\n";
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        echo '</tr>' . "\n";
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        // allow resize larger
        $Image->allow_resize_larger = true;
        echo '<tr>' . "\n";
        echo '<td colspan="5"><h3>Allow resize larger, master dimension '.$master_dim.'</h3></td>'."\n";
        echo '</tr>' . "\n";
        $base_save_file_name = '../processed-images/rundiz-gd-image-resizeratio-testpage';
        $base_save_file_name2 = $base_save_file_name . '-allowlarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo '<tr>' . "\n";
        echo '<td>Saved as</td>' . "\n";
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            echo '<td>' . "\n";
            echo '<img src="'.$base_save_file_name3.'" alt="" class="thumbnail"><br>';
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a>';
            echo '</td>' . "\n";
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        echo '</tr>' . "\n";
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        // not allow resize larger, master dim = height
        $master_dim = 'height';
        $Image->allow_resize_larger = false;
        echo '<tr>' . "\n";
        echo '<td colspan="5"><h3>Not allow resize larger, master dimension '.$master_dim.'</h3></td>'."\n";
        echo '</tr>' . "\n";
        $base_save_file_name = '../processed-images/rundiz-gd-image-resizeratio-testpage';
        $base_save_file_name2 = $base_save_file_name . '-nolarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo '<tr>' . "\n";
        echo '<td>Saved as</td>' . "\n";
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            echo '<td>' . "\n";
            echo '<img src="'.$base_save_file_name3.'" alt="" class="thumbnail"><br>';
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a>';
            echo '</td>' . "\n";
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        echo '</tr>' . "\n";
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        unset($Image, $master_dim);

        echo '</tbody></table>' . "\n";
    }// endforeach;
    unset($items, $main_ext);
}// displayTestResizeRatio
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>GD test resize by aspect ratio</h1>
        <?php
        $resize_sizes = [
            [300, 300],
            [900, 400],
            [400, 700],
            [2100, 1500],
        ];
        $test_data_set = [
            'JPG' => [
                'source_image_path' => $source_image_jpg,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'jpg',
            ],
            'PNG' => [
                'source_image_path' => $source_image_png,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'png',
            ],
            'GIF' => [
                'source_image_path' => $source_image_gif,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'gif',
            ],
        ];
        displayTestResizeRatio($test_data_set);
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>