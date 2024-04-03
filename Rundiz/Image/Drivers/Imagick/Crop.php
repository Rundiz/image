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


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        $this->ImagickD->Imagick->setFirstIterator();

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

        if ($this->ImagickD->source_image_frames > 1) {
            // if animated.
            $this->ImagickD->Imagick = $this->ImagickD->Imagick->coalesceImages();
            if (is_object($this->ImagickD->Imagick)) {
                $i = 1;
                foreach ($this->ImagickD->Imagick as $Frame) {
                    // fill background color.
                    if ($fill != 'transparent') {
                        $Frame->setImageBackgroundColor($$fill);
                        $Frame->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
                    } else {
                        $Frame->setImageBackgroundColor($transparent);
                    }
                    // crop an image.
                    $this->ImagickD->Imagick->cropImage($width, $height, $start_x, $start_y);
                    $Frame->extentImage(
                        $width, 
                        $height, 
                        $this->calculateStartXOfCenter($width, $this->ImagickD->Imagick->getImageWidth()), 
                        $this->calculateStartXOfCenter($height, $this->ImagickD->Imagick->getImageHeight())
                    );// this make crop larger correctly.
                    $this->ImagickD->Imagick->setImagePage($width, $height, 0, 0);
                    if ($i == 1) {
                        $this->ImagickD->ImagickFirstFrame = $Frame->getImage();
                    }
                    $i++;
                }// endforeach;
                unset($Frame, $i);
            }
        } else {
            // if non-animated.
            // fill background color.
            if ($fill != 'transparent') {
                $this->ImagickD->Imagick->setImageBackgroundColor($$fill);
                $this->ImagickD->Imagick->setImageAlphaChannel(\Imagick::ALPHACHANNEL_REMOVE);
            } else {
                $this->ImagickD->Imagick->setImageBackgroundColor($transparent);
            }
            // crop an image.
            $this->ImagickD->Imagick->cropImage($width, $height, $start_x, $start_y);
            $this->ImagickD->Imagick->extentImage(
                $width, 
                $height, 
                $this->calculateStartXOfCenter($width, $this->ImagickD->Imagick->getImageWidth()), 
                $this->calculateStartXOfCenter($height, $this->ImagickD->Imagick->getImageHeight())
            );// this make crop larger correctly.
            $this->ImagickD->Imagick->setImagePage($width, $height, 0, 0);// this make non-animated GIF has correct dimension.
            $this->ImagickD->ImagickFirstFrame = null;
        }// endif;

        $black->destroy();
        $white->destroy();
        $transparent->destroy();
        $transwhite->destroy();

        return true;
    }// execute


}
