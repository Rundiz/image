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
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-non-transparent.webp');
        $this->assertTrue($WebP->isGDSupported());
        unset($WebP);

        // not webp.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertFalse($WebP->isGDSupported());
        unset($WebP);

        // animated.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-animated.webp');
        $this->assertFalse($WebP->isGDSupported());
        unset($WebP);

        // transparent.
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.webp');
            $this->assertTrue($WebP->isGDSupported());
            unset($WebP);
        } else {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.webp');
            $this->assertFalse($WebP->isGDSupported());
            unset($WebP);
        }// endif; php version compare.
    }// testIsGDSupported


    public function testIsImagickSupported()
    {
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-non-transparent.webp');
        $this->assertTrue($WebP->isImagickSupported());
        unset($WebP);

        // not webp.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.jpg');
        $this->assertFalse($WebP->isImagickSupported());
        unset($WebP);

        // transparent.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam.webp');
        $this->assertTrue($WebP->isImagickSupported());
        unset($WebP);

        // animated.
        if (version_compare(PHP_VERSION, '7.3', '>=')) {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-animated.webp');
            $this->assertTrue($WebP->isImagickSupported());
            unset($WebP);
        } else {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'city-amsterdam-animated.webp');
            $this->assertFalse($WebP->isImagickSupported());
            unset($WebP);
        }
    }// testIsImagickSupported


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
