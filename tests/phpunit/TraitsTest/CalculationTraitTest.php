<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\TraitsTest;


class CalculationTraitTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testCalculateCounterClockwise()
    {
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame(270, $Calculation->calculateCounterClockwise(-1));// less than 0, set to 90 and calculate
        $this->assertSame(270, $Calculation->calculateCounterClockwise(361));// more than 360, set to 90 and calculate
        $this->assertSame(0, $Calculation->calculateCounterClockwise(0));
        $this->assertSame(270, $Calculation->calculateCounterClockwise(90));
        $this->assertSame(180, $Calculation->calculateCounterClockwise(180));
        $this->assertSame(90, $Calculation->calculateCounterClockwise(270));
        $this->assertSame(0, $Calculation->calculateCounterClockwise(360));
        $this->assertSame(359, $Calculation->calculateCounterClockwise(1));// non 90 degrees
        $this->assertSame(358, $Calculation->calculateCounterClockwise(2));// non 90 degrees
        $this->assertSame(357, $Calculation->calculateCounterClockwise(3));// non 90 degrees
        $this->assertSame(355, $Calculation->calculateCounterClockwise(5));// non 90 degrees
        $this->assertSame(354, $Calculation->calculateCounterClockwise(6));// non 90 degrees
        $this->assertSame(350, $Calculation->calculateCounterClockwise(10));// non 90 degrees
        $this->assertSame(315, $Calculation->calculateCounterClockwise(45));// non 90 degrees
        $this->assertSame(237, $Calculation->calculateCounterClockwise(123));// non 90 degrees
        unset($Calculation);
    }// testCalculateCounterClockwise
    
    
    public function testCalculateStartXOfCenter()
    {
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame(150, $Calculation->calculateStartXOfCenter(200, 500));
        $this->assertSame(150, $Calculation->calculateStartXOfCenter(200.10, 500.50));
        $this->assertSame(151, $Calculation->calculateStartXOfCenter(202.91, 503.91));
        $this->assertSame(550, $Calculation->calculateStartXOfCenter(400, 1500));
        unset($Calculation);
    }// testCalculateStartXOfCenter


    public function testCalculateWatermarkImageStartXY()
    {
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame([10, 10], $Calculation->calculateWatermarkImageStartXY('left', 'top', 800, 600, 200, 100));
        $this->assertSame([10, 490], $Calculation->calculateWatermarkImageStartXY('left', 'bottom', 800, 600, 200, 100));// (600-100)-10 that is padding = 490
        $this->assertSame([590, 490], $Calculation->calculateWatermarkImageStartXY('right', 'bottom', 800, 600, 200, 100));// (800-(200+10 that is padding)) = 590
        $this->assertSame([590, 10], $Calculation->calculateWatermarkImageStartXY('right', 'top', 800, 600, 200, 100));
        $this->assertSame([300, 250], $Calculation->calculateWatermarkImageStartXY('center', 'middle', 800, 600, 200, 100));
        unset($Calculation);

        // test resizeed with a real image and then calculate again.
        $sourceImage = static::$source_images_dir . 'source-image.jpg';
        $Image = new \Rundiz\Image\Drivers\Gd($sourceImage);
        list($origWidth, $origHeight) = getimagesize($sourceImage);
        $imgSizes = $Image->getImageSize();
        // make sure that original image size is the same as retrieved via `getImageSize()` method.
        $this->assertSame($origWidth, $imgSizes['width']);
        $this->assertSame($origHeight, $imgSizes['height']);
        unset($imgSizes, $origHeight, $origWidth);
        $Image->resize(600, 500);
        // make sure that property's value had changed.
        $this->assertSame(600, $Image->last_modified_image_width);
        $this->assertSame(400, $Image->last_modified_image_height);
        // now, the same calculate with test above should now changed the value from 490 to 290. New height is now 400, half of watermark width is (200/2) = 100, padding is 10 by default.
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame([10, 290], $Calculation->calculateWatermarkImageStartXY('left', 'bottom', $Image->last_modified_image_width, $Image->last_modified_image_height, 200, 100));// (400-100)-10 = 290
        // New width is now 600, watermark width is 200, padding is 10 by default.
        $this->assertSame([390, 290], $Calculation->calculateWatermarkImageStartXY('right', 'bottom', $Image->last_modified_image_width, $Image->last_modified_image_height, 200, 100));// (600-(200+10)) = 390

        // resize again without any reset or clear (smaller).
        $Image->resize(200, 100);
        $this->assertSame(200, $Image->last_modified_image_width);
        $this->assertSame(133, $Image->last_modified_image_height);
        $this->assertSame([10, 23], $Calculation->calculateWatermarkImageStartXY('left', 'bottom', $Image->last_modified_image_width, $Image->last_modified_image_height, 200, 100));// (133-100)-10 = 23
        $this->assertSame([-10, 23], $Calculation->calculateWatermarkImageStartXY('right', 'bottom', $Image->last_modified_image_width, $Image->last_modified_image_height, 200, 100));// (200-(200+10)) = -10
        unset($Calculation, $Image, $sourceImage);
    }// testCalculateWatermarkImageStartXY


    public function testConvertAlpha127ToRgba()
    {
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame('1.00', $Calculation->convertAlpha127ToRgba(-1));// less than 0, set to 0 and calculate
        $this->assertSame('1.00', $Calculation->convertAlpha127ToRgba(0));
        $this->assertSame('0.69', $Calculation->convertAlpha127ToRgba(40));
        $this->assertSame('0.29', $Calculation->convertAlpha127ToRgba(90));
        $this->assertSame('0.00', $Calculation->convertAlpha127ToRgba(127));
        $this->assertSame('0.00', $Calculation->convertAlpha127ToRgba(128));// over max, reduced to 127 and calculate
        unset($Calculation);
    }// testConvertAlpha127ToRgba


}
