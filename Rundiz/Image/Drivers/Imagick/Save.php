<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Save command class.
 * 
 * @since 3.1.0
 */
class Save extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Drivers\Traits\ImagickTrait;


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

        // set compression. -----------------------------
        if ($check_file_ext === 'jpg' || $check_file_ext === 'webp') {
            // if save to JPG or WEBP.
            if ($check_file_ext === 'jpg') {
                $this->fillWhiteToImage();
            }
            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1 || $this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if save to PNG.
            $this->ImagickD->png_quality = intval($this->ImagickD->png_quality);
            if ($this->ImagickD->png_quality < 0 || $this->ImagickD->png_quality > 9) {
                $this->ImagickD->png_quality = 0;
            }
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));
        }// endif;
        // end set compression. ------------------------

        if (
            ($check_file_ext === 'gif' || $check_file_ext === 'webp') 
            && $this->ImagickD->save_animate_gif === true
        ) {
            // if save as gif or webp and allow to save animated
            $save_result = $this->ImagickD->Imagick->writeImages($file_name, true);

            if (
                $check_file_ext === 'webp' 
                && $this->ImagickD->source_image_frames > 1
                && version_compare(PHP_VERSION, '7.3', '<')
            ) {
                // if save as webp but php does not met requirement.
                $Ims = $this->ImagickD->getStatic();
                $this->setErrorMessage('Current version of PHP and Imagick does not support animated WebP.', $Ims::RDIERROR_SRC_WEBP_ANIMATED_NOTSUPPORTED);
                unset($Ims);
                return false;
            }
        } else {
            // if everything else.
            if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                // if source image is animated.
                $this->getFirstFrame();
            }
            $save_result = $this->ImagickD->Imagick->writeImage($file_name);
        }// endif;

        // clear
        unset($check_file_ext);

        if (isset($save_result) && $save_result !== false) {
            $this->setStatusSuccess();
            return true;
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->setErrorMessage('Failed to save the image.', $Ims::RDIERROR_SAVE_FAILED);
            unset($Ims);
            return false;
        }
    }// execute


}
