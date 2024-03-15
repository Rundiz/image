<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\ExtensionsTests;


class WebPTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    /**
     * @depends Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists
     */
    public function testWebPInfo()
    {
        $Webp = new \Rundiz\Image\Extensions\WebP();
        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'city-amsterdam.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertTrue($webpInfo['ALPHA']);
        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'city-amsterdam-non-transparent.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertFalse($webpInfo['ALPHA']);
        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'city-amsterdam-animated.webp');
        $this->assertTrue($webpInfo['ANIMATION']);
        $this->assertFalse($webpInfo['ALPHA']);
        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'transparent-lossless.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertTrue($webpInfo['ALPHA']);
        unset($Webp);
    }// testWebPInfo


}
