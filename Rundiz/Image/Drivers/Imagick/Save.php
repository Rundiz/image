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
            // if save as JPG or WEBP.
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
        } elseif ($check_file_ext === 'avif') {
            // if save as AVIF.
            $this->ImagickD->Imagick->setCompressionQuality($this->ImagickD->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if save as PNG.
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
            ) {
                // if save as webp and maybe animated
                $WebP = new \Rundiz\Image\Extensions\WebP($this->ImagickD->source_image_path);
                if (!$WebP->isImagickSupportedAnimated()) {
                    $Ims = $this->ImagickD->getStatic();
                    $this->setErrorMessage('Current version of ImageMagick does not support animated WebP.', $Ims::RDIERROR_SRC_WEBP_ANIMATED_NOTSUPPORTED);
                    unset($Ims, $WebP);
                    return false;
                }// endif;
                unset($WebP);
            }
        } else {
            // if everything else.
            if ($this->ImagickD->source_image_frames > 1 && is_object($this->ImagickD->ImagickFirstFrame)) {
                // if source image is animated.
                $this->getFirstFrame();
            }

            if ($check_file_ext === 'jpg') {
                // if save as JPG.
                $this->fillWhiteToImage();
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
