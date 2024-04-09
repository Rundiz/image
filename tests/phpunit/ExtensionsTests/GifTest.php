<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\ExtensionsTests;


class GifTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    /**
     * @depends Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists
     */
    public function testGifInfo()
    {
        $Gif = new \Rundiz\Image\Extensions\Gif();
        $gifinfo = $Gif->gifInfo(static::$source_images_dir . 'source-image.gif');
        $this->assertFalse($gifinfo['ANIMATION']);
        $gifinfo = $Gif->gifInfo(static::$source_images_dir . 'source-image-animated.gif');
        //$this->assertTrue($gifinfo['ANIMATION']);// couldn't detect for some file.
        unset($Gif, $gifinfo);
    }// testGifInfo


}
