<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\PHP71;


/**
 * @group processing
 */
class AbstractImageTest extends \Rundiz\Image\Tests\PHP71\CommonTestAbstractClass
{


    public function testBuildSourceImageData()
    {
        $this->requireFilesExistsAndFolderWritable();
        foreach (static::$source_images_set as $source_image) {
            $ImgAC = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . $source_image);
            $this->assertTrue($ImgAC->status);
            unset($ImgAC);
        }// endforeach;
    }// testBuildSourceImageData


    public function testGetImageSize()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
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
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'sample-portrait.jpg');
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
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertSame('L', $ImgAc->getSourceImageOrientation());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'sample-portrait.jpg');
        $this->assertSame('P', $ImgAc->getSourceImageOrientation());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'sample-square.jpg');
        $this->assertSame('S', $ImgAc->getSourceImageOrientation());
        unset($ImgAc);
    }// testGetSourceImageOrientation


    public function testIsClassSetup()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertTrue($ImgAc->isClassSetup());
        $ImgAc = new \Rundiz\Image\Tests\ExtendedAbstractImage(static::$source_images_dir . 'file-not-exists' . date('YmdHis') . '.jpg');
        $this->assertFalse($ImgAc->isClassSetup());
        unset($ImgAc);
    }// testIsClassSetup


}
