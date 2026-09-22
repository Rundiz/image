<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Traits;


/**
 * Imagick trait.
 * 
 * @since 3.1.0
 */
trait ImagickTrait
{


    /**
     * Fill white to the image.
     */
    protected function fillWhiteToImage()
    {
        $this->ImagickD->Imagick->setImageBackgroundColor(new \ImagickPixel('white'));
        $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
    }// fillWhiteToImage


    /**
     * Get animated picture's first frame.
     */
    protected function getFirstFrame()
    {
        $this->ImagickD->Imagick->clear();
        $this->ImagickD->Imagick = $this->ImagickD->ImagickFirstFrame;
        $this->ImagickD->ImagickFirstFrame = null;
    }// getFirstFrame


    /**
     * Get image color with alpha value (transparency).
     * 
     * @since 3.2.0
     * @param string $colorName The color name. Supported 'black', 'white', 'red', 'green', 'blue', 'yellow', 'cyan', 'magenta'.
     * @param int $alpha The alpha value for use with `\ImagickPixel('rgba()')`. The value should be 0.0 to 1.0.
     * @throws \InvalidArgumentException Throw exception if provide argument type mismatch.
     */
    private function getImageColorAlpha($colorName, $alpha)
    {
        if (!is_string($colorName)) {
            throw new \InvalidArgumentException('The argument `$colorName` must be string.');
        }
        
        if (!is_numeric($alpha)) {
            throw new \InvalidArgumentException('The argument `$alpha` must be number.');
        }

        switch ($colorName) {
            case 'black':
                return new \ImagickPixel('rgba(0, 0, 0, ' . $alpha . ')');
            case 'red':
                return new \ImagickPixel('rgba(255, 0, 0, ' . $alpha . ')');
            case 'green':
                return new \ImagickPixel('rgba(0, 255, 0, ' . $alpha . ')');
            case 'blue':
                return new \ImagickPixel('rgba(0, 0, 255, ' . $alpha . ')');
            case 'yellow':
                return new \ImagickPixel('rgba(255, 255, 0, ' . $alpha . ')');
            case 'cyan':
                return new \ImagickPixel('rgba(0, 255, 255, ' . $alpha . ')');
            case 'magenta':
                return new \ImagickPixel('rgba(255, 0, 255, ' . $alpha . ')');
            case 'white':
            default:
                return new \ImagickPixel('rgba(255, 255, 255, ' . $alpha . ')');
        }
    }// getImageColorAlpha


}
