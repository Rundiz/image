<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * No operation.
 * 
 * Usually use for open source image and save as.
 * 
 * @since 3.1.4
 */
class NoOp extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute()
    {
        if ($this->ImagickD->source_image_frames > 1) {
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    $Frame->setImagePage(0, 0, 0, 0);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }
                unset($Frame, $i);
            }
        } else {
            $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
            $this->ImagickD->ImagickFirstFrame = null;
        }

        return true;
    }// execute


}
