<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';

$rotate = 90;
$resize_w = 700;
$resize_h = 467;
$crop_width = 460;
$crop_height = 460;

$base_save_file_name = '../processed-images/rundiz-gd-image-resize-'.$resize_w.'x'.$resize_h.'-rotate-'.$rotate.'-crop-'.$crop_width.'x'.$crop_height;
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Test Image manipulation class.</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>GD test multi process <small>resize <?php echo $resize_w.'x'.$resize_h; ?> &gt; rotate <?php echo $rotate; ?>&deg; &gt; crop <?php echo $crop_width.'x'.$crop_height; ?></small></h1>
        <h2>JPG</h2>
        <?php
        echo '<a href="'.$source_image_jpg.'">source image</a><img src="'.$source_image_jpg.'" alt="" class="thumbnail"><br>'."\n";
        echo 'Save as: '."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_jpg);
        $file_ext = 'jpg';
        $file_name = $base_save_file_name.'-sourcejpg.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'png';
        $file_name = $base_save_file_name.'-sourcejpg.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'gif';
        $file_name = $base_save_file_name.'-sourcejpg.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'jpg';
        echo '<br>';
        $file_name = '../processed-images/rundiz-gd-image-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-center,middle-rotate-'.$rotate.'-sourcejpg.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height, 'center', 'middle');
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (resize &gt; crop &gt; rotate)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-crop-'.$crop_width.'x'.$crop_height.'-resize-'.$resize_w.'x'.$resize_h.'-rotate-'.$rotate.'-sourcejpg.'.$file_ext;
        $Image->crop($crop_width, $crop_height);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; resize &gt; rotate)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-crop-'.$crop_width.'x'.$crop_height.'-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-sourcejpg.'.$file_ext;
        $Image->crop($crop_width, $crop_height);
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; rotate &gt; resize)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-rotate-'.$rotate.'-crop-'.$crop_width.'x'.$crop_height.'-resize-'.$resize_w.'x'.$resize_h.'-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; crop &gt; resize)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image-crop-900x900-crop-600x600-crop-'.$crop_width.'x'.$crop_height.'-crop-400x400-sourcejpg.'.$file_ext;
        $Image->crop(900, 900);
        $Image->crop(600, 600);
        $Image->crop($crop_width, $crop_height);
        $Image->crop(400, 400);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (crop &gt; crop &gt; crop &gt; crop)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-watermarkimage-right,bottom-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->watermarkImage('../source-images/watermark.png', 'right', 'bottom');
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop &gt; watermark image)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        $file_name = '../processed-images/rundiz-gd-image'.'-rotate-'.$rotate.'-resize-'.$resize_w.'x'.$resize_h.'-crop-'.$crop_width.'x'.$crop_height.'-watermarktext-right,bottom-sourcejpg.'.$file_ext;
        $Image->rotate($rotate);
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->crop($crop_width, $crop_height);
        $Image->watermarkText('Rundiz Image สั้น ญู ให้ ทดสอบสระ.', '../source-images/cschatthai.ttf', 'right', 'bottom', 13);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.' (rotate &gt; resize &gt; crop &gt; watermark text)</a><img src="'.$file_name.'" alt="" class="thumbnail"><br> ';
        unset($file_name, $Image);

        // -------------------------------------------------------------------------------------------------------------------
        ?> 
        <h2>PNG</h2>
        <?php
        echo '<a href="'.$source_image_png.'">source image</a><img src="'.$source_image_png.'" alt="" class="thumbnail"><br>'."\n";
        echo 'Save as: '."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_png);
        $file_ext = 'jpg';
        $file_name = $base_save_file_name.'-sourcepng.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'png';
        $file_name = $base_save_file_name.'-sourcepng.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'gif';
        $file_name = $base_save_file_name.'-sourcepng.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        unset($file_name, $Image);

        // -------------------------------------------------------------------------------------------------------------------
        ?> 
        <h2>GIF</h2>
        <?php
        echo '<a href="'.$source_image_gif.'">source image</a><img src="'.$source_image_gif.'" alt="" class="thumbnail"><br>'."\n";
        echo 'Save as: '."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_gif);
        $file_ext = 'jpg';
        $file_name = $base_save_file_name.'-sourcegif.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'png';
        $file_name = $base_save_file_name.'-sourcegif.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        $file_ext = 'gif';
        $file_name = $base_save_file_name.'-sourcegif.'.$file_ext;
        $Image->resizeNoRatio($resize_w, $resize_h);
        $Image->rotate($rotate);
        $Image->crop($crop_width, $crop_height);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$file_ext.'</a><img src="'.$file_name.'" alt="" class="thumbnail"> ';
        unset($file_name, $Image);

        // -------------------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>