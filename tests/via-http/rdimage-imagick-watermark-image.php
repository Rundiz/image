<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Imagick.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

function displayTestWatermarkImage(array $test_data_set)
{
    foreach ($test_data_set as $image_ext => $items) {
        echo '<h3><a href="'.$items['source_image_path'].'">'.$image_ext.'</a><img src="'.$items['source_image_path'].'" alt="" class="thumbnail"></h3>'."\n";
        $Image = new \Rundiz\Image\Drivers\Imagick($items['source_image_path']);
        $main_save_file_name = '../processed-images/' . basename(__FILE__, '.php') . '-source-'.  strtolower($image_ext);
        foreach ($items['watermark_exts'] as $wm_ext) {
            $wmext_save_file_name = $main_save_file_name.'-watermark-'.$wm_ext;
            $watermark_image_path = '../source-images/watermark.'.$wm_ext;
            echo '<h4>watermark from <a href="'.$watermark_image_path.'">'.$wm_ext.'</a><img src="'.$watermark_image_path.'" alt="" class="thumbnail"></h4>'."\n";
            echo '<table><tbody>' . "\n";
            foreach ($items['watermark_positions'] as $wm_pos) {
                $save_file_name = $wmext_save_file_name.'-position-'.$wm_pos[0].','.$wm_pos[1];
                echo '<tr>' . "\n";
                echo '<td>position '.$wm_pos[0].', '.$wm_pos[1].'</td>' . "\n";
                foreach ($items['watermark_exts'] as $save_ext) {
                    $Image->watermarkImage($watermark_image_path, $wm_pos[0], $wm_pos[1]);
                    $Image->save($save_file_name.'.'.$save_ext);
                    $Image->clear();
                    echo '<td>';
                    echo '<img src="'.$save_file_name.'.'.$save_ext.'" alt="" class="thumbnail"><br><a href="'.$save_file_name.'.'.$save_ext.'">save as '.$save_ext.'</a>';
                    echo '</td>' . "\n";
                }
                echo '</tr>' . "\n";
                unset($save_file_name);
            }
            echo '</tbody></table>'."\n";
            unset($watermark_image_path, $wmext_save_file_name, $wm_pos);
        }
        unset($Image, $main_save_file_name);
    }
}// displayTestWatermarkImage
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Imagick test watermark</h1>
        <h2>Watermark image</h2>
        <?php
        $watermark_exts = ['jpg', 'gif', 'png'];
        $watermark_positions = [
            [100, 300],
            ['left', 'top'],
            ['left', 'middle'],
            ['left', 'bottom'],
            ['center', 'top'],
            ['center', 'middle'],
            ['center', 'bottom'],
            ['right', 'top'],
            ['right', 'middle'],
            ['right', 'bottom'],
        ];
        $test_data_set = [
            'JPG' => [
                'source_image_path' => $source_image_jpg,
                'watermark_exts' => $watermark_exts,
                'watermark_positions' => $watermark_positions,
            ],
            'PNG' => [
                'source_image_path' => $source_image_png,
                'watermark_exts' => $watermark_exts,
                'watermark_positions' => $watermark_positions,
            ],
            'GIF' => [
                'source_image_path' => $source_image_gif,
                'watermark_exts' => $watermark_exts,
                'watermark_positions' => $watermark_positions,
            ],
            'Animated GIF' => [
                'source_image_path' => $source_image_animated_gif,
                'watermark_exts' => $watermark_exts,
                'watermark_positions' => $watermark_positions,
            ],
        ];
        unset($watermark_exts, $watermark_positions);
        displayTestWatermarkImage($test_data_set);
        unset($test_data_set);
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>