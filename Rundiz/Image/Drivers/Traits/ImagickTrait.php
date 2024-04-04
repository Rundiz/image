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


}
