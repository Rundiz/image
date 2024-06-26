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


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($file_name)
    {
        $FS = new \Rundiz\Image\FileSystem();
        $file_name = $FS->getFileRealpath($file_name);
        $check_file_ext = strtolower($FS->getFileExtension($file_name));
        unset($FS);

        if ($check_file_ext === 'gif') {
            // if save as gif
            $save_result = imagegif($this->Gd->destination_image_object, $file_name);
        } elseif ($check_file_ext === 'jpg') {
            // if save as jpg
            $this->fillWhiteBgOnDestination();

            $save_result = imagejpeg($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if save as png

            $save_result = imagepng($this->Gd->destination_image_object, $file_name, $this->Gd->png_quality);
        } elseif ($check_file_ext === 'webp') {
            // if save as webp
            if ($this->Gd->source_image_type === IMAGETYPE_PNG) {
                // if source image is PNG file.
                if (version_compare(PHP_VERSION, '7.0', '<')) {
                    // if PHP version is older than 7.0
                    // fix save transparent PNG to WEBP becomes black background.
                    // unlike GIF source image, PNG itself can't be filled with transparent-white on source or destination image directly.
                    // fill white bg.
                    $this->fillWhiteBgOnDestination();
                }
            }

            $save_result = imagewebp($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } elseif (function_exists('imageavif') && $check_file_ext === 'avif') {
            // if save as avif
            $save_result = imageavif($this->Gd->destination_image_object, $file_name, $this->Gd->jpg_quality);
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage(sprintf('Unable to save this kind of image. (%s)', $check_file_ext), $Gds::RDIERROR_SAVE_UNSUPPORT);
            unset($Gds);
            return false;
        }

        // clear
        unset($check_file_ext);

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
