<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Show image to browser.
 * 
 * @since 3.1.0
 */
class Show extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    public function execute($file_ext = '')
    {

        if ($file_ext == null) {
            $file_ext = str_replace('.', '', $this->Gd->source_image_ext);
        }
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        // show image to browser.
        if ($check_file_ext === 'gif') {
            // if save to gif
            if (
                $this->Gd->source_image_type === IMAGETYPE_PNG || 
                $this->Gd->source_image_type === IMAGETYPE_WEBP
            ) {
                // if source image is png or webp file. 
                // preserve transparency part.
                $this->fillTransparentDestinationImage();
            }

            $show_result = imagegif($this->Gd->destination_image_object);

            if (isset($temp_image_object) && $this->isResourceOrGDObject($temp_image_object)) {
                imagedestroy($temp_image_object);
                unset($temp_image_object);
            }
        } elseif ($check_file_ext === 'jpg') {
            // if save to jpg
            if (
                $this->Gd->source_image_type === IMAGETYPE_PNG || 
                $this->Gd->source_image_type === IMAGETYPE_GIF ||
                $this->Gd->source_image_type === IMAGETYPE_WEBP
            ) {
                // if source image is png or gif or webp file. 
                // convert transparency png to white before save.
                $this->fillWhiteDestinationImage();
            }

            $this->Gd->jpg_quality = intval($this->Gd->jpg_quality);
            if ($this->Gd->jpg_quality < 0 || $this->Gd->jpg_quality > 100) {
                $this->Gd->jpg_quality = 100;
            }

            $show_result = imagejpeg($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);

            if (isset($temp_image_object) && $this->isResourceOrGDObject($temp_image_object)) {
                imagedestroy($temp_image_object);
                unset($temp_image_object);
            }
        } elseif ($check_file_ext === 'png') {
            // if save to png
            // if source image is gif then it is fine, transparency will be send to png with no problem.

            $this->Gd->png_quality = intval($this->Gd->png_quality);
            if ($this->Gd->png_quality < 0 || $this->Gd->png_quality > 9) {
                $this->Gd->png_quality = 0;
            }

            $show_result = imagepng($this->Gd->destination_image_object, null, $this->Gd->png_quality);
        } elseif ($check_file_ext === 'webp') {
            // if save to webp
            // transparency png to webp will be black and transparency gif to webp will be white prior PHP 7.0
            // this is known bug but can't fix and no more update fix from PHP.
            if (version_compare(PHP_VERSION, '7.0', '<')) {
                // if php version is older than 7.0
                // fill white bg.
                $this->fillWhiteDestinationImage();
            }
            if (
                function_exists('imagepalettetotruecolor') && 
                ($this->Gd->source_image_type === IMAGETYPE_GIF || $this->Gd->source_image_type === IMAGETYPE_PNG)
            ) {
                // if imagepalettetotruecolor() function exists (PHP 5.5+)
                // and if source image is gif, png.
                // there will be "Paletter image not supported by webp" error. fix by convert to truecolor.
                imagepalettetotruecolor($this->Gd->destination_image_object);
                imagealphablending($this->Gd->destination_image_object, true);
                imagesavealpha($this->Gd->destination_image_object, true);
            }

            $this->Gd->jpg_quality = intval($this->Gd->jpg_quality);
            if ($this->Gd->jpg_quality < 0 || $this->Gd->jpg_quality > 100) {
                $this->Gd->jpg_quality = 100;
            }

            $show_result = imagewebp($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = 'Unable to show this kind of image.';
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if ($show_result !== false) {
            $this->Gd->status = true;
            $this->Gd->status_msg = null;
            return true;
        } else {
            $this->Gd->status = false;
            $this->Gd->status_msg = 'Failed to show the image.';
            return false;
        }
    }// execute


}
