<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\PHP71;


/**
 * @group processing
 */
class ImageAbstractClassTest extends \Rundiz\Image\Tests\PHP71\CommonTestAbstractClass
{


    public function testBuildSourceImageData()
    {
        foreach (static::$source_images_set as $source_image) {
            $ImgAC = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . $source_image);
            $this->assertTrue($ImgAC->status);
            unset($ImgAC);
        }// endforeach;
    }// testBuildSourceImageData


    public function testCalculateImageSizeRatio()
    {
        // use landscape image.
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.jpg');
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 133);
        $result = $ImgAc->calculateImageSizeRatio(0, 0);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 67);
        // change master dimension
        $ImgAc->master_dim = 'width';
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 133);
        $ImgAc->master_dim = 'height';
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 300);
        $this->assertEquals($result['height'], 200);
        // new height, width is larger than original.
        $ImgAc->master_dim = 'auto';
        $result = $ImgAc->calculateImageSizeRatio(2000, 2000);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);
        $ImgAc->master_dim = 'width';
        $result = $ImgAc->calculateImageSizeRatio(2000, 2000);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);
        $ImgAc->master_dim = 'height';
        $result = $ImgAc->calculateImageSizeRatio(2000, 2000);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);
        unset($ImgAc, $result);

        // use new portrait image.
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'sample-portrait.jpg');
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 200);
        $result = $ImgAc->calculateImageSizeRatio(0, 0);
        $this->assertEquals($result['width'], 50);
        $this->assertEquals($result['height'], 100);
        // change master dimension
        $ImgAc->master_dim = 'width';
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 400);
        $ImgAc->master_dim = 'height';
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 200);
        unset($ImgAc, $result);

        // use new square image.
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'sample-square.jpg');
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 200);
        $result = $ImgAc->calculateImageSizeRatio(0, 0);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 100);
        // change master dimension
        $ImgAc->master_dim = 'width';
        $result = $ImgAc->calculateImageSizeRatio(200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 200);
        $ImgAc->master_dim = 'height';
        $result = $ImgAc->calculateImageSizeRatio(400, 400);
        $this->assertEquals($result['width'], 400);
        $this->assertEquals($result['height'], 400);
        $result = $ImgAc->calculateImageSizeRatio(2000, 2000);
        $this->assertEquals($result['width'], 800);
        $this->assertEquals($result['height'], 800);
        unset($ImgAc, $result);
    }// testCalculateImageSizeRatio


    public function testGetImageFileData()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.jpg');
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


    public function testGetImageSize()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertTrue(
            empty(\Rundiz\Image\Tests\PHPUnitFunctions\Arrays::array_diff_assoc_recursive(
                array(
                    'width' => 1920, 
                    'height' => 1281
                ), 
                $ImgAc->getImageSize()
            ))
        );
        $this->assertFalse(
            empty(\Rundiz\Image\Tests\PHPUnitFunctions\Arrays::array_diff_assoc_recursive(
                array(
                    'width' => 1921, 
                    'height' => 1281
                ), 
                $ImgAc->getImageSize()
            ))
        );
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'sample-portrait.jpg');
        $this->assertTrue(
            empty(\Rundiz\Image\Tests\PHPUnitFunctions\Arrays::array_diff_assoc_recursive(
                array(
                    'width' => 400, 
                    'height' => 800
                ), 
                $ImgAc->getImageSize()
            ))
        );
        unset($ImgAc);
    }// testGetImageSize


    public function testGetSourceImageOrientation()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame('L', $ImgAc->getSourceImageOrientation());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'sample-portrait.jpg');
        $this->assertSame('P', $ImgAc->getSourceImageOrientation());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'sample-square.jpg');
        $this->assertSame('S', $ImgAc->getSourceImageOrientation());
        unset($ImgAc);
    }// testGetSourceImageOrientation


    public function testIsAnimatedWebP()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.webp');
        $this->assertFalse($ImgAc->isAnimatedWebP(static::$source_images_dir . 'city-amsterdam.webp'));
        $this->assertTrue($ImgAc->isAnimatedWebP(static::$source_images_dir . 'city-amsterdam-animated.webp'));
        unset($ImgAc);
    }// testIsAnimatedWebP


    public function testIsClassSetup()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertTrue($ImgAc->isClassSetup());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'file-not-exists' . date('YmdHis') . '.jpg');
        $this->assertFalse($ImgAc->isClassSetup());
        unset($ImgAc);
    }// testIsClassSetup


}
