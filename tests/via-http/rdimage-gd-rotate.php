<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


function displayTestRotate(array $test_data_set)
{
    $rotates = [90, 180, 270, 'hor', 'vrt', 'horvrt'];
    echo '<h1>Rotate &amp; flip the images (GD)</h1>'."\n";
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
            $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
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
            foreach ($rotates as $rotate) {
                $file_name = '../processed-images/' . basename(__FILE__, '.php') . '_src'.str_replace(' ', '-', strtolower($img_type_name)).'_'.$rotate;
                echo '<td>' . "\n";
                echo '<a href="'.$file_name.$file_ext.'"><img src="'.$file_name.$file_ext.'" alt="" class="thumbnail"></a><br>';
                echo 'Rotate '.$rotate.''."\n";
                $Image->rotate($rotate);
                $saveResult = $Image->save($file_name.$file_ext);
                if ($saveResult != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: '.$Image->status_msg.'</span><br>'."\n";
                }
                echo '</td>' . "\n";
                unset($file_name, $saveResult);
                $Image->clear();

                $countRotate++;
                $i++;
                if ($i > 3) {
                    $i = 1;
                    if ($countRotate <= count($rotates)) {
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
    unset($rotates);
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
        <?php
        displayTestRotate($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>