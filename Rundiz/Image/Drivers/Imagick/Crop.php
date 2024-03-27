<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Crop
 * 
 * @since 3.1.0
 */
class Crop extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Traits\CalculationTrait;


    public function execute($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        // calculate start x while $start_x was set as 'center'.
        if ($start_x === 'center') {
            $canvas_width = $this->ImagickD->Imagick->getImageWidth();
            $object_width = $width;

            $start_x = $this->calculateStartXOfCenter($object_width, $canvas_width);

            unset($canvas_width, $object_width);
        } else {
            $start_x = intval($start_x);
        }

        // calculate start y while $start_y was set as 'middle'
        if ($start_y === 'middle') {
            $canvas_height = $this->ImagickD->Imagick->getImageHeight();
            $object_height = $height;

            $start_y = $this->calculateStartXOfCenter($object_height, $canvas_height);

            unset($canvas_height, $object_height);
        } else {
            $start_y = intval($start_y);
        }

        // set color
        $black = new \ImagickPixel('black');
        $white = new \ImagickPixel('white');
        $transparent = new \ImagickPixel('transparent');
        $transwhite = new \ImagickPixel('rgba(255, 255, 255, 0)');

        if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
            $fill = 'transparent';
        }

        // begins crop
        switch ($this->ImagickD->source_image_type) {
            case IMAGETYPE_GIF:
                if ($this->ImagickD->source_image_frames > 1) {
                    $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
                    if (is_object($this->ImagickD->Imagick)) {
                        $i = 1;
                        foreach ($this->ImagickD->Imagick as $Frame) {
                            if ($fill != 'transparent') {
                                $Frame->setImageBackgroundColor($$fill);
                                $Frame->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                            } else {
                                $Frame->setImageBackgroundColor($transparent);
                            }
                            $Frame->cropImage($width, $height, $start_x, $start_y);
                            $Frame->extentImage(
                                $width, 
                                $height, 
                                $this->calculateStartXOfCenter($width, $this->ImagickD->Imagick->getImageWidth()), 
                                $this->calculateStartXOfCenter($height, $this->ImagickD->Imagick->getImageHeight())
                            );
                            $Frame->setImagePage($width, $height, 0, 0);
                            if ($i == 1) {
                                $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                            }
                            $i++;
                        }
                        unset($Frame, $i);
                    }
                } else {
                    if ($fill != 'transparent') {
                        $this->ImagickD->Imagick->setImageBackgroundColor($$fill);
                        $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                    } else {
                        $this->ImagickD->Imagick->setImageBackgroundColor($transparent);
                    }
                    $this->ImagickD->Imagick->cropImage($width, $height, $start_x, $start_y);
                    $this->ImagickD->Imagick->setImagePage(0, 0, 0, 0);
                    $this->ImagickD->Imagick->extentImage(
                        $width, 
                        $height, 
                        $this->calculateStartXOfCenter($width, $this->ImagickD->Imagick->getImageWidth()), 
                        $this->calculateStartXOfCenter($height, $this->ImagickD->Imagick->getImageHeight())
                    );
                    $this->ImagickD->ImagickFirstFrame = null;
                }
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_PNG:
            case IMAGETYPE_WEBP:
                $this->ImagickD->Imagick->cropImage($width, $height, $start_x, $start_y);
                $this->ImagickD->Imagick->setImageBackgroundColor($transwhite);// for transparent png and allow to fill other bg color than black in jpg.
                $this->ImagickD->Imagick->extentImage(
                    $width, 
                    $height, 
                    $this->calculateStartXOfCenter($width, $this->ImagickD->Imagick->getImageWidth()), 
                    $this->calculateStartXOfCenter($height, $this->ImagickD->Imagick->getImageHeight())
                );
                if ($fill != 'transparent') {
                    $this->ImagickD->Imagick->setImageBackgroundColor($$fill);
                    $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                }
                break;
            default:
                $Ims = $this->ImagickD->getStatic();
                $this->setErrorMessage('Unable to crop this kind of image.', $Ims::RDIERROR_CROP_UNKNOWNIMG);
                unset($Ims);
                return false;
        }// endswitch;

        $black->destroy();
        $white->destroy();
        $transparent->destroy();
        $transwhite->destroy();

        return true;
    }// execute


}
