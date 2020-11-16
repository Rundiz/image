<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Imagick.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';


$imageDriverText = 'imagick';


function displayTests($imageSource, $driver = 'gd')
{
    global $imageDriverText;
    $saveAsExts = array('gif', 'jpg', 'png');
    $transparency = (stripos($imageSource, 'non-transparent') !== false ? 'nontransparentgif' : 'transparentgif');

    echo 'Source image: <a href="'.$imageSource.'"><img src="'.$imageSource.'" alt="" class="thumbnail"></a><br>'."\n";
    echo '<h3>Resize</h3>'.PHP_EOL;
    if ($driver === 'gd') {
        $Image = new \Rundiz\Image\Drivers\Gd($imageSource);
    } else {
        $Image = new \Rundiz\Image\Drivers\Imagick($imageSource);
    }
    foreach ($saveAsExts as $ext) {
        $Image->resize(600, 600);
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-resize-600x600';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
    echo '<br>Use show() method as ';
    foreach ($saveAsExts as $ext) {
        $image_class_show_url = $imageDriverText . '-show-image.php?source_image_file='.$imageSource.'&amp;show_ext='.$ext.'&amp;act=resize&amp;width=600&amp;height=600';
        echo ' <a href="'.$image_class_show_url.'">' . $ext . '</a><img src="'.$image_class_show_url.'" alt="" class="thumbnail">'."\n";
    }
    unset($ext, $image_class_show_url);

    echo '<h3>Rotate</h3>' . PHP_EOL;
    foreach ($saveAsExts as $ext) {
        $Image->rotate(90);
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-rotate-90';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
    echo '<br>Use show() method as ';
    foreach ($saveAsExts as $ext) {
        $image_class_show_url = $imageDriverText . '-show-image.php?source_image_file='.$imageSource.'&amp;show_ext='.$ext.'&amp;act=rotate&amp;degree=90';
        echo ' <a href="'.$image_class_show_url.'">' . $ext . '</a><img src="'.$image_class_show_url.'" alt="" class="thumbnail">'."\n";
    }
    unset($ext, $image_class_show_url);

    echo '<h3>Flip</h3>' . PHP_EOL;
    foreach ($saveAsExts as $ext) {
        $Image->rotate('hor');
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-rotate-hor';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
    echo '<br>Use show() method as ';
    foreach ($saveAsExts as $ext) {
        $image_class_show_url = $imageDriverText . '-show-image.php?source_image_file='.$imageSource.'&amp;show_ext='.$ext.'&amp;act=rotate&amp;degree=hor';
        echo ' <a href="'.$image_class_show_url.'">' . $ext . '</a><img src="'.$image_class_show_url.'" alt="" class="thumbnail">'."\n";
    }
    unset($ext, $image_class_show_url);

    echo '<h3>Crop</h3>' . PHP_EOL;
    foreach ($saveAsExts as $ext) {
        $Image->crop(300, 300);
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-crop-300x300';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
    echo '<br>Use show() method as ';
    foreach ($saveAsExts as $ext) {
        $image_class_show_url = $imageDriverText . '-show-image.php?source_image_file='.$imageSource.'&amp;show_ext='.$ext.'&amp;act=crop&amp;width=300&amp;height=300';
        echo ' <a href="'.$image_class_show_url.'">' . $ext . '</a><img src="'.$image_class_show_url.'" alt="" class="thumbnail">'."\n";
    }
    unset($ext, $image_class_show_url);

    displayWmTests($imageSource, $driver);
}// displayTests


function displayWmTests($imageSource, $driver = 'gd')
{
    global $imageDriverText;
    $saveAsExts = array('gif', 'jpg', 'png');
    $transparency = (stripos($imageSource, 'non-transparent') !== false ? 'nontransparentgif' : 'transparentgif');
    $source_font = '../source-images/cschatthai.ttf';
    if ($driver === 'gd') {
        $Image = new \Rundiz\Image\Drivers\Gd($imageSource);
    } else {
        $Image = new \Rundiz\Image\Drivers\Imagick($imageSource);
    }

    echo '<h3>Watermark text</h3>' . PHP_EOL;
    foreach ($saveAsExts as $ext) {
        $Image->watermarkText('Rundiz watermark สั้น ญู ให้ ทดสอบสระ.', $source_font, 10, 10, 20);
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-wmtext';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
    echo '<br>Use show() method as ';
    foreach ($saveAsExts as $ext) {
        $image_class_show_url = $imageDriverText . '-show-image.php?source_image_file='.$imageSource.'&amp;show_ext='.$ext.'&amp;act=watermarktext&amp;startx=10&amp;starty=10&amp;fontsize=20';
        echo ' <a href="'.$image_class_show_url.'">' . $ext . '</a><img src="'.$image_class_show_url.'" alt="" class="thumbnail">'."\n";
    }
    unset($ext, $image_class_show_url);

    echo '<h3>Watermark image</h3>' . PHP_EOL;
    foreach ($saveAsExts as $ext) {
        $Image->watermarkImage('../source-images/watermark.png', 10, 10);
        $file_name = '../processed-images/rundiz-' . $imageDriverText . '-' . $transparency . '-wmimage';
        $save_result = $Image->save($file_name . '.' . $ext);
        if ($save_result === true) {
            echo ' <a href="' . $file_name . '.' . $ext . '">' . $ext . '</a><img src="'.$file_name.'.'.$ext.'" alt="" class="thumbnail">'."\n";
            $img_data = getimagesize($file_name . '.' . $ext);
            if (is_array($img_data) && array_key_exists('mime', $img_data)) {
                echo '(' . $img_data['mime'] . ')'."\n";
            }
        } else {
            echo '<br>Error: '.$Image->status_msg."\n";
        }
        $Image->clear();
    }// endforeach;
    unset($ext);
}// displayWmTests
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>Imagick test transparent and non transparent gif</h1>
        <h2>Transparent gif</h2>
        <?php displayTests($source_image_gif, 'imagick'); ?> 
        <h2>Non-Transparent gif</h2>
        <?php displayTests('../source-images/city-amsterdam-non-transparent.gif', 'imagick'); ?> 
        <?php include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php'; ?> 
    </body>
</html>