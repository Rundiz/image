<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Rotate
 * 
 * @since 3.1.0
 */
class Rotate extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    use \Rundiz\Image\Traits\ImageTrait;


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($degree = 90)
    {
        // get and set source (or last modified) image width and height
        $source_image_width = $this->Gd->source_image_width;
        if ($this->Gd->last_modified_image_width > 0) {
            $source_image_width = $this->Gd->last_modified_image_width;
        }
        $source_image_height = $this->Gd->source_image_height;
        if ($this->Gd->last_modified_image_height > 0) {
            $source_image_height = $this->Gd->last_modified_image_height;
        }

        $degree = $this->normalizeDegree($degree);
        // set color
        $transparent = imagecolorallocatealpha($this->Gd->destination_image_object, 255, 255, 255, 127);

        // begins rotate.
        if (is_int($degree)) {
            // if rotate by degree
            // copy source image to destination.
            imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $source_image_width, $source_image_height);
            // rotate
            $this->Gd->destination_image_object = imagerotate($this->Gd->destination_image_object, $degree, $transparent);
            imagesavealpha($this->Gd->destination_image_object, true);
        } else {
            // flip image
            if (version_compare(phpversion(), '5.5', '<')) {
                $Gds = $this->Gd->getStatic();
                $this->setErrorMessage('Unable to flip image using PHP older than 5.5.', $Gds::RDIERROR_FLIP_NOTSUPPORTED);
                unset($Gds);
                return false;
            }

            if ($degree == 'hor') {
                $mode = IMG_FLIP_HORIZONTAL;
            } elseif ($degree == 'vrt') {
                $mode = IMG_FLIP_VERTICAL;
            } else {
                $mode = IMG_FLIP_BOTH;
            }

            // copy source image to destination.
            imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $source_image_width, $source_image_height);
            // flip image.
            imageflip($this->Gd->destination_image_object, $mode);
            unset($mode);
        }// endif; check degree

        $this->Gd->destination_image_height = imagesy($this->Gd->destination_image_object);
        $this->Gd->destination_image_width = imagesx($this->Gd->destination_image_object);

        $this->Gd->last_modified_image_height = $this->Gd->destination_image_height;
        $this->Gd->last_modified_image_width = $this->Gd->destination_image_width;
        $this->Gd->source_image_height = $this->Gd->destination_image_height;
        $this->Gd->source_image_width = $this->Gd->destination_image_width;

        unset($transparent);

        // clear unused variable
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            // if there is source image object.
            if ($this->Gd->source_image_object != $this->Gd->destination_image_object) {
                // if source image object is not the same as destination.
                // this is for prevent when chaining it will be destroy both variables.
                imagedestroy($this->Gd->source_image_object);
            }
            $this->Gd->source_image_object = null;
        }

        unset($source_image_height, $source_image_width);
        return true;
    }// execute


}
