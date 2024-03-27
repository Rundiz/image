<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Gd;


/**
 * Save command class.
 * 
 * @since 3.1.0
 */
class Save extends \Rundiz\Image\Drivers\AbstractGdCommand
{


    use \Rundiz\Image\Drivers\Traits\GdTrait;


    public function execute($file_name)
    {
        $FS = new \Rundiz\Image\FileSystem();
        $file_name = $FS->getFileRealpath($file_name);
        $check_file_ext = strtolower($FS->getFileExtension($file_name));
        unset($FS);

        // save to file. each image types use different ways to save.
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

            $save_result = imagegif($this->Gd->destination_image_object, $file_name);
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

            $save_result = imagejpeg($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if save to png
            // if source image is gif then it is fine, transparency will be send to png with no problem.

            $this->Gd->png_quality = intval($this->Gd->png_quality);
            if ($this->Gd->png_quality < 0 || $this->Gd->png_quality > 9) {
                $this->Gd->png_quality = 0;
            }

            $save_result = imagepng($this->Gd->destination_image_object, $file_name, $this->Gd->png_quality);
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

            $save_result = imagewebp($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage(sprintf('Unable to save this kind of image. (%s)', $check_file_ext), $Gds::RDIERROR_SAVE_UNSUPPORT);
            unset($Gds);
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if (isset($save_result) && $save_result !== false) {
            $this->setStatusSuccess();
            return true;
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage('Failed to save the image.', $Gds::RDIERROR_SAVE_FAILED);
            unset($Gds);
            return false;
        }
    }// execute


}
