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


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($file_ext = '')
    {

        if ($file_ext == null) {
            $file_ext = str_replace('.', '', $this->Gd->source_image_ext);
        }
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        if ($check_file_ext === 'gif') {
            // if show as gif
            $show_result = imagegif($this->Gd->destination_image_object);
        } elseif ($check_file_ext === 'jpg') {
            // if show as jpg
            $this->fillWhiteBgOnDestination();

            $show_result = imagejpeg($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if show as png

            $show_result = imagepng($this->Gd->destination_image_object, null, $this->Gd->png_quality);
        } elseif ($check_file_ext === 'webp') {
            // if show as webp
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

            $show_result = imagewebp($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
        } elseif (function_exists('imageavif') && $check_file_ext === 'avif') {
            $show_result = imageavif($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage(sprintf('Unable to show this kind of image. (%s)', $check_file_ext), $Gds::RDIERROR_SHOW_UNSUPPORT);
            unset($Gds);
            return false;
        }

        // clear
        unset($check_file_ext, $file_ext);

        if ($show_result !== false) {
            $this->setStatusSuccess();
            return true;
        } else {
            $Gds = $this->Gd->getStatic();
            $this->setErrorMessage('Failed to show the image.', $Gds::RDIERROR_SHOW_FAILED);
            unset($Gds);
            return false;
        }
    }// execute


}
