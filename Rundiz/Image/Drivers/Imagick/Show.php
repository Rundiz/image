<?php
/**
 * @license http://opensource.org/licenses/MIT MIT
 */


namespace Rundiz\Image\Drivers\Imagick;


/**
 * Show image to browser.
 * 
 * @since 3.1.0
 */
class Show extends \Rundiz\Image\Drivers\AbstractImagickCommand
{


    use \Rundiz\Image\Drivers\Traits\ImagickTrait;


    /**
     * Execute the command.
     * 
     * @return bool
     */
    public function execute($file_ext = '')
    {
        if ($file_ext == null) {
            $file_ext = str_replace('.', '', $this->source_image_ext);
        }
        $file_ext = str_ireplace('jpeg', 'jpg', $file_ext);
        $file_ext = ltrim($file_ext, '.');

        $check_file_ext = strtolower($file_ext);

        // set compression. -----------------------------
        if ($check_file_ext === 'jpg' || $check_file_ext === 'webp') {
            // if show as JPG or WEBP.
            if ($check_file_ext === 'jpg') {
                $this->fillWhiteToImage();
            }
            $this->ImagickD->jpg_quality = intval($this->ImagickD->jpg_quality);
            if ($this->ImagickD->jpg_quality < 1 || $this->ImagickD->jpg_quality > 100) {
                $this->ImagickD->jpg_quality = 100;
            }
            $this->ImagickD->Imagick->setImageCompressionQuality($this->ImagickD->jpg_quality);
        } elseif ($check_file_ext === 'png') {
            // if show as PNG.
            $this->ImagickD->png_quality = intval($this->ImagickD->png_quality);
            if ($this->ImagickD->png_quality < 0 || $this->ImagickD->png_quality > 9) {
                $this->ImagickD->png_quality = 0;
            }
            $this->ImagickD->Imagick->setCompressionQuality(intval($this->ImagickD->png_quality . 5));
        }// endif;
        // end set compression. ------------------------

        // show image to browser.
        // http://php.net/manual/en/imagick.getimageblob.php for single frame of image or non-animated picture.
        // http://php.net/manual/en/imagick.getimagesblob.php for animated gif, webp.
        if (
            ($check_file_ext === 'gif' || $check_file_ext === 'webp') 
            && $this->ImagickD->save_animate_gif === true
        ) {
            // if show as gif or webp and allow to save (show) animated
            $this->ImagickD->Imagick->setFirstIterator();// Fix animated GIF show as animated WEBP but save as still be GIF.
            $this->ImagickD->Imagick->setImageFormat($check_file_ext);
            $show_result = $this->ImagickD->Imagick->getImagesBlob();

            if (
                $check_file_ext === 'webp' 
                && $this->ImagickD->source_image_frames > 1
            ) {
                // if show as webp and maybe animated
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
            $this->ImagickD->Imagick->setImageFormat($check_file_ext);
            $show_result = $this->ImagickD->Imagick->getImageBlob();
        }// endif;

        // clear
        unset($check_file_ext, $file_ext);

        if (isset($show_result) && $show_result !== false) {
            $this->setStatusSuccess();
            // Because in PHP GD it is automatically show the image content by omit the file name in `imagexxx()` function without echo command.
            // But in PHP Imagick must echo content that have got from getImageBlob() of Imagick class, then we have to echo it here to make this image class work in the same way.
            echo $show_result;
            return true;
        } else {
            $Ims = $this->ImagickD->getStatic();
            $this->setErrorMessage('Failed to show the image.', $Ims::RDIERROR_SHOW_FAILED);
            unset($Ims);
            return false;
        }
    }// execute


}
