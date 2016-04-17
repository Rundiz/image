<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

$source_font = '../source-images/cschatthai.ttf';

function displayTestWatermarkText(array $test_data_set)
{
    foreach ($test_data_set as $image_ext => $items) {
        echo '<h3><a href="'.$items['source_image_path'].'">'.$image_ext.'</a></h3>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($items['source_image_path']);
        $main_save_file_name = '../processed-images/rundiz-gd-image-watermarkText-testpage-source-'.  strtolower($image_ext);
        foreach ($items['watermark_fonts'] as $wm_font) {
            $font_path = '../source-images/'.$wm_font;
            $wmext_save_file_name = $main_save_file_name.'-font-'.$wm_font;
            echo '<h4>watermark font '.$wm_font.'</h4>'."\n";
            foreach ($items['watermark_positions'] as $wm_pos) {
                $save_file_name = $wmext_save_file_name.'-position-'.$wm_pos[0].','.$wm_pos[1];
                echo 'position '.$wm_pos[0].', '.$wm_pos[1].'<br>';
                foreach ($items['save_exts'] as $save_ext) {
                    $Image->watermarkText('Rundiz watermark ภาษาไทย สั้น ญู ให้.', $font_path, $wm_pos[0], $wm_pos[1], 15);
                    $Image->save($save_file_name.'.'.$save_ext);
                    $Image->clear();
                    echo '<a href="'.$save_file_name.'.'.$save_ext.'">save as '.$save_ext.'</a> ';
                }
                echo '<br>'."\n";
                unset($save_file_name);
            }
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
        <h1>GD test watermark</h1>
        <h2>Watermark text</h2>
        <?php
        $save_exts = array('jpg', 'png', 'gif');
        $watermark_fonts = array('cschatthai.ttf');
        $watermark_positions = array(
            array(100, 300),
            array('left', 'top'),
            array('left', 'middle'),
            array('left', 'bottom'),
            array('center', 'top'),
            array('center', 'middle'),
            array('center', 'bottom'),
            array('right', 'top'),
            array('right', 'middle'),
            array('right', 'bottom'),
        );
        $test_data_set = array(
            'JPG' => array(
                'source_image_path' => $source_image_jpg,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ),
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
        );
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        $test_data_set = array(
            'PNG' => array(
                'source_image_path' => $source_image_png,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ),
        );
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        $test_data_set = array(
            'GIF' => array(
                'source_image_path' => $source_image_gif,
                'watermark_fonts' => $watermark_fonts,
                'watermark_positions' => $watermark_positions,
                'save_exts' => $save_exts,
            ),
        );
        displayTestWatermarkText($test_data_set);
        ?><hr>
        <?php
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>