<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Crop
 * 
 * @since 3.1.0
 */
class Crop extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Traits\CalculationTrait;


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    public function execute($width, $height, $start_x = '0', $start_y = '0', $fill = 'transparent')
    {
        // calculate start x while $start_x was set as 'center'.
        if ($start_x === 'center') {
            $canvas_width = imagesx($this->Gd->source_image_object);
            $object_width = imagesx($this->Gd->destination_image_object);

            $start_x = $this->calculateStartXOfCenter($object_width, $canvas_width);

            unset($canvas_width, $object_width);
        } else {
            $start_x = intval($start_x);
        }

        // calculate start y while $start_y was set as 'middle'
        if ($start_y === 'middle') {
            $canvas_height = imagesy($this->Gd->source_image_object);
            $object_height = imagesy($this->Gd->destination_image_object);

            $start_y = $this->calculateStartXOfCenter($object_height, $canvas_height);

            unset($canvas_height, $object_height);
        } else {
            $start_y = intval($start_y);
        }

        // set color
        $black = imagecolorallocate($this->Gd->destination_image_object, 0, 0, 0);
        $white = imagecolorallocate($this->Gd->destination_image_object, 255, 255, 255);
        $transwhite = imagecolorallocatealpha($this->Gd->destination_image_object, 255, 255, 255, 127);// set color transparent white

        if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
            $fill = 'transparent';
        }

        // begins crop
        if ($this->Gd->source_image_type === IMAGETYPE_GIF) {
            // gif
            if ($fill == 'transparent') {
                imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
                imagecolortransparent($this->Gd->destination_image_object, $transwhite);
            } else {
                imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
            }

            imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            // fill "again" in case that cropping image is larger than source image.
            if ($width > imagesx($this->Gd->source_image_object) || $height > imagesy($this->Gd->source_image_object)) {
                if ($fill == 'transparent') {
                    imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->Gd->destination_image_object, $transwhite);
                } else {
                    imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
                }
            }
        } elseif ($this->Gd->source_image_type === IMAGETYPE_JPEG) {
            // jpg
            imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            if ($fill != 'transparent') {
                imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
            }
        } elseif ($this->Gd->source_image_type === IMAGETYPE_PNG) {
            // png
            if ($fill == 'transparent') {
                imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
                imagecolortransparent($this->Gd->destination_image_object, $black);
                imagealphablending($this->Gd->destination_image_object, false);
                imagesavealpha($this->Gd->destination_image_object, true);
            } else {
                imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
            }

            imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, $start_x, $start_y, $width, $height);

            // fill "again" in case that cropping image is larger than source image.
            if ($width > imagesx($this->Gd->source_image_object) || $height > imagesy($this->Gd->source_image_object)) {
                if ($fill == 'transparent') {
                    imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->Gd->destination_image_object, $black);
                    imagealphablending($this->Gd->destination_image_object, false);
                    imagesavealpha($this->Gd->destination_image_object, true);
                } else {
                    imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
                }
            }
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = 'Unable to crop this kind of image.';
            return false;
        }

        // clear unused variables
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            imagedestroy($this->Gd->source_image_object);
            $this->Gd->source_image_object = null;
        }

        return true;
    }// execute


}
