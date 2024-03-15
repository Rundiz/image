<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\TraitsTest;


class ImageTraitTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    /**
     * @depends Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists
     */
    public function testGetImageFileData()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.jpg')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.gif')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.png')));
        $this->assertTrue(is_array($ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.webp')));
        $this->assertFalse($ImgAc->getImageFileData(static::$source_images_dir . 'file-not-exists' . date('YmdHis') . '.jpg'));
        $this->assertFalse($ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam-text.jpg'));

        // go in details.
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame(IMAGETYPE_JPEG, $result[2]);
        $this->assertSame(2, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.gif');
        $this->assertSame(IMAGETYPE_GIF, $result[2]);
        $this->assertSame(1, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.png');
        $this->assertSame(IMAGETYPE_PNG, $result[2]);
        $this->assertSame(3, $result[2]);
        $result = $ImgAc->getImageFileData(static::$source_images_dir . 'city-amsterdam.webp');
        $this->assertSame(IMAGETYPE_WEBP, $result[2]);
        $this->assertSame(18, $result[2]);
        $this->assertSame('image/webp', $result['mime']);
        $this->assertSame('.webp', $result['ext']);
        unset($ImgAc, $result);
    }// testGetImageFileData


}
