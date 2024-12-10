<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\ExtensionsTests;


class WebPTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testIsAnimated()
    {
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-non-transparent.webp');
        $this->assertFalse($WebP->isAnimated());

        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.webp');
        $this->assertFalse($WebP->isAnimated());

        // not WEBP.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.jpg');
        $this->assertFalse($WebP->isAnimated());

        // not exists.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-' . date('YmdHis') . '.jpg');
        $this->assertFalse($WebP->isAnimated());

        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-animated.webp');
        $this->assertTrue($WebP->isAnimated());
        unset($WebP);
    }// testIsAnimated


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testIsGDSupported()
    {
        // non-transparent webp must be supported in all PHP version (>= 5.4) but in fact, it is not fully supported prior PHP 5.6.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-non-transparent.webp');
        $this->assertTrue($WebP->isGDSupported());
        unset($WebP);

        // not webp.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.jpg');
        $this->assertFalse($WebP->isGDSupported());
        unset($WebP);

        // animated.
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-animated.webp');
        $this->assertFalse($WebP->isGDSupported());
        unset($WebP);

        // transparent.
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.webp');
            $this->assertTrue($WebP->isGDSupported());
            unset($WebP);
        } else {
            $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.webp');
            $this->assertFalse($WebP->isGDSupported());
            unset($WebP);
        }// endif; php version compare.
    }// testIsGDSupported


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testIsImagickSupportedAnimated()
    {
        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image-animated.webp');
        $this->assertTrue(is_bool($WebP->isImagickSupportedAnimated()));

        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.webp');
        $this->assertFalse($WebP->isAnimated() && $WebP->isImagickSupportedAnimated());// not animated WEBP.

        $WebP = new \Rundiz\Image\Extensions\WebP(static::$source_images_dir . 'source-image.jpg');
        $this->assertFalse($WebP->isAnimated() && $WebP->isImagickSupportedAnimated());// not WEBP.
        unset($WebP);
    }// testIsImagickSupportedAnimated


    #[Depends('Rundiz\Image\Tests\DependentTests\DirsFilesExistsTest::testImageExists')]
    public function testWebPInfo()
    {
        $Webp = new \Rundiz\Image\Extensions\WebP();
        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'source-image.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertTrue($webpInfo['ALPHA']);
        $this->assertTrue(is_int($webpInfo['HEIGHT']));
        $this->assertTrue(is_int($webpInfo['WIDTH']));

        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'source-image-non-transparent.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertFalse($webpInfo['ALPHA']);
        $this->assertTrue(is_int($webpInfo['HEIGHT']));
        $this->assertTrue(is_int($webpInfo['WIDTH']));

        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'source-image-animated.webp');
        $this->assertTrue($webpInfo['ANIMATION']);
        // do not test alpha for animated 
        // because some program export an animated image with alpha on some frames but some program is not.
        $this->assertTrue(is_int($webpInfo['HEIGHT']));
        $this->assertTrue(is_int($webpInfo['WIDTH']));

        $webpInfo = $Webp->webPInfo(static::$source_images_dir . 'transparent-lossless.webp');
        $this->assertFalse($webpInfo['ANIMATION']);
        $this->assertTrue($webpInfo['ALPHA']);
        $this->assertTrue(is_int($webpInfo['HEIGHT']));
        $this->assertTrue(is_int($webpInfo['WIDTH']));
        $this->assertTrue($webpInfo['HEIGHT'] > 0);
        $this->assertTrue($webpInfo['WIDTH'] > 0);
        unset($Webp);
    }// testWebPInfo


}
