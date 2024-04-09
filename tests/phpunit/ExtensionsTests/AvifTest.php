<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Tests\ExtensionsTests;


class AvifTest extends \Rundiz\Image\Tests\RDICommonTestCase
{


    public function testAvifInfo()
    {
        $Avif = new \Rundiz\Image\Extensions\Avif(static::$source_images_dir . 'source-image.avif');
        $this->assertTrue(array_key_exists('HEIGHT', $Avif->avifInfo()));
        $this->assertTrue(array_key_exists('WIDTH', $Avif->avifInfo()));
        $avifInfo = $Avif->avifInfo();
        $this->assertTrue($avifInfo['HEIGHT'] > 0);
        $this->assertTrue($avifInfo['WIDTH'] > 0);
        unset($Avif, $avifInfo);
    }// testAvifInfo


}
