<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Rotate
 * 
 * @since 3.1.0
 */
class Rotate extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Traits\CalculationTrait;


    use \Rundiz\Image\Traits\ImageTrait;


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($degree = 90)
    {
        $degree = $this->normalizeDegree($degree);

        // begins rotate.
        if (is_int($degree)) {
            // rotate by degree
            if ($this->ImagickD->source_image_frames > 1) {
                // if animated.
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                if (is_object($this->ImagickD->Imagick)) {
                    $i = 1;
                    foreach ($this->ImagickD->Imagick as $Frame) {
                        $Frame->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                        if ($i == 1) {
                            $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                        }
                        $i++;
                    }// endforeach;
                    unset($Frame, $i);
                }
            } else {
                // if non-animated.
                $this->ImagickD->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                $this->ImagickD->ImagickFirstFrame = null;
            }// endif;
        } else {
            // flip image
            if ($this->ImagickD->source_image_frames > 1) {
                // if animated.
                $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                if (is_object($this->ImagickD->Imagick)) {
                    $i = 1;
                    foreach ($this->ImagickD->Imagick as $Frame) {
                        if ($degree === 'hor') {
                            $Frame->flopImage();
                        } elseif ($degree == 'vrt') {
                            $Frame->flipImage();
                        } else {
                            $Frame->flopImage();
                            $Frame->flipImage();
                        }
                        if ($i == 1) {
                            $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                        }
                        $i++;
                    }// endforeach;
                    unset($Frame, $i);
                }
            } else {
                // if non-animated.
                if ($degree === 'hor') {
                    $this->ImagickD->Imagick->flopImage();
                } elseif ($degree == 'vrt') {
                    $this->ImagickD->Imagick->flipImage();
                } else {
                    $this->ImagickD->Imagick->flopImage();
                    $this->ImagickD->Imagick->flipImage();
                }
                $this->ImagickD->ImagickFirstFrame = null;
            }// endif;
        }// endif; check degree

        $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
        $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

        $this->ImagickD->last_modified_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->last_modified_image_width = $this->ImagickD->destination_image_width;
        $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
        $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;

        return true;
    }// execute


}
