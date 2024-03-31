<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * No operation.
 * 
 * Usually use for open source image and save as.
 * 
 * @since 3.1.4
 */
class NoOp extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute()
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

        // copy source image to destination.
        imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $source_image_width, $source_image_height);

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
