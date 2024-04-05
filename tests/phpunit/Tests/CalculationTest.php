<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\Tests;


/**
 * Test on `Calculation` class.
 * 
 * Most methods that `Calculation` class uses are already have tests in `CalculationTraitTest` class.
 * 
 * The methods that are in trait should be test with `TraitTest` class, not here.
 */
class CalculationTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


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


    public function testCalculateWidthHeightIndependent()
    {
        $Calculation = new \Rundiz\Image\Calculation();
        $this->assertSame([1422, 450], $Calculation->calculateWidthHeightIndependent(1920, 1080, 800, 800));
        $this->assertSame([1067, 338], $Calculation->calculateWidthHeightIndependent(1920, 1080, 600, 600));
        unset($Calculation);
    }// testCalculateWidthHeightIndependent


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
