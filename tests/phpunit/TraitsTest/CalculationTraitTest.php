<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\TraitsTest;


class CalculationTraitTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testCalculateCounterClockwise()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame(270, $ImgAc->calculateCounterClockwise(-1));// less than 0, set to 90 and calculate
        $this->assertSame(270, $ImgAc->calculateCounterClockwise(361));// more than 360, set to 90 and calculate
        $this->assertSame(0, $ImgAc->calculateCounterClockwise(0));
        $this->assertSame(270, $ImgAc->calculateCounterClockwise(90));
        $this->assertSame(180, $ImgAc->calculateCounterClockwise(180));
        $this->assertSame(90, $ImgAc->calculateCounterClockwise(270));
        $this->assertSame(0, $ImgAc->calculateCounterClockwise(360));
        $this->assertSame(359, $ImgAc->calculateCounterClockwise(1));// non 90 degrees
        $this->assertSame(358, $ImgAc->calculateCounterClockwise(2));// non 90 degrees
        $this->assertSame(357, $ImgAc->calculateCounterClockwise(3));// non 90 degrees
        $this->assertSame(355, $ImgAc->calculateCounterClockwise(5));// non 90 degrees
        $this->assertSame(354, $ImgAc->calculateCounterClockwise(6));// non 90 degrees
        $this->assertSame(350, $ImgAc->calculateCounterClockwise(10));// non 90 degrees
        $this->assertSame(315, $ImgAc->calculateCounterClockwise(45));// non 90 degrees
        $this->assertSame(237, $ImgAc->calculateCounterClockwise(123));// non 90 degrees
        unset($ImgAc);
    }// testCalculateCounterClockwise


    public function testCalculateImageSizeRatio()
    {
        // use landscape image.
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
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
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'sample-portrait.jpg');
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
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'sample-square.jpg');
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
    
    
    public function testCalculateStartXOfCenter()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame(150, $ImgAc->calculateStartXOfCenter(200, 500));
        $this->assertSame(150, $ImgAc->calculateStartXOfCenter(200.10, 500.50));
        $this->assertSame(151, $ImgAc->calculateStartXOfCenter(202.91, 503.91));
        $this->assertSame(550, $ImgAc->calculateStartXOfCenter(400, 1500));
        unset($ImgAc);
    }// testCalculateStartXOfCenter


    public function testCalculateWatermarkImageStartXY()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame([10, 10], $ImgAc->calculateWatermarkImageStartXY('left', 'top', 800, 600, 200, 100));
        $ImgAc->setSourceImageWidth(800);
        $ImgAc->setSourceImageHeight(600);
        $this->assertSame([10, 490], $ImgAc->calculateWatermarkImageStartXY('left', 'bottom', 800, 600, 200, 100));// (600-100)-10 that is padding = 490
        $ImgAc->setSourceImageWidth(800);
        $ImgAc->setSourceImageHeight(600);
        $this->assertSame([590, 490], $ImgAc->calculateWatermarkImageStartXY('right', 'bottom', 800, 600, 200, 100));
        $ImgAc->setSourceImageWidth(800);
        $ImgAc->setSourceImageHeight(600);
        $this->assertSame([590, 10], $ImgAc->calculateWatermarkImageStartXY('right', 'top', 800, 600, 200, 100));
        $ImgAc->setSourceImageWidth(800);
        $ImgAc->setSourceImageHeight(600);
        $this->assertSame([300, 250], $ImgAc->calculateWatermarkImageStartXY('center', 'middle', 800, 600, 200, 100));
        unset($ImgAc);
    }// testCalculateWatermarkImageStartXY


    public function testConvertAlpha127ToRgba()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame('1.00', $ImgAc->convertAlpha127ToRgba(-1));// less than 0, set to 0 and calculate
        $this->assertSame('1.00', $ImgAc->convertAlpha127ToRgba(0));
        $this->assertSame('0.69', $ImgAc->convertAlpha127ToRgba(40));
        $this->assertSame('0.29', $ImgAc->convertAlpha127ToRgba(90));
        $this->assertSame('0.00', $ImgAc->convertAlpha127ToRgba(127));
        $this->assertSame('0.00', $ImgAc->convertAlpha127ToRgba(128));// over max, reduced to 127 and calculate
        unset($ImgAc);
    }// testConvertAlpha127ToRgba


}
