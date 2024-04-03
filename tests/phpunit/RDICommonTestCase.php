<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests;


abstract class RDICommonTestCase extends \Yoast\PHPUnitPolyfills\TestCases\TestCase
{


    /**
     * @var string Source images folder path. This path end with directory separator.
     */
    protected static $source_images_dir;


    /**
     * @var array The list of source images that will be use for tests.
     */
    protected static $source_images_set = ['city-amsterdam.gif', 'city-amsterdam.jpg', 'city-amsterdam.png', 'city-amsterdam.webp', 'city-amsterdam-animated.gif'];


    protected static $source_watermark_images_set = ['watermark.gif', 'watermark.jpg', 'watermark.png', 'watermark.webp'];
    protected static $source_watermark_fonts_set = ['font.ttf'];
    protected static $watermark_text = 'Rundiz watermark สั้น ญู ให้ ทดสอบสระ.';

    /**
     * @var string Processed images folder path. This path end with directory separator.
     */
    protected static $processed_images_dir;

    protected static $processed_extensions = [
        IMAGETYPE_GIF => 'gif', 
        IMAGETYPE_JPEG => 'jpg', 
        IMAGETYPE_PNG => 'png', 
        IMAGETYPE_WEBP => 'webp'
    ];


    /**
     * @link https://github.com/Yoast/PHPUnit-Polyfills
     */
    public static function set_up_before_class() {
        parent::set_up_before_class();

        // other fixture which needs to be available.

        self::$source_images_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'source-images' . DIRECTORY_SEPARATOR;
        self::$processed_images_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'processed-images' . DIRECTORY_SEPARATOR;

        $processed_images_folder = self::$processed_images_dir;
        self::rrmdir($processed_images_folder, $processed_images_folder);
        unset($processed_images_folder);
    }// set_up_before_class


    /**
     * @link https://github.com/Yoast/PHPUnit-Polyfills
     */
    public static function tear_down_after_class() {
        // other clean up related to `set_up_before_class()`

        $processed_images_folder = self::$processed_images_dir;
        self::rrmdir($processed_images_folder, $processed_images_folder);
        unset($processed_images_folder);

        parent::tear_down_after_class();
    }// tear_down_after_class


    /**
     * Get extension from file name.
     * 
     * @param string $file_name
     * @return string
     */
    protected function getExtensionFromName($file_name)
    {
        if (strpos($file_name, '.') !== false) {
            $file_name_exp = explode('.', $file_name);

            if (stripos($file_name, '-animated') !== false) {
                $output = 'animated';
            } else {
                $output = '';
            }
            $output .= $file_name_exp[count($file_name_exp)-1];

            unset($file_name_exp);
            return $output;
        }

        return $file_name;
    }// getExtensionFromName


    /**
     * Get processed extension type number.<br>
     * gif = 1, jpg = 2, png = 3, webp = 18
     * 
     * @param string $extension
     * @return integer
     */
    protected function getProcessedExtensionTypeNumber($extension)
    {
        if (is_array(self::$processed_extensions) && in_array($extension, self::$processed_extensions)) {
            return array_search($extension, self::$processed_extensions);
        }

        return false;
    }// getProcessedExtensionTypeNumber


    /**
     * Recursively remove files and folders
     * @param string $dir
     * @param string $limited_dir
     */
    protected static function rrmdir($dir, $limited_dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..' && $object != '.gitkeep' && $object != '.gitignore') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                        rrmdir($dir . DIRECTORY_SEPARATOR . $object, $limited_dir);
                    } else {
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                    }
                }
            }

            if ($dir != $limited_dir) {
                rmdir($dir);
            }
        }
    }// rrmdir


}
