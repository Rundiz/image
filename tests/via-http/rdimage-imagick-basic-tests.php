<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Imagick.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
require __DIR__.DIRECTORY_SEPARATOR.'include-imagick-functions.php';


function displayTestsConstructor(array $test_data_set)
{
    echo '<h2>Constructor image data.</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo 'Source image: ';
                if (is_file($item['source_image_path'])) {
                    echo '<a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a>'."\n";
                }
                $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
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
    $test_exts = ['gif', 'jpg', 'png'];
    echo '<h2>Resize the images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo '<table><tbody>';
                echo '<tr>'."\n";
                echo '<td>Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a></td>'."\n";
                echo '</tr>'."\n";
                $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
                $file_name = '../processed-images/rundiz-imagick-image-resize-'.str_replace(' ', '-', strtolower($img_type_name)).'-900x600';
                echo '<tr>'."\n";
                echo '<td>Save as</td>'."\n";
                foreach ($test_exts as $ext) {
                    $Image->resizeNoRatio(900, 400);
                    $save_result = $Image->save($file_name . '.' . $ext);
                    $Image->clear();
                    echo '<td>'."\n";
                    if ($save_result === true) {
                        echo '<img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail"><a href="' . $file_name . '.' . $ext . '">' . $ext . '</a>'."\n";
                        $img_data = getimagesize($file_name . '.' . $ext);
                        if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                            echo '(' . $img_data['mime'] . ')'."\n";
                        }
                    } else {
                        echo 'Error: '.$Image->status_msg."\n";
                    }
                    echo '</td>'."\n";
                    unset($img_data, $save_result);
                }
                echo '</tr>'."\n";
                unset($ext, $file_name, $Image);
                
                echo '<tr>' . "\n";
                echo '<td>Use show() method as</td>' . "\n";
                foreach ($test_exts as $ext) {
                    $image_class_show_url = 'rdimage-imagick-show-image.php?source_image_file='.$item['source_image_path'].'&amp;show_ext='.$ext.'&amp;act=resizenoratio&amp;width=900&amp;height=400';
                    echo '<td><img src="'.$image_class_show_url.'" alt="" class="thumbnail"><a href="'.$image_class_show_url.'">' . $ext . '</a></td>' . "\n";
                }
                echo '</tr>' . "\n";
                unset($ext);

                echo '</tbody></table>' . "\n";
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestResizes


function displayTestCrop(array $test_data_set)
{
    $test_crop = [[0, 0, 'transparent'], [90, 90, 'black'], ['center', 'middle', 'white']];
    echo '<h2>Crop the images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo '<table><tbody>';
                echo '<tr>' . "\n";
                echo '<td>Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a></td>'."\n";
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
                foreach ($test_crop as $crop_xy) {
                    if (isset($crop_xy[0]) && isset($crop_xy[1]) && isset($crop_xy[2])) {
                        $file_name = '../processed-images/rundiz-imagick-image-crop-'.str_replace(' ', '-', strtolower($img_type_name)).'-900x900-start'.$crop_xy[0].','.$crop_xy[1].'-fill-'.$crop_xy[2];
                        echo '<td>' . "\n";
                        echo '<img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"><br>Cropping at <a href="'.$file_name.$file_ext.'">'.$crop_xy[0].', '.$crop_xy[1].'</a> fill '.$crop_xy[2]."\n";
                        $Image->crop(900, 900, $crop_xy[0], $crop_xy[1], $crop_xy[2]);
                        $save_result = $Image->save($file_name.$file_ext);
                        if ($save_result != true) {
                            echo ' &nbsp; &nbsp; Error: '.$Image->status_msg.'<br>'."\n";
                        }
                        unset($file_name, $save_result);
                        $Image->clear();
                        echo '</td>' . "\n";
                    }
                }// endforeach;
                $Image->crop(2000, 2000, 'center', 'middle', 'white');
                $file_name = '../processed-images/rundiz-imagick-image-crop-'.str_replace(' ', '-', strtolower($img_type_name)).'-2000x2000-startcenter,middle-fill-white';
                echo '<td>' . "\n";
                echo '<img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"><br>Cropping at <a href="'.$file_name.$file_ext.'">center, middle</a> (2000x2000) fill white'."\n";
                echo '</td>' . "\n";
                $Image->save($file_name.$file_ext);
                $Image->clear();
                echo '</tr>' . "\n";
                unset($crop_xy, $file_ext, $Image);

                echo '</tbody></table>' . "\n";
            }
            echo "\n\n";
        }// endforeach;
    }
    echo "\n\n";
}// displayTestCrop


function displayTestRotate(array $test_data_set)
{
    $test_rotate = [90, 180, 270, 'hor', 'vrt', 'horvrt'];
    echo '<h2>Rotate/flip images</h2>'."\n";
    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h3>'.$img_type_name.'</h3>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo '<table><tbody>' . "\n";
                echo '<tr>' . "\n";
                echo '<td>Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a></td>'."\n";
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
                $i = 1;
                $countRotate = 1;
                foreach ($test_rotate as $rotate) {
                    $file_name = '../processed-images/rundiz-imagick-image-rotate-'.str_replace(' ', '-', strtolower($img_type_name)).'-rotate'.$rotate;
                    echo '<td>' . "\n";
                    echo '<img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"><br>Rotate at <a href="'.$file_name.$file_ext.'">'.$rotate.'</a>'."\n";
                    $Image->rotate($rotate);
                    $save_result = $Image->save($file_name.$file_ext);
                    if ($save_result != true) {
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg.'</span><br>'."\n";
                    }
                    echo '</td>' . "\n";
                    unset($file_name, $save_result);
                    $Image->clear();

                    $countRotate++;
                    $i++;
                    if ($i > 3) {
                        $i = 1;
                        if ($countRotate <= count($test_rotate)) {
                            echo '</tr>' . "\n";
                            echo '<tr>' . "\n";
                            echo '<td></td>' . "\n";
                        }
                    }
                }// endforeach;
                $Image->clear();
                echo '</tr>' . "\n";
                unset($file_ext, $Image, $rotate);

                echo '</tbody></table>' . "\n";
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
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Imagick basic tests</h1>
        <?php 
        $test_data_set = [
            'JPG' => [
                'source_image_path' => $source_image_jpg,
            ],
            'PNG' => [
                'source_image_path' => $source_image_png,
            ],
            'GIF' => [
                'source_image_path' => $source_image_gif,
            ],
            'Wrong image extension' => [
                'source_image_path' => $source_image_fake,
            ],
            'Fake image' => [
                'source_image_path' => $source_image_fake2,
            ],
            'Not exists image' => [
                'source_image_path' => $source_image_404,
            ],
        ];

        displayTestsConstructor($test_data_set);
        ?><hr>
        <?php
        $test_data_set = [
            'JPG' => [
                'source_image_path' => $source_image_jpg,
            ],
            'PNG' => [
                'source_image_path' => $source_image_png,
            ],
            'GIF' => [
                'source_image_path' => $source_image_gif,
            ],
            'GIF Animation' => [
                'source_image_path' => $source_image_animated_gif,
            ],
            'Wrong image extension' => [
                'source_image_path' => $source_image_fake,
            ],
        ];
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