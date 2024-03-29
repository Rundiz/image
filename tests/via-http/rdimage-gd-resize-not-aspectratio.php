<?php
require_once 'includes/include-rundiz-image.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
include_once 'includes/include-functions.php';


function displayTestResizeNotRatio(array $test_data_set)
{
    $resizeDim = [900, 400];
    echo '<h1>Resize by NOT aspect ratio (GD)</h1>' . "\n";
    foreach ($test_data_set as $img_type_name => $item) {
        echo '<h3>' . $img_type_name . '</h3>' ."\n";
        if (is_array($item) && array_key_exists('source_image_path', $item)) {
            $file_ext = '.' . pathinfo($item['source_image_path'], PATHINFO_EXTENSION);
            $isSupported = true;
            if (strtolower($file_ext) === '.webp') {
                $WebP = new Rundiz\Image\Extensions\WebP($item['source_image_path']);
                $isSupported = $WebP->isGDSupported();
                unset($WebP);
            }// endif check webp extension.
            echo '<table><tbody>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td>Source image</td>' . "\n";
            echo '        <td>' . "\n";
            debugImage($item['source_image_path']);
            echo '        </td>' . "\n";
            echo '    </tr>' . "\n";
            if (true === $isSupported) {
                $Image = new \Rundiz\Image\Drivers\Gd($item['source_image_path']);
            }
            echo '    <tr>' . "\n";
            echo '        <td>Use <code>save()</code> method</td>' . "\n";
            echo '        <td>' . "\n";
            if (true === $isSupported) {
                $file_name = '../processed-images/' . autoImageFilename() . '-src-' . str_replace(' ', '-', strtolower($img_type_name)) . '_' . $resizeDim[0] . 'x' . $resizeDim[1];
                $Image->resizeNoRatio($resizeDim[0], $resizeDim[1]);
                $save_result = $Image->save($file_name . $file_ext);
                debugImage($file_name . $file_ext);
                if ($save_result != true) {
                    echo ' &nbsp; &nbsp; <span class="text-error">Error: ' . $Image->status_msg . '</span>' . "\n";
                }
                unset($file_name, $save_result);
                $Image->clear();
            } else {
                echo '<div class="text-error">Current version of PHP does not support this kind of image.</div>' . "\n";
            }// endif; image supported.
            echo '        </td>' . "\n";
            echo '    </tr>' . "\n";
            echo '    <tr>' . "\n";
            echo '        <td>Use <code>show()</code> method</td>' . "\n";
            $image_class_show_url = 'rdimage-gd-show-image.php?source_image_file=' . $item['source_image_path'] . '&amp;show_ext=' . $file_ext . '&amp;act=resizenoratio&amp;width=' . $resizeDim[0] . '&amp;height=' . $resizeDim[1];
            echo '        <td><a href="' . $image_class_show_url .'"><img src="' . $image_class_show_url . '" alt="" class="thumbnail"></a><br>';
            echo 'Extension: ' . $file_ext . '</td>' . "\n";
            echo '    </tr>' . "\n";

            echo '</tbody></table>' . "\n";
            unset($file_ext, $Image, $isSupported);
        }
        echo "\n\n";
    }// endforeach;
    unset($resizeDim);
    echo "\n\n";
}// displayTestResizeNotRatio
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
        displayTestResizeNotRatio($test_data_set);
        unset($test_data_set);
        ?>
        <hr>
        <?php
        include 'includes/include-page-footer.php';
        ?>
    </body>
</html>