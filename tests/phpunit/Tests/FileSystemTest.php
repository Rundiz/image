<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\Tests;


class FileSystemTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testGetFileExtension()
    {
        $FileSystem = new \Rundiz\Image\FileSystem();
        $this->assertSame('jpg', $FileSystem->getFileExtension('image.jpeg'));
        $this->assertSame('jpg', $FileSystem->getFileExtension('image.JPEG'));// replaced
        $this->assertSame('JPG', $FileSystem->getFileExtension('image.JPG'));// still same case
        $this->assertSame('jpg', $FileSystem->getFileExtension('image.jpg'));
        $this->assertSame('gif', $FileSystem->getFileExtension('image.gif'));
        $this->assertSame('png', $FileSystem->getFileExtension('image.png'));
        $this->assertSame('webp', $FileSystem->getFileExtension('image.webp'));
    }// testGetFileExtension


    public function testGetFileRealpath()
    {
        $FileSystem = new \Rundiz\Image\FileSystem();
        $sourceDir = str_replace(['/', '\\', DIRECTORY_SEPARATOR], '/', static::$source_images_dir);
        $sourceDirRealDirSep = str_replace(['/', '\\', DIRECTORY_SEPARATOR], DIRECTORY_SEPARATOR, static::$source_images_dir);

        $this->assertSame($sourceDirRealDirSep . 'image.jpg', $FileSystem->getFileRealpath($sourceDir . 'image.jpg'));
        $this->assertSame($sourceDirRealDirSep . 'image.jpg', $FileSystem->getFileRealpath($sourceDir . '/image.jpg'));
        $this->assertSame($sourceDirRealDirSep . 'image.png', $FileSystem->getFileRealpath($sourceDir . '/image.png'));
        $this->assertSame($sourceDirRealDirSep . 'image.webp', $FileSystem->getFileRealpath($sourceDir . '/image.webp'));
        $this->assertSame(DIRECTORY_SEPARATOR . 'image.jpg', $FileSystem->getFileRealpath('not-exists/image.jpg'));
    }// testGetFileRealpath


}
