<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

$source_font = '../source-images/cschatthai.ttf';

function displayTestWatermarkText(array $test_data_set)
{
    foreach ($test_data_set as $image_ext => $items) {
        echo '<h3><a href="'.$items['source_image_path'].'">'.$image_ext.'</a><img src="'.$items['source_image_path'].'" alt="" class="thumbnail"></h3>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($items['source_image_path']);
        $main_save_file_name = '../processed-images/' . basename(__FILE__, '.php') . '-source-'.  strtolower($image_ext);
        foreach ($items['watermark_fonts'] as $wm_font) {
            $font_path = '../source-images/'.$wm_font;
            $wmext_save_file_name = $main_save_file_name.'-font-'.$wm_font;
            echo '<h4>watermark font '.$wm_font.'</h4>'."\n";
            echo '<table><tbody>' . "\n";
            foreach ($items['watermark_positions'] as $wm_pos) {
                $save_file_name = $wmext_save_file_name.'-position-'.$wm_pos[0].','.$wm_pos[1];
                echo '<tr>' . "\n";
                echo '<td>position '.$wm_pos[0].', '.$wm_pos[1].'</td>' . "\n";
                foreach ($items['save_exts'] as $save_ext) {
                    $Image->watermarkText('Rundiz watermark สั้น ญู ให้ ทดสอบสระ.', $font_path, $wm_pos[0], $wm_pos[1], 15);
                    $Image->save($save_file_name.'.'.$save_ext);
                    $Image->clear();
                    echo '<td>';
                    echo '<img src="'.$save_file_name.'.'.$save_ext.'" alt="" class="thumbnail"><br><a href="'.$save_file_name.'.'.$save_ext.'">save as '.$save_ext.'</a>';
                    echo '</td>' . "\n";
                }
                echo '</tr>'."\n";
                unset($save_file_name);
            }
            echo '</tbody></table>'."\n";
            unset($font_path, $wmext_save_file_name, $wm_pos);
        }
        unset($Image, $main_save_file_name, $wm_font);
    }
}// displayTestWatermarkText
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>GD test watermark</h1>
        <h2>Watermark text</h2>
        <?php
        $save_exts = ['jpg', 'png', 'gif'];
        $watermark_fonts = ['cschatthai.ttf'];
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
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ],
            /*'PNG' => array(
                'source_image_path' => $source_image_png,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ),
            'GIF' => array(
                'source_image_path' => $source_image_gif,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ),*/
        ];
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        $test_data_set = [
            'PNG' => [
                'source_image_path' => $source_image_png,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ],
        ];
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        $test_data_set = [
            'GIF' => [
                'source_image_path' => $source_image_gif,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ],
        ];
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>