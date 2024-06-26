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


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($width, $height)
    {
        // begins resize
        if ($this->ImagickD->source_image_frames > 1) {
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    $Frame->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }// endforeach;
                unset($Frame, $i);
            }
        } else {
            $this->ImagickD->Imagick->resizeImage($width, $height, $this->ImagickD->imagick_filter, 1);
            $this->ImagickD->ImagickFirstFrame = null;
        }

        return true;
    }// execute


}
