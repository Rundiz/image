<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Resize (not aspect ratio).
 * 
 * @since 3.1.0
 */
class Resize extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    public function execute($width, $height)
    {
        // begins resize
        switch ($this->ImagickD->source_image_type) {
            case IMAGETYPE_GIF:
                if ($this->ImagickD->source_image_frames > 1) {
                    $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                    if (is_object($this->ImagickD->Imagick)) {
                        $i = 1;
                        foreach ($this->ImagickD->Imagick as $Frame) {
                            $Frame->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
                            $Frame->setImagePage(0, 0, 0, 0);
                            if ($i == 1) {
                                $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                            }
                            $i++;
                        }
                        unset($Frame, $i);
                    }
                } else {
                    $this->ImagickD->Imagick->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
                    $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                    $this->ImagickD->ImagickFirstFrame = null;
                }
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_WEBP:
                $this->ImagickD->Imagick->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
                break;
            default:
                $Ims = $this->ImagickD->getStatic();
                $this->setErrorMessage('Unable to resize this kind of image.', $Ims::RDIERROR_RESIZE_UNKNOWIMG);
                unset($Ims);
                return false;
        }// endswitch;

        return true;
    }// execute


}
