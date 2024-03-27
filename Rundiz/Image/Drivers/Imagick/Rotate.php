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


    public function execute($degree = 90)
    {

        $degree = $this->normalizeDegree($degree);

        // begins rotate.
        if (is_int($degree)) {
            // rotate by degree
            switch ($this->ImagickD->source_image_type) {
                case IMAGETYPE_GIF:
                    if ($this->ImagickD->source_image_frames > 1) {
                        $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                        if (is_object($this->ImagickD->Imagick)) {
                            $i = 1;
                            foreach ($this->ImagickD->Imagick as $Frame) {
                                $Frame->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1) {
                                    $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                                }
                                $i++;
                            }
                            unset($Frame, $i);
                        }
                    } else {
                        $this->ImagickD->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                        $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                        $this->ImagickD->ImagickFirstFrame = null;
                    }
                    break;
                case IMAGETYPE_JPEG:
                case IMAGETYPE_PNG:
                case IMAGETYPE_WEBP:
                    $this->ImagickD->Imagick->rotateImage(new \ImagickPixel('rgba(255, 255, 255, 0)'), $this->calculateCounterClockwise($degree));
                    break;
                default:
                    $Ims = $this->ImagickD->getStatic();
                    $this->setErrorMessage('Unable to rotate this kind of image.', $Ims::RDIERROR_ROTATE_UNKNOWIMG);
                    unset($Ims);
                    return false;
            }

            $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
            $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

            $this->ImagickD->last_modified_image_height = $this->ImagickD->destination_image_height;
            $this->ImagickD->last_modified_image_width = $this->ImagickD->destination_image_width;
            $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
            $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;
        } else {
            // flip image
            switch ($this->ImagickD->source_image_type) {
                case IMAGETYPE_GIF:
                    if ($this->ImagickD->source_image_frames > 1) {
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
                                $Frame->setImagePage(0, 0, 0, 0);
                                if ($i == 1 && $this->ImagickD->ImagickFirstFrame == null) {
                                    $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                                }
                                $i++;
                            }
                            unset($Frame, $i);
                        }
                    } else {
                        if ($degree === 'hor') {
                            $this->ImagickD->Imagick->flopImage();
                        } elseif ($degree == 'vrt') {
                            $this->ImagickD->Imagick->flipImage();
                        } else {
                            $this->ImagickD->Imagick->flopImage();
                            $this->ImagickD->Imagick->flipImage();
                        }
                        $this->ImagickD->ImagickFirstFrame = null;
                    }
                    break;
                case IMAGETYPE_JPEG:
                case IMAGETYPE_PNG:
                case IMAGETYPE_WEBP:
                    if ($degree === 'hor') {
                        $this->ImagickD->Imagick->flopImage();
                    } elseif ($degree == 'vrt') {
                        $this->ImagickD->Imagick->flipImage();
                    } else {
                        $this->ImagickD->Imagick->flopImage();
                        $this->ImagickD->Imagick->flipImage();
                    }
                    break;
                default:
                    $Ims = $this->ImagickD->getStatic();
                    $this->setErrorMessage('Unable to flip this kind of image.', $Ims::RDIERROR_FLIP_UNKNOWIMG);
                    unset($Ims);
                    return false;
            }

            $this->ImagickD->destination_image_height = $this->ImagickD->Imagick->getImageHeight();
            $this->ImagickD->destination_image_width = $this->ImagickD->Imagick->getImageWidth();

            $this->ImagickD->last_modified_image_height = $this->ImagickD->destination_image_height;
            $this->ImagickD->last_modified_image_width = $this->ImagickD->destination_image_width;
            $this->ImagickD->source_image_height = $this->ImagickD->destination_image_height;
            $this->ImagickD->source_image_width = $this->ImagickD->destination_image_width;
        }

        return true;
    }// execute


}
