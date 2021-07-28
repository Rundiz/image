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


    public function execute($degree = 90)
    {

        $degree = $this->normalizeDegree($degree);

        // begins rotate.
        if (is_int($degree)) {
            // rotate by degree
            switch ($this->Gd->source_image_type) {
                case IMAGETYPE_GIF:
                    // set source image width and height
                    $source_image_width = imagesx($this->Gd->source_image_object);
                    $source_image_height = imagesy($this->Gd->source_image_object);

                    $this->Gd->destination_image_object = imagecreatetruecolor($source_image_width, $source_image_height);
                    $transwhite = imagecolorallocatealpha($this->Gd->destination_image_object, 255, 255, 255, 127);
                    imagefill($this->Gd->destination_image_object, 0, 0, $transwhite);
                    imagecolortransparent($this->Gd->destination_image_object, $transwhite);
                    imagecopy($this->Gd->destination_image_object, $this->Gd->source_image_object, 0, 0, 0, 0, $source_image_width, $source_image_height);
                    $this->Gd->destination_image_object = imagerotate($this->Gd->destination_image_object, $degree, $transwhite);
                    unset($source_image_height, $source_image_width, $transwhite);
                    break;
                case IMAGETYPE_JPEG:
                    $white = imagecolorallocate($this->Gd->source_image_object, 255, 255, 255);
                    $this->Gd->destination_image_object = imagerotate($this->Gd->source_image_object, $degree, $white);
                    unset($white);
                    break;
                case IMAGETYPE_PNG:
                case IMAGETYPE_WEBP:
                    $transwhite = imageColorAllocateAlpha($this->Gd->source_image_object, 0, 0, 0, 127);
                    $this->Gd->destination_image_object = imagerotate($this->Gd->source_image_object, $degree, $transwhite);
                    imagealphablending($this->Gd->destination_image_object, false);
                    imagesavealpha($this->Gd->destination_image_object, true);
                    unset($transwhite);
                    break;
                default:
                    $this->Gd->status = false;
                    $this->Gd->status_msg = 'Unable to rotate this kind of image.';
                    return false;
            }

            $this->Gd->destination_image_height = imagesy($this->Gd->destination_image_object);
            $this->Gd->destination_image_width = imagesx($this->Gd->destination_image_object);

            $this->Gd->source_image_height = $this->Gd->destination_image_height;
            $this->Gd->source_image_width = $this->Gd->destination_image_width;
        } else {
            // flip image
            if (version_compare(phpversion(), '5.5', '<')) {
                $this->Gd->status = false;
                $this->Gd->status_msg = 'Unable to flip image using PHP older than 5.5.';
                return false;
            }

            if ($degree == 'hor') {
                $mode = IMG_FLIP_HORIZONTAL;
            } elseif ($degree == 'vrt') {
                $mode = IMG_FLIP_VERTICAL;
            } else {
                $mode = IMG_FLIP_BOTH;
            }

            // flip image.
            imageflip($this->Gd->source_image_object, $mode);
            unset($mode);

            $this->Gd->destination_image_object = $this->Gd->source_image_object;
            $this->Gd->destination_image_height = imagesy($this->Gd->source_image_object);
            $this->Gd->destination_image_width = imagesx($this->Gd->source_image_object);

            $this->Gd->source_image_height = $this->Gd->destination_image_height;
            $this->Gd->source_image_width = $this->Gd->destination_image_width;
        }

        // clear unused variable
        if ($this->isResourceOrGDObject($this->Gd->source_image_object)) {
            if ($this->Gd->source_image_object != $this->Gd->destination_image_object) {
                imagedestroy($this->Gd->source_image_object);
            }
            $this->Gd->source_image_object = null;
        }

        return true;
    }// execute


}
