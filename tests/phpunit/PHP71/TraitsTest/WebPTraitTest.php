<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\PHP71\TraitsTest;


/**
 * @group processing
 */
class WebPTraitTest extends \Rundiz\Image\Tests\PHP71\CommonTestAbstractClass
{


    public function testWebPInfo()
    {
        $ImgAc = new \Rundiz\Image\Tests\ExtendedImageAbstractClass(static::$source_images_dir . 'city-amsterdam.webp');
        $webpInfo = $ImgAc->webPInfo(static::$source_images_dir . 'city-amsterdam.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertFalse($webpInfo['ALPHA']);
        $webpInfo = $ImgAc->webPInfo(static::$source_images_dir . 'city-amsterdam-animated.webp');
        $this->assertTrue($webpInfo['ANIMATION']);
        $this->assertFalse($webpInfo['ALPHA']);
        $webpInfo = $ImgAc->webPInfo(static::$source_images_dir . 'transparent-lossless.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertTrue($webpInfo['ALPHA']);
        unset($ImgAc);
    }// testWebPInfo


}
