<?php
require_once 'include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

$rotate = 90;
$resize_w = 700;
$resize_h = 467;
$crop_width = 460;
$crop_height = 460;

$base_save_file_name = '../processed-images/' . basename(__FILE__, '.php') . '-resize-'.$resize_w.'x'.$resize_h.'-rotate-'.$rotate.'-crop-'.$crop_width.'x'.$crop_height;


function displayStandardMultiProcess(array $test_data_set)
{
    global $base_save_file_name;
    global $rotate, $resize_h, $resize_w, $crop_height, $crop_width;
    $test_exts = ['gif', 'jpg', 'png'];

    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h2>'.$img_type_name.'</h2>'."\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo '<table><tbody>' . "\n";
                echo '<tr>' . "\n";
                echo '<td>Source image</td><td><a href="'.$item['source_image_path'].'"><img src="'.$item['source_image_path'].'" alt="" class="thumbnail"></a></td>'."\n";
                echo '</tr>' . "\n";
                echo '<tr>' . "\n";
                echo '<td></td>' . "\n";
                foreach ($test_exts as $ext) {
                    $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
                    $source_image_exp = explode('.', $item['source_image_path']);
                    unset($source_image_exp);
                    $file_name = $base_save_file_name.'-source' . pathinfo($item['source_image_path'], PATHINFO_EXTENSION) . '.' . $ext;
                    $Image->resizeNoRatio($resize_w, $resize_h);
                    $Image->rotate($rotate);
                    $Image->crop($crop_width, $crop_height);
                    $Image->save($file_name);
                    $Image->clear();
                    echo '<td>' . "\n";
                    echo '<img src="'.$file_name.'" alt="" class="thumbnail"><br><a href="'.$file_name.'">'.$ext.'</a>';
                    $img_data = getimagesize($file_name);
                    if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                        echo ' (' . $img_data['mime'] . ')'."\n";
                        echo ' ' . $img_data[0] . 'x' . $img_data[1];
                    }
                    unset($img_data);
                    echo '</td>' . "\n";
                }// endforeach; ext
                unset($ext);
                echo '</tr>' . "\n";
                echo '</tbody></table>' . "\n";
            }
        }// endforeach;
        unset($img_type_name, $item);
    }// endif;

    unset($test_exts);
}// displayStandardMultiProcess
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>GD test multi process</h1>
        <p>resize at <?php echo $resize_w.'x'.$resize_h; ?> &gt; rotate at <?php echo $rotate; ?>&deg; &gt; crop at <?php echo $crop_width.'x'.$crop_height; ?></p>
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
        displayStandardMultiProcess($test_data_set);
        unset($test_data_set);
        ?> 
        <hr>
        <h2>Custom multi process</h2>
        <?php
        echo '<a href="'.$source_image_jpg.'">source image</a><img src="'.$source_image_jpg.'" alt="" class="thumbnail"><br>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_jpg);
        $file_ext = 'jpg';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-center,middle-rotate-'.$rotate.'-sourcejpg.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height, 'center', 'middle');
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (resize &gt; crop &gt; rotate)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-crop-'.$crop_width.'x'.$crop_height.'-resize-'.$resize_w.'x'.$resize_h.'-rotate-'.$rotate.'-sourcejpg.'.$file_ext;
        $Image->crop($crop_width, $crop_height);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; resize &gt; rotate)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-crop-'.$crop_width.'x'.$crop_height.'-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-sourcejpg.'.$file_ext;
        $Image->crop($crop_width, $crop_height);
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; rotate &gt; resize)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-'.$rotate.'-crop-'.$crop_width.'x'.$crop_height.'-resize-'.$resize_w.'x'.$resize_h.'-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; crop &gt; resize)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-crop-900x900-crop-600x600-crop-'.$crop_width.'x'.$crop_height.'-crop-400x400-sourcejpg.'.$file_ext;
        $Image->crop(900, 900);
        $Image->crop(600, 600);
        $Image->crop($crop_width, $crop_height);
        $Image->crop(400, 400);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; crop &gt; crop &gt; crop)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-watermarkimage-right,bottom-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop &gt; watermark image)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/' . basename(__FILE__, '.php') . '-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-watermarktext-right,bottom-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->watermarkText('Rundiz watermark สั้น ญู ให้ ทดสอบสระ.', '../source-images/cschatthai.ttf', 'right', 'bottom', 13);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop &gt; watermark text)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        unset($file_name, $Image);
        ?> 
        <hr>
        <?php
        // -------------------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>