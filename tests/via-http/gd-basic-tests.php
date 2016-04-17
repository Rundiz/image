<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestsConstructor(array $test_data_set)
{
    echo '<h2>Constructor image data.</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo 'Source image: ';
                if (is_file($item['source_image_path'])) {
                    echo '<a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" style="width: 300px;"></a>'."\n";
                }
                $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
                echo '<pre class="mini-data-box">'.print_r($Image, true).'</pre>'."\n";
                unset($Image);
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestsConstructor


function displayTestResizes(array $test_data_set)
{
    $test_exts = array('gif', 'jpg', 'png');
    echo '<h2>Resize the images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo 'Source image: <a href="'.$item['source_image_path'].'">link</a>.<br>'."\n";
                $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
                $Image->resizeNoRatio(900, 600);
                $file_name = '../processed-images/rundiz-gd-image-resize-'.str_replace(' ', '-', strtolower($img_type_name)).'-900x600';
                echo 'Save as ';
                foreach ($test_exts as $ext) {
                    $save_result = $Image->save($file_name . '.' . $ext);
                    if ($save_result === true) {
                        echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a>'."\n";
                        $img_data = getimagesize($file_name . '.' . $ext);
                        if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                            echo '(' . $img_data['mime'] . ')'."\n";
                        }
                    } else {
                        echo '<br>Error: '.$Image->status_msg."\n";
                    }
                    unset($img_data, $save_result);
                }
                $Image->clear();
                unset($ext, $file_name, $Image);
                
                echo '<br>Use show() method as ';
                foreach ($test_exts as $ext) {
                    echo ' <a href="gd-show-image.php?source_image_file='.$item['source_image_path'].'&amp;show_ext='.$ext.'&amp;act=resizenoratio&amp;width=900&amp;height=600">' . $ext . '</a>'."\n";
                }
                unset($ext);
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestResizes


function displayTestCrop(array $test_data_set)
{
    $test_crop = array(array(0, 0), array(90, 90), array('center', 'middle'));
    echo '<h2>Crop the images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo 'Source image: <a href="'.$item['source_image_path'].'">link</a>.<br>'."\n";
                $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
                $source_image_exp = explode('.', $item['source_image_path']);
                $file_ext = '.';
                if (is_array($source_image_exp)) {
                    $file_ext .= $source_image_exp[count($source_image_exp)-1];
                }
                unset($source_image_exp);
                foreach ($test_crop as $crop_xy) {
                    if (isset($crop_xy[0]) && isset($crop_xy[1])) {
                        $file_name = '../processed-images/rundiz-gd-image-crop-'.str_replace(' ', '-', strtolower($img_type_name)).'-900x900-start'.$crop_xy[0].','.$crop_xy[1];
                        echo 'Cropping at <a href="'.$file_name.$file_ext.'">'.$crop_xy[0].', '.$crop_xy[1].'</a><br>'."\n";
                        $Image->crop(900, 900, $crop_xy[0], $crop_xy[1]);
                        $save_result = $Image->save($file_name.$file_ext);
                        if ($save_result != true) {
                            echo ' &nbsp; &nbsp; Error: '.$Image->status_msg.'<br>'."\n";
                        }
                        unset($file_name, $save_result);
                        $Image->clear();
                    }
                }// endforeach;
                $Image->clear();
                unset($crop_xy, $file_ext, $Image);
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestCrop


function displayTestRotate(array $test_data_set)
{
    $test_rotate = array(90, 180, 270, 'hor', 'vrt', 'horvrt');
    echo '<h2>Rotate/flip images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo 'Source image: <a href="'.$item['source_image_path'].'">link</a>.<br>'."\n";
                $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
                $source_image_exp = explode('.', $item['source_image_path']);
                $file_ext = '.';
                if (is_array($source_image_exp)) {
                    $file_ext .= $source_image_exp[count($source_image_exp)-1];
                }
                unset($source_image_exp);
                foreach ($test_rotate as $rotate) {
                    $file_name = '../processed-images/rundiz-gd-image-rotate-'.str_replace(' ', '-', strtolower($img_type_name)).'-rotate'.$rotate;
                    echo 'Rotate at <a href="'.$file_name.$file_ext.'">'.$rotate.'</a><br>'."\n";
                    $Image->rotate($rotate);
                    $save_result = $Image->save($file_name.$file_ext);
                    if ($save_result != true) {
                        echo ' &nbsp; &nbsp; Error: '.$Image->status_msg.'<br>'."\n";
                    }
                    unset($file_name, $save_result);
                    $Image->clear();
                }// endforeach;
                $Image->clear();
                unset($file_ext, $Image, $rotate);
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestRotate
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <style>
            body {
                background-color: #fff;
                color: #333;
                margin: 20px;
                padding: 0;
            }
            .mini-data-box {
                background-color: #eee;
                height: 150px;
                overflow: auto;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        <h1>GD basic tests</h1>
        <?php 
        $test_data_set = array(
            'JPG' => array(
                'source_image_path' => $source_image_jpg,
            ),
            'PNG' => array(
                'source_image_path' => $source_image_png,
            ),
            'GIF' => array(
                'source_image_path' => $source_image_gif,
            ),
            'Wrong image extension' => array(
                'source_image_path' => $source_image_fake,
            ),
            'Fake image' => array(
                'source_image_path' => $source_image_fake2,
            ),
            'Not exists image' => array(
                'source_image_path' => $source_image_404,
            ),
        );

        displayTestsConstructor($test_data_set);
        ?><hr>
        <?php
        $test_data_set = array(
            'JPG' => array(
                'source_image_path' => $source_image_jpg,
            ),
            'PNG' => array(
                'source_image_path' => $source_image_png,
            ),
            'GIF' => array(
                'source_image_path' => $source_image_gif,
            ),
            'Wrong image extension' => array(
                'source_image_path' => $source_image_fake,
            ),
        );
        displayTestResizes($test_data_set);
        ?><hr>
        <?php
        displayTestCrop($test_data_set);
        ?><hr>
        <?php
        displayTestRotate($test_data_set);
        ?><hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?>
    </body>
</html>