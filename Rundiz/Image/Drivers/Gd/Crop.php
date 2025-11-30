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


    /**
     * Execute the command.
     * 
     * @return bool
     */
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
        $transparent = imagecolorallocatealpha($this->Gd->destination_image_object, 255, 255, 255, 127);

        if ($fill != 'transparent' && $fill != 'white' && $fill != 'black') {
            $fill = 'transparent';
        }

        // fill destination image object (this should be empty canvas).
        imagefill($this->Gd->destination_image_object, 0, 0, $$fill);
        // crop
        imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, $start_x, $start_y, $width, $height);
        // fill again in case cropping size is larger than source image.
        imagefill($this->Gd->destination_image_object, 0, 0, $$fill);

        // clear unused variables
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            // if there is source image object.
            if ($this->Gd->source_image_object != $this->Gd->destination_image_object && version_compare(PHP_VERSION, '8.0', '<')) {
                // if source image object is not the same as destination.
                // this is for prevent when chaining it will be destroy both variables.
                imagedestroy($this->Gd->source_image_object);
            }
            $this->Gd->source_image_object = null;
        }

        return true;
    }// execute


}
