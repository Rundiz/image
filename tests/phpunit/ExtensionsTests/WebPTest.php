<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\ExtensionsTests;


class WebPTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testIsGDSupported()
    {
        // non-transparent webp must be supported in all PHP version (>= 5.4) but in fact, it is not fully supported prior PHP 5.6.
        $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-non-transparent.webp');
        $this->assertTrue($Webp->isGDSupported());
        unset($Webp);

        // not webp.
        $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertFalse($Webp->isGDSupported());
        unset($Webp);

        // animated.
        $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-animated.webp');
        $this->assertFalse($Webp->isGDSupported());
        unset($Webp);

        // transparent.
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.webp');
            $this->assertTrue($Webp->isGDSupported());
            unset($Webp);
            $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'transparent-lossless.webp');
            $this->assertTrue($Webp->isGDSupported());
            unset($Webp);
        } else {
            $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.webp');
            $this->assertFalse($Webp->isGDSupported());
            unset($Webp);
            $Webp = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'transparent-lossless.webp');
            $this->assertFalse($Webp->isGDSupported());
            unset($Webp);
        }// endif; php version compare.
    }// testIsGDSupported


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
