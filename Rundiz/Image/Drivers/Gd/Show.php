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

        // show image to browser.
        if ($check_file_ext === 'gif') {
            // if save to gif
            $show_result = imagegif($this->Gd->destination_image_object);
        } elseif ($check_file_ext === 'jpg') {
            // if save to jpg
            $this->fillWhiteBgOnDestination();
            $this->Gd->jpg_quality = intval($this->Gd->jpg_quality);
            if ($this->Gd->jpg_quality < 0 || $this->Gd->jpg_quality > 100) {
                $this->Gd->jpg_quality = 100;
            }

            $show_result = imagejpeg($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if save to png
            $this->Gd->png_quality = intval($this->Gd->png_quality);
            if ($this->Gd->png_quality < 0 || $this->Gd->png_quality > 9) {
                $this->Gd->png_quality = 0;
            }

            $show_result = imagepng($this->Gd->destination_image_object, null, $this->Gd->png_quality);
        } elseif ($check_file_ext === 'webp') {
            // if save to webp
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

            $this->Gd->jpg_quality = intval($this->Gd->jpg_quality);
            if ($this->Gd->jpg_quality < 0 || $this->Gd->jpg_quality > 100) {
                $this->Gd->jpg_quality = 100;
            }

            $show_result = imagewebp($this->Gd->destination_image_object, null, $this->Gd->jpg_quality);
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
