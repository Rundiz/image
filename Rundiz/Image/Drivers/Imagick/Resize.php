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
        if ($this->ImagickD->source_image_type === IMAGETYPE_GIF) {
            // gif
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
        } elseif ($this->ImagickD->source_image_type === IMAGETYPE_JPEG || $this->ImagickD->source_image_type === IMAGETYPE_PNG) {
            // jpg or png
            $this->ImagickD->Imagick->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
        } else {
            $this->ImagickD->status = false;
            $this->ImagickD->status_msg = 'Unable to resize this kind of image.';
            return false;
        }

        return true;
    }// execute


}
