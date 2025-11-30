<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Resize (not aspect ratio).
 * 
 * @since 3.1.0
 */
class Resize extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($width, $height)
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

        // resize
        imagecopyresampled($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);

        // clear unused variable
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            // if there is source image object.
            if ($this->Gd->source_image_object != $this->Gd->destination_image_object && version_compare(PHP_VERSION, '8.0', '<')) {
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
