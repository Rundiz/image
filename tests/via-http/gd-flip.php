<?php
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageInterface.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/ImageAbstractClass.php';
require dirname(dirname(__DIR__)).'/Rundiz/Image/Drivers/Gd.php';

require __DIR__.DIRECTORY_SEPARATOR.'include-image-source.php';
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
        <h1>GD test flip</h1>
        <h2>JPG</h2>
        <?php
        echo '<a href="'.$source_image_jpg.'">source image</a><br>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_jpg);
        $rotate = 'hor';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.jpg';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'vrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.jpg';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'horvrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.jpg';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        unset($Image);
        // ------------------------------------------------------------------------------------------------------
        ?> 
        <h2>PNG</h2>
        <?php
        echo '<a href="'.$source_image_png.'">source image</a><br>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_png);
        $rotate = 'hor';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.png';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'vrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.png';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'horvrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.png';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        unset($Image);
        // ------------------------------------------------------------------------------------------------------
        ?> 
        <h2>GIF</h2>
        <?php
        echo '<a href="'.$source_image_gif.'">source image</a><br>'."\n";
        $Image = new \Rundiz\Image\Drivers\Gd($source_image_gif);
        $rotate = 'hor';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.gif';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'vrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.gif';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        $rotate = 'horvrt';
        $file_name = '../processed-images/rundiz-gd-image-flip-testpage-'.$rotate.'.gif';
        $Image->rotate($rotate);
        $Image->save($file_name);
        $Image->clear();
        echo '<a href="'.$file_name.'">'.$rotate.'</a> ';
        //
        unset($Image);
        // ------------------------------------------------------------------------------------------------------
        include __DIR__.DIRECTORY_SEPARATOR.'include-memory-usage.php';
        ?> 
    </body>
</html>