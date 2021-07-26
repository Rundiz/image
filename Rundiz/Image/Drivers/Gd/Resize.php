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


    public function execute($width, $height)
    {
        // get and set source (or last modified) image width and height
        $source_image_width = $this->Gd->source_image_width;
        if ($this->Gd->last_modified_image_width != null) {
            $source_image_width = $this->Gd->last_modified_image_width;
        }
        $source_image_height = $this->Gd->source_image_height;
        if ($this->Gd->last_modified_image_height != null) {
            $source_image_height = $this->Gd->last_modified_image_height;
        }

        // begins resize
        if ($this->Gd->source_image_type === IMAGETYPE_GIF) {
            // gif
            $transwhite = imagecolorallocatealpha($this->Gd->destination_image_object, 255, 255, 255, 127);
            imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
            imagecolortransparent($this->Gd->destination_image_object, $transwhite);
            imagecopyresampled($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
            unset($transwhite);
        } elseif ($this->Gd->source_image_type === IMAGETYPE_JPEG) {
            // jpg
            imagecopyresampled($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
        } elseif ($this->Gd->source_image_type === IMAGETYPE_PNG) {
            // png
            imagealphablending($this->Gd->destination_image_object, false);
            imagesavealpha($this->Gd->destination_image_object, true);
            imagecopyresampled($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $width, $height, $source_image_width, $source_image_height);
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = 'Unable to resize this kind of image.';
            unset($source_image_height, $source_image_width);
            return false;
        }

        // clear unused variable
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            if ($this->Gd->source_image_object != $this->Gd->destination_image_object) {
                imagedestroy($this->Gd->source_image_object);
            }
            $this->Gd->source_image_object = null;
        }

        unset($source_image_height, $source_image_width);
        return true;
    }// execute


}