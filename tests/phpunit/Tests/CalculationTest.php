<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\Tests;


/**
 * Test on `Calculation` class.
 * 
 * Most methods that `Calculation` class uses are already have tests in `CalculationTraitTest` class.
 */
class CalculationTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testCalculateCounterClockwise()
    {
        // this test call `Calculation` class directly, not extended one in tests folder.
        $Calculation = new \Rundiz\Image\Calculation();
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


    public function testCalculateNewDimensionByRatio()
    {
        $Calculation = new \Rundiz\Image\Calculation();

        // landscape image dimension tests. ----------------------------------------------------
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 133);

        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 0, 0);
        $this->assertEquals($result['width'], 1);
        $this->assertEquals($result['height'], 1);

        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 100, 100);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 67);

        // change master dimension -------------------------------
        $options = ['master_dim' => 'width'];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 200, 200, $options);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 133);

        $options = ['master_dim' => 'height'];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 200, 200, $options);
        $this->assertEquals($result['width'], 300);
        $this->assertEquals($result['height'], 200);
        // end change master dimension tests. --------------------

        // new height, width is larger than original. ---------------
        $options = ['master_dim' => 'auto'];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);

        $options = [
            'master_dim' => 'auto',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 2000);
        $this->assertEquals($result['height'], 1334);

        $options = ['master_dim' => 'width'];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);

        $options = [
            'master_dim' => 'width',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 2000);
        $this->assertEquals($result['height'], 1334);

        $options = ['master_dim' => 'height'];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 1920);
        $this->assertEquals($result['height'], 1281);

        $options = [
            'master_dim' => 'height',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(1920, 1281, 2000, 2000, $options);
        $this->assertEquals($result['width'], 2998);
        $this->assertEquals($result['height'], 2000);
        // end new height, width is larger than original. ----------
        // end landscape image dimension tests. -----------------------------------------------

        // portrait image dimension tests. -------------------------------------------------------
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 200, 200);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 200);

        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 0, 0);
        $this->assertEquals($result['width'], 1);
        $this->assertEquals($result['height'], 1);

        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 100, 100);
        $this->assertEquals($result['width'], 50);
        $this->assertEquals($result['height'], 100);

        // change master dimension -------------------------------
        $options = ['master_dim' => 'width'];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 200, 200, $options);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 400);

        $options = ['master_dim' => 'height'];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 200, 200, $options);
        $this->assertEquals($result['width'], 100);
        $this->assertEquals($result['height'], 200);
        // end change master dimension tests. --------------------

        // new height, width is larger than original. ---------------
        $options = ['master_dim' => 'auto'];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 400);
        $this->assertEquals($result['height'], 800);

        $options = [
            'master_dim' => 'auto',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 1000);
        $this->assertEquals($result['height'], 2000);

        $options = ['master_dim' => 'width'];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 400);
        $this->assertEquals($result['height'], 800);

        $options = [
            'master_dim' => 'width',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 2000);
        $this->assertEquals($result['height'], 4000);

        $options = ['master_dim' => 'height'];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 400);
        $this->assertEquals($result['height'], 800);

        $options = [
            'master_dim' => 'height',
            'allow_resize_larger' => true,
        ];
        $result = $Calculation->calculateNewDimensionByRatio(400, 800, 2000, 2000, $options);
        $this->assertEquals($result['width'], 1000);
        $this->assertEquals($result['height'], 2000);
        // end new height, width is larger than original. ----------
        // end portrait image dimension tests. ---------------------------------------------------

        // square image dimension tests. ---------------------------------------------------------
        $result = $Calculation->calculateNewDimensionByRatio(800, 800, 200, 200);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 200);

        $result = $Calculation->calculateNewDimensionByRatio(800, 800, 0, 0);
        $this->assertEquals($result['width'], 1);
        $this->assertEquals($result['height'], 1);

        // change master dimension -------------------------------
        $options = ['master_dim' => 'width'];
        $result = $Calculation->calculateNewDimensionByRatio(800, 800, 200, 200, $options);
        $this->assertEquals($result['width'], 200);
        $this->assertEquals($result['height'], 200);

        $options = ['master_dim' => 'height'];
        $result = $Calculation->calculateNewDimensionByRatio(800, 800, 400, 400, $options);
        $this->assertEquals($result['width'], 400);
        $this->assertEquals($result['height'], 400);

        $result = $Calculation->calculateNewDimensionByRatio(800, 800, 2000, 2000);
        $this->assertEquals($result['width'], 800);
        $this->assertEquals($result['height'], 800);
        // end change master dimension tests. --------------------
        // end square image dimension tests. ----------------------------------------------------

        unset($Calculation, $options);
    }// testCalculateNewDimensionByRatio


    public function testCalculateStartXOfCenter()
    {
        $Calculation = new \Rundiz\Image\Calculation();
        $this->assertSame(150, $Calculation->calculateStartXOfCenter(200, 500));
        $this->assertSame(150, $Calculation->calculateStartXOfCenter(200.10, 500.50));
        $this->assertSame(151, $Calculation->calculateStartXOfCenter(202.91, 503.91));
        $this->assertSame(550, $Calculation->calculateStartXOfCenter(400, 1500));
        unset($Calculation);
    }// testCalculateStartXOfCenter


    public function testCalculateWatermarkImageStartXY()
    {
        $Calculation = new \Rundiz\Image\Calculation();
        $this->assertSame([10, 10], $Calculation->calculateWatermarkImageStartXY('left', 'top', 800, 600, 200, 100));
        $this->assertSame([10, 490], $Calculation->calculateWatermarkImageStartXY('left', 'bottom', 800, 600, 200, 100));// (600-100)-10 that is padding = 490
        $this->assertSame([590, 490], $Calculation->calculateWatermarkImageStartXY('right', 'bottom', 800, 600, 200, 100));// (800-(200+10 that is padding)) = 590
        $this->assertSame([590, 10], $Calculation->calculateWatermarkImageStartXY('right', 'top', 800, 600, 200, 100));
        $this->assertSame([300, 250], $Calculation->calculateWatermarkImageStartXY('center', 'middle', 800, 600, 200, 100));
        unset($Calculation);
    }// testCalculateWatermarkImageStartXY


    public function testCalculateWidthHeightIndependent()
    {
        $Calculation = new \Rundiz\Image\Calculation();
        $this->assertSame([1422, 450], $Calculation->calculateWidthHeightIndependent(1920, 1080, 800, 800));
        $this->assertSame([1067, 338], $Calculation->calculateWidthHeightIndependent(1920, 1080, 600, 600));
        unset($Calculation);
    }// testCalculateWidthHeightIndependent


    public function testConvertAlpha127ToRgba()
    {
        $Calculation = new \Rundiz\Image\Calculation();
        $this->assertSame('1.00', $Calculation->convertAlpha127ToRgba(-1));// less than 0, set to 0 and calculate
        $this->assertSame('1.00', $Calculation->convertAlpha127ToRgba(0));
        $this->assertSame('0.69', $Calculation->convertAlpha127ToRgba(40));
        $this->assertSame('0.29', $Calculation->convertAlpha127ToRgba(90));
        $this->assertSame('0.00', $Calculation->convertAlpha127ToRgba(127));
        $this->assertSame('0.00', $Calculation->convertAlpha127ToRgba(128));// over max, reduced to 127 and calculate
        unset($Calculation);
    }// testConvertAlpha127ToRgba


    public function testGetSourceImageOrientation()
    {
        $Calculation = new \Rundiz\Image\Tests\ExtendedCalculation();
        $this->assertSame('S', $Calculation->getSourceImageOrientation(400, 400));
        $this->assertSame('S', $Calculation->getSourceImageOrientation('400', '400'));
        $this->assertSame('S', $Calculation->getSourceImageOrientation(600.66, 600.66));

        $this->assertSame('L', $Calculation->getSourceImageOrientation(800, 600));
        $this->assertSame('L', $Calculation->getSourceImageOrientation(800.1, 800));

        $this->assertSame('P', $Calculation->getSourceImageOrientation(800, 1600));
        $this->assertSame('P', $Calculation->getSourceImageOrientation(800, 800.1));
        unset($Calculation);
    }// testGetSourceImageOrientation


}
