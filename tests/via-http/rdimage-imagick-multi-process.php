<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';

$rotate = 90;
$resize_w = 700;
$resize_h = 467;
$crop_width = 460;
$crop_height = 460;

$base_save_file_name = '../processed-images/' . autoImageFilename() . '-resize-' . $resize_w . 'x' . $resize_h . '-rotate-' . $rotate . '-crop-' . $crop_width . 'x' . $crop_height;


function displayStandardMultiProcess(array $test_data_set)
{
    global $base_save_file_name;
    global $rotate, $resize_h, $resize_w, $crop_height, $crop_width;
    $test_exts = ['gif', 'jpg', 'png', 'webp'];

    if (is_array($test_data_set)) {
        foreach ($test_data_set as $img_type_name => $item) {
            echo '<h2>' . $img_type_name . '</h2>' . "\n";
            if (is_array($item) && array_key_exists('source_image_path', $item)) {
                echo '<table><tbody>' . "\n";
                echo '<tr>' . "\n";
                echo '<td>Source image</td>' . "\n";
                echo '<td>' . "\n";
                debugImage($item['source_image_path']);
                echo '</td>' . "\n";
                echo '</tr>' . "\n";
                echo '<tr>' . "\n";
                echo '<td></td>' . "\n";
                foreach ($test_exts as $ext) {
                    $Image = new \Rundiz\Image\Drivers\Imagick($item['source_image_path']);
                    $source_image_exp = explode('.', $item['source_image_path']);
                    unset($source_image_exp);
                    $file_name = $base_save_file_name . '-source-' . str_replace(' ', '-', strtolower($img_type_name)) . '.' . $ext;
                    $Image->resizeNoRatio($resize_w, $resize_h);
                    $Image->rotate($rotate);
                    $Image->crop($crop_width, $crop_height);
                    $Image->save($file_name);
                    echo '<td>' . "\n";
                    debugImage($file_name);
                    if ($Image->status !== true) {
                        echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span>' . "\n";
                    }
                    echo '</td>' . "\n";
                    $Image->clear();
                    unset($Image);
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
        <h1>Imagick test multi process</h1>
        <p>
            resize at <?php echo $resize_w . '&times;' . $resize_h; ?> 
            &rarr; rotate at <?php echo $rotate; ?>&deg; 
            &rarr; crop at <?php echo $crop_width . '&times;' . $crop_height; ?>
        </p>
        <?php
        displayStandardMultiProcess($test_data_set + $test_data_anim);
        ?> 
        <hr>
        <h2>Custom multi process</h2>
        <?php
        $custom_multiprocess_source_img = $source_image_jpg;
        ?> 
        <table>
            <tbody>
                <tr>
                    <td>Source image</td>
                    <td colspan="2">
                        <?php
                        debugImage($custom_multiprocess_source_img);
                        $Image = new \Rundiz\Image\Drivers\Imagick($custom_multiprocess_source_img);
                        $saveExt = 'jpg';
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td>resize &rarr; crop &rarr; add watermark image &rarr; rotate</td>
                    <td colspan="2">
                        <?php
                        $file_name = '../processed-images/' . autoImageFilename() . '-resize-' . $resize_w . 'x' . $resize_h . '-crop-' . $crop_width . 'x' . $crop_height . '-watermarkimage-right,bottom-rotate-' . $rotate 
                            . '-source-' . pathinfo($custom_multiprocess_source_img, PATHINFO_EXTENSION) . '.' . $saveExt;
                        $Image->resizeNoRatio($resize_w, $resize_h);
                        $Image->crop($crop_width, $crop_height);
                        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
                        $Image->rotate($rotate);
                        $Image->save($file_name);
                        $Image->clear();
                        debugImage($file_name);
                        ?> 
                    </td>
                </tr>
                <tr>
                    <td>add watermark image &rarr; resize &rarr; add watermark image &rarr; resize &rarr; add watermark image</td>
                    <td>
                        <?php
                        $file_name = '../processed-images/' . autoImageFilename() . '-wmimg-right,bottom-resize1000x1000-wmimg-right,bottom-resize450x450-wmimg-right,bottom' 
                            . '-source-' . pathinfo($custom_multiprocess_source_img, PATHINFO_EXTENSION) . '.' . $saveExt;
                        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
                        $Image->resize(1000, 1000);
                        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
                        $Image->resize(450, 450);
                        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
                        $Image->save($file_name);
                        $Image->clear();
                        debugImage($file_name);
                        ?> 
                    </td>
                    <td>This image, you must see watermark at bottom right in 3 different sizes.</td>
                </tr>
                <tr>
                    <td>rotate &rarr; resize &rarr; crop &rarr; add watermark text</td>
                    <td colspan="2">
                        <?php
                        $file_name = '../processed-images/' . autoImageFilename() . '-rotate-' . $rotate . '-resize-' . $resize_w . 'x' . $resize_h . '-crop-' . $crop_width . 'x' . $crop_height . '-watermarktext-right,bottom' 
                            . '-source-' . pathinfo($custom_multiprocess_source_img, PATHINFO_EXTENSION) . '.' . $saveExt;
                        $Image->rotate($rotate);
                        $Image->resizeNoRatio($resize_w, $resize_h);
                        $Image->crop($crop_width, $crop_height);
                        $Image->wmTextBottomPadding = 6;
                        $Image->watermarkText('Rundiz watermark สั้น ญู ให้ ทดสอบสระ.', '../source-images/font.ttf', 'right', 'bottom', 14, 'transwhitetext', 20);
                        $Image->save($file_name);
                        $Image->clear();
                        debugImage($file_name);
                        ?> 
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
        unset($file_name, $Image);
        unset($custom_multiprocess_source_img);
        ?> 
        <hr>
        <?php
        // -------------------------------------------------------------------------------------------------------------------
        include 'includes/include-page-footer.php';
        ?> 
    </body>
</html>