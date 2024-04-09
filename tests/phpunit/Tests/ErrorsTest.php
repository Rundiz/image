<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\Tests;


class ErrorsTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    /**
     * @depends Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists
     */
    public function testSourceErrors()
    {
        // normal image, shoud have no errors.
        $Image = new \Rundiz\Image\Drivers\Gd(self::$source_images_dir . 'source-image.jpg');
        $this->assertTrue($Image->status);

        // now errors.
        $Image = new \Rundiz\Image\Drivers\Gd(self::$source_images_dir . 'source-image-text.jpg');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_SRC_NOTIMAGE, $Image->statusCode);
        $Image = new \Rundiz\Image\Drivers\Gd(self::$source_images_dir . 'image-not-exists' . date('YmdHis') . '.jpg');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_SRC_NOTEXISTS, $Image->statusCode);
    }// testSourceErrors


    public function testWatermarkErrors()
    {
        $Image = new \Rundiz\Image\Drivers\Gd(self::$source_images_dir . 'source-image.jpg');
        $Image->watermarkImage(self::$source_images_dir . 'wm-not-exists' . date('YmdHis') . '.png');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_WMI_NOTEXISTS, $Image->statusCode);
        $Image->clear();

        $Image->watermarkImage(self::$source_images_dir . 'font.ttf');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_WMI_UNKNOWIMG, $Image->statusCode);
        $Image->clear();

        $Image->watermarkText('Hello', self::$source_images_dir . 'font-not-exists' . date('YmdHis') . '.ttf');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_WMT_FONT_NOTEXISTS, $Image->statusCode);
        $Image->clear();
    }// testWatermarkErrors


    public function testSaveShowErrors()
    {
        $Image = new \Rundiz\Image\Drivers\Gd(self::$source_images_dir . 'source-image.jpg');
        $Image->save(self::$processed_images_dir . 'processed-image.jpgxxx');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_SAVE_UNSUPPORT, $Image->statusCode);
        $Image->clear();

        $Image->show('pngxxx');
        $this->assertFalse($Image->status);
        $this->assertSame(\Rundiz\Image\Drivers\Gd::RDIERROR_SHOW_UNSUPPORT, $Image->statusCode);
        $Image->clear();
    }// testSaveShowErrors


}
