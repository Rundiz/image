<?php


function rrmdir($dir, $limited_dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..' && $object != '.gitkeep' && $object != '.gitignore') {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object, $limited_dir);
                } else {
                    echo $dir . DIRECTORY_SEPARATOR . $object . "<br>\n";
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }

        if ($dir != $limited_dir) {
            echo $dir . "<br>\n";
            rmdir($dir);
        }
    }
}


$processed_images_folder = dirname(__DIR__).DIRECTORY_SEPARATOR.'processed-images';
rrmdir($processed_images_folder, $processed_images_folder);
unset($processed_images_folder);
echo 'Deleted, <a href="./">go back</a>.';
include 'includes/include-page-footer.php';