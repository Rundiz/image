<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\TraitsTest;


class ImageTraitTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testGetImageFileData()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'source-image.jpg');
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'source-image.jpg')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'source-image.gif')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'source-image.png')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'source-image.webp')));
        $this->assertFalse($ImgAc->getImageFileData(static::$source_images_dir . 'file-not-exists' . date('YmdHis') . '.jpg'));
        $this->assertFalse($ImgAc->getImageFileData(static::$source_images_dir . 'source-image-text.jpg'));

        // go in details.
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'source-image.jpg');
        $this->assertSame(IMAGETYPE_JPEG, $result[2]);
        $this->assertSame(2, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'source-image.gif');
        $this->assertSame(IMAGETYPE_GIF, $result[2]);
        $this->assertSame(1, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'source-image.png');
        $this->assertSame(IMAGETYPE_PNG, $result[2]);
        $this->assertSame(3, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'source-image.webp');
        $this->assertSame(IMAGETYPE_WEBP, $result[2]);
        $this->assertSame(18, $result[2]);
        $this->assertSame('image/webp', $result['mime']);
        $this->assertSame('.webp', $result['ext']);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'source-image.avif');
        $this->assertSame(IMAGETYPE_AVIF, $result[2]);
        $this->assertSame(19, $result[2]);
        $this->assertSame('image/avif', $result['mime']);
        $this->assertSame('.avif', $result['ext']);
        $this->assertTrue($result[0] > 0);
        $this->assertTrue($result[1] > 0);
        unset($ImgAc, $result);
    }// testGetImageFileData


}
