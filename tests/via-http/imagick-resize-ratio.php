<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Imagick.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

function displayTestResizeRatio(array $test_data_set)
{
    foreach ($test_data_set as $main_ext => $items) {
        echo '<h2><a href="'.$items['source_image_path'].'">'.$main_ext.'</a><img src="'.$items['source_image_path'].'" alt="" class="thumbnail"></h2>'."\n";
        $Image = new \Rundiz\Image\Drivers\Imagick($items['source_image_path']);
        $master_dim = 'auto';
        echo '<h3>Not allow resize larger, master dimension '.$master_dim.'</h3>'."\n";
        $base_save_file_name = '../processed-images/rundiz-imagick-image-resizeratio-testpage-source-'.strtolower(str_replace(array('\\', '/', ' '), '', $main_ext));
        $base_save_file_name2 = $base_save_file_name . '-nolarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo 'Saved as: ';
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a><img src="'.$base_save_file_name3.'" alt="" class="thumbnail"> ';
            echo ' &nbsp; &nbsp;';
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        echo '<br>';
        // allow resize larger
        $Image->allow_resize_larger = true;
        echo '<h3>Allow resize larger, master dimension '.$master_dim.'</h3>'."\n";
        $base_save_file_name = '../processed-images/rundiz-imagick-image-resizeratio-testpage-source-'.strtolower(str_replace(array('\\', '/', ' '), '', $main_ext));
        $base_save_file_name2 = $base_save_file_name . '-allowlarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo 'Saved as: ';
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a><img src="'.$base_save_file_name3.'" alt="" class="thumbnail"> ';
            echo ' &nbsp; &nbsp;';
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        echo '<br>';
        // not allow resize larger, master dim = height
        $master_dim = 'height';
        $Image->allow_resize_larger = false;
        echo '<h3>Not allow resize larger, master dimension '.$master_dim.'</h3>'."\n";
        $base_save_file_name = '../processed-images/rundiz-imagick-image-resizeratio-testpage-source-'.strtolower(str_replace(array('\\', '/', ' '), '', $main_ext));
        $base_save_file_name2 = $base_save_file_name . '-nolarger-masterdim-'.$master_dim;
        $Image->master_dim = $master_dim;
        echo 'Saved as: ';
        foreach ($items['resize_sizes'] as $sizes) {
            $base_save_file_name3 = $base_save_file_name2 . '-resize-'.$sizes[0].'x'.$sizes[1].'.'.$items['save_as'];
            $Image->resize($sizes[0], $sizes[1]);
            $Image->save($base_save_file_name3);
            $Image->clear();
            list($saved_w, $saved_h) = getimagesize($base_save_file_name3);
            if ($saved_w != $sizes[0] || $saved_h != $sizes[1]) {
                echo $sizes[0].'x'.$sizes[1].' =&gt; ';
            }
            echo '<a href="'.$base_save_file_name3.'">'.$saved_w.'x'.$saved_h.'</a><img src="'.$base_save_file_name3.'" alt="" class="thumbnail"> ';
            echo ' &nbsp; &nbsp;';
            unset($base_save_file_name3, $saved_h, $saved_w);
        }
        unset($base_save_file_name, $base_save_file_name2, $sizes);
        echo '<br>';
        unset($Image, $master_dim);
    }
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
        <h1>Imagick test resize by aspect ratio</h1>
        <?php
        $resize_sizes = array(
            array(300, 300),
            array(900, 400),
            array(400, 700),
            array(2100, 1500),
        );
        $test_data_set = array(
            'JPG' => array(
                'source_image_path' => $source_image_jpg,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'jpg',
            ),
            'PNG' => array(
                'source_image_path' => $source_image_png,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'png',
            ),
            'GIF' => array(
                'source_image_path' => $source_image_gif,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'gif',
            ),
            'Animated GIF' => array(
                'source_image_path' => $source_image_animated_gif,
                'resize_sizes' => $resize_sizes,
                'save_as' => 'gif',
            ),
        );
        displayTestResizeRatio($test_data_set);
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>