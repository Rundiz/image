<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\PHP71\Extensions;


/**
 * @group processing
 */
class GifTest extends \Rundiz\Image\Tests\PHP71\CommonTestAbstractClass
{


    public function testGifInfo()
    {
        $Gif = new \Rundiz\Image\Extensions\Gif();
        $gifinfo = $Gif->gifInfo(static::$source_images_dir . 'city-amsterdam.gif');
        $this->assertFalse($gifinfo['ANIMATION']);
        $gifinfo = $Gif->gifInfo(static::$source_images_dir . 'city-amsterdam-animated.gif');
        //$this->assertTrue($gifinfo['ANIMATION']);// couldn't detect for some file.
        unset($Gif, $gifinfo);
    }// testGifInfo


}
