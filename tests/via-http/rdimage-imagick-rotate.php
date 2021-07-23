<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestRotate(array $test_data_set)
{
    $test_rotate = [90, 180, 270];
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
                    $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-'.str_replace(' ', '-', strtolower($img_type_name)).'-rotate'.$rotate;
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
        <h1>Imagick test rotate</h1>
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
        ];
        displayTestRotate($test_data_set);
        unset($test_data_set);
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>