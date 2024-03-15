<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\DependentTests;


class DirsFilesExistsTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testImageExists()
    {
        // use the code below to generate files list.
        /*$DI = new \DirectoryIterator(self::$source_images_dir);
        foreach ($DI as $File) {
            if ($File->isDot()) {
                continue;
            }
            echo "'";
            echo $File->getFilename();
            echo "'";
            echo ', ';
            echo "\n";
        }
        exit;*/
        // end code to generate files list.

        // generated files while they are all correctly created.
        $files = [
            'city-amsterdam-animated.gif',
            'city-amsterdam-animated.webp',
            'city-amsterdam-jpg.png',
            'city-amsterdam-non-transparent.gif',
            'city-amsterdam-non-transparent.png',
            'city-amsterdam-non-transparent.webp',
            'city-amsterdam-text.jpg',
            'city-amsterdam.gif',
            'city-amsterdam.jpg',
            'city-amsterdam.png',
            'city-amsterdam.webp',
            'cschatthai.ttf',
            'font.ttf',
            'sample-portrait.jpg',
            'sample-square.jpg',
            'transparent-lossless.webp',
            'watermark.gif',
            'watermark.jpg',
            'watermark.png',
        ];

        foreach ($files as $file) {
            $this->assertTrue(is_file(static::$source_images_dir . DIRECTORY_SEPARATOR . $file));
        }
    }// testImageExists


    public function testProcessedDirExists()
    {
        $this->assertTrue(is_dir(static::$processed_images_dir) && is_writable(static::$processed_images_dir), 'The "processed-images" folder is not exists or is not writable.');
    }// testProcessedDirExists


}
