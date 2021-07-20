<?php


namespace Rundiz\Image\Tests\PHP72;

abstract class CommonTestAbstractClass extends \PHPUnit\Framework\TestCase
{


    protected static $source_images_dir;
    protected static $source_images_set = array('city-amsterdam.gif', 'city-amsterdam.jpg', 'city-amsterdam.png', 'city-amsterdam-animated.gif');
    protected static $source_watermark_images_set = array('watermark.gif', 'watermark.jpg', 'watermark.png');
    protected static $source_watermark_fonts_set = array('cschatthai.ttf');
    protected static $watermark_text = 'Rundiz watermark สั้น ญู ให้ ทดสอบสระ.';
    protected static $processed_images_dir;
    protected static $processed_extensions = array('gif', 'jpg', 'png');


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


    public static function setUpBeforeClass(): void
    {
        self::$source_images_dir = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'source-images'.DIRECTORY_SEPARATOR;
        self::$processed_images_dir = dirname(dirname(__DIR__)).DIRECTORY_SEPARATOR.'processed-images'.DIRECTORY_SEPARATOR;

        $processed_images_folder = self::$processed_images_dir;
        self::rrmdir($processed_images_folder, $processed_images_folder);
        unset($processed_images_folder);
    }// setUpBeforeClass


    public static function tearDownAfterClass(): void
    {
        $processed_images_folder = self::$processed_images_dir;
        self::rrmdir($processed_images_folder, $processed_images_folder);
        unset($processed_images_folder);
    }// tearDownAfterClass


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

            if (strpos($file_name, '-animated') !== false) {
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
     * gif = 1, jpg = 2, png = 3
     * 
     * @param string $extension
     * @return integer
     */
    protected function getProcessedExtensionTypeNumber($extension)
    {
        if (is_array(self::$processed_extensions) && in_array($extension, self::$processed_extensions)) {
            return (array_search($extension, self::$processed_extensions) + 1);
        }

        return false;
    }// getProcessedExtensionTypeNumber


    /**
     * @requires PHP 5.3
     */
    public function testRequireFilesExistsAndFolderWritable()
    {
        if (is_array(self::$source_images_set)) {
            foreach (self::$source_images_set as $source_image) {
                $this->assertFileExists(self::$source_images_dir.$source_image);
            }
        }
        if (is_array(self::$source_watermark_images_set)) {
            foreach (self::$source_watermark_images_set as $source_image) {
                $this->assertFileExists(self::$source_images_dir.$source_image);
            }
        }
        if (is_array(self::$source_watermark_fonts_set)) {
            foreach (self::$source_watermark_fonts_set as $source_image) {
                $this->assertFileExists(self::$source_images_dir.$source_image);
            }
        }
        $this->assertTrue(is_dir(self::$processed_images_dir) && is_writable(self::$processed_images_dir), 'The "processed-images" folder is not exists or is not writable.');
    }// testRequireFilesExistsAndFolderWritable


}
